<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Tute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::withCount('students')->latest()->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($request->title) . '-' . uniqid();
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        Course::create($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['tutes', 'notices', 'exams']);
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Delete thumbnail
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
        
        $course->delete(); // Cascades delete related items due to DB constraints (if set) or Model logic

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }

    /**
     * Store a Tute for the Course (Sub-resource)
     */
    public function storeTute(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:file,live_class,recording_class', // Updated types
            'file_path' => 'nullable|file|max:10240', // Optional attachment for all
            'video_url' => 'nullable|required_if:type,live_class,recording_class|url', // Required for live/recording
            'is_published' => 'boolean'
        ]);

        $data = [
            'course_id' => $course->id,
            'title' => $request->title,
            'type' => $request->type,
            'video_url' => $request->video_url,
            'is_published' => $request->has('is_published'),
            'order' => $course->tutes()->count() + 1
        ];

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('tutes', 'public');
            $data['file_path'] = $path;
        }

        Tute::create($data);

        return redirect()->route('admin.courses.show', $course)->with('success', 'Material added successfully.');
    }

    public function destroyTute(Tute $tute)
    {
        if ($tute->file_path) {
            Storage::disk('public')->delete($tute->file_path);
        }
        $tute->delete();
        return back()->with('success', 'Material deleted.');
    }
}
