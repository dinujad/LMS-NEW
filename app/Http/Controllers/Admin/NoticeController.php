<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::with('course')->latest()->paginate(10);
        return view('admin.notices.index', compact('notices'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('admin.notices.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'expires_at' => 'nullable|date',
            'is_published' => 'boolean'
        ]);

        $validated['is_published'] = $request->has('is_published');
        
        Notice::create($validated);

        return redirect()->route('admin.notices.index')->with('success', 'Notice published.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }
}
