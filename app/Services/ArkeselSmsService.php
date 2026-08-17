<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArkeselSmsService
{
    protected $apiKey;
    protected $senderId;
    protected $baseUrl = 'https://sms.arkesel.com/api/v1/sms/send';

    public function __construct()
    {
        $this->apiKey = config('services.arkesel.api_key');
        $this->senderId = config('services.arkesel.sender_id', 'TASKMGR');
    }

    /**
     * Send SMS to a single recipient
     */
    public function sendSms(string $phoneNumber, string $message): bool
    {
        try {
            // Format phone number (remove + if present and ensure proper format)
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'sender' => $this->senderId,
                'message' => $message,
                'phone_numbers' => [$phoneNumber],
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'message' => $message,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('SMS failed to send', [
                'phone' => $phoneNumber,
                'message' => $message,
                'error' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'phone' => $phoneNumber,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send SMS to multiple recipients
     */
    public function sendBulkSms(array $phoneNumbers, string $message): array
    {
        $results = [];

        foreach ($phoneNumbers as $phoneNumber) {
            $results[$phoneNumber] = $this->sendSms($phoneNumber, $message);
        }

        return $results;
    }

    /**
     * Format phone number for Ghana/Africa
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If starts with 0, replace with 233 (Ghana country code)
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = '233' . substr($phoneNumber, 1);
        }

        // If doesn't start with country code, add 233
        if (!str_starts_with($phoneNumber, '233')) {
            $phoneNumber = '233' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Check account balance
     */
    public function getBalance(): ?float
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
            ])->get('https://sms.arkesel.com/api/v1/sms/balance');

            if ($response->successful()) {
                return $response->json()['balance'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get SMS balance', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
