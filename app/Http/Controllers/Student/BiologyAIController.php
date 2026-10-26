<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiologyAIController extends Controller
{
    public function index()
    {
        return view('student.biology_ai');
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        $question = $request->input('question');
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return response()->json(['error' => 'API Key not configured.'], 500);
        }

        $systemInstruction = "You are the official 'Biology AI Assistant' for the Lakmal Chamara Biology LMS. 
        You were specially designed for students of Mr. Lakmal Chamara to get quick help. 
        Your goal is to provide Biology explanations that align with the Sri Lankan A/L syllabus.

        Strict Rules:
        1. Answer only Biology-related questions.
        2. If the question is NOT about Biology, respond in Sinhala: 'මම ලක්මාල් චාමර සර්ගේ ජීව විද්‍යාව පන්තියේ සිසුන් සඳහා ජීව විද්‍යාව විෂයට අදාළ ප්‍රශ්න වලට පමණක් පිළිතුරු ලබා දෙමි.'
        3. **IMPORTANT:** At the end of EVERY answer, you MUST add this exact disclaimer in Sinhala:
           '\n\n(සටහන: මෙය AI සහායකයක් පමණක් බැවින්, පිළිතුරෙහි නිරවද්‍යතාවය පිළිබඳව ගැටළුවක් ඇත්නම් පන්තියේදී සර් ගෙන් විමසා තහවුරු කරගන්න.)'";

        try {
            // ✅ වෙනස් කළ කොටස: 'gemini-flash-latest' භාවිතා කිරීම.
            // මෙය ඔයාගේ ලිස්ට් එකේ තිබුණා. මෙය තමයි Standard High-Limit Model එක.
            $response = Http::withoutVerifying() 
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $systemInstruction . "\n\nUser Question: " . $request->question]
                            ]
                        ]
                    ]
                ]);

            $data = $response->json();
            
            // --- Error Handling ---
            
            // 1. Limit Exceeded (429)
            if ($response->status() == 429) {
                return response()->json([
                    'answer' => 'සමාවන්න, අද දිනට නියමිත ප්‍රශ්න ප්‍රමාණය අවසන් වී ඇත. කරුණාකර හෙට දින නැවත උත්සාහ කරන්න.'
                ]);
            }

            // 2. API Errors
            if(isset($data['error'])) {
                if (str_contains($data['error']['message'], 'Resource has been exhausted') || $data['error']['code'] == 429) {
                    return response()->json([
                        'answer' => 'සමාවන්න, අද දිනට නියමිත ප්‍රශ්න ප්‍රමාණය අවසන් වී ඇත. කරුණාකර හෙට දින නැවත උත්සාහ කරන්න.'
                    ]);
                }
                
                return response()->json(['answer' => 'තාක්ෂණික දෝෂයක් (API Error): ' . $data['error']['message']]);
            }

            $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No Answer received';
            return response()->json(['answer' => $answer]);

        } catch (\Exception $e) {
            return response()->json([
                'answer' => 'සමාවන්න, පද්ධතියේ දෝෂයක් සිදු විය. කරුණාකර ටික වේලාවකින් උත්සාහ කරන්න.'
            ], 500);
        }
    }
}