<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $senderId;
    protected $url;

    public function __construct()
    {
        $this->apiKey = env('TEXTLK_API_KEY');
        $this->senderId = env('TEXTLK_SENDER_ID', 'Team Emedia');
        $this->url = env('TEXTLK_URL', 'https://app.text.lk/api/v3/sms/send');
    }

    public function send($mobile, $message)
    {
        try {
            // Format number to 947... if starts with 07
            if (substr($mobile, 0, 2) === '07') {
                $mobile = '94' . substr($mobile, 1);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->url, [
                'recipient' => $mobile,
                'sender_id' => $this->senderId,
                'message' => $message,
                'type' => 'plain',
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('SMS Failed: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS Exception: ' . $e->getMessage());
            return false;
        }
    }
}
