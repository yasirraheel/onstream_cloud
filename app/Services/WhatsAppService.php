<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('WA_API_URL', 'https://wa-server.shahabtech.com/api/v1/send-message');
        $this->apiKey = env('WA_API_KEY');

        $this->client = new Client([
            'timeout' => 30,
            'verify' => false, // For local development, set to true in production
        ]);
    }

    /**
     * Send a WhatsApp message
     *
     * @param string $number Phone number with country code
     * @param string $message Message content
     * @param string|null $accountName Account name (optional)
     * @param string|null $sessionId Session ID (optional)
     * @return array Response with success status and message
     */
    public function sendMessage($number, $message, $accountName = null, $sessionId = null)
    {
        try {
            // Prepare request body
            $body = [
                'number' => $number,
                'message' => $message,
            ];

            // Add either account_name or session_id
            if ($accountName) {
                $body['account_name'] = $accountName;
            } elseif ($sessionId) {
                $body['session_id'] = $sessionId;
            } else {
                // Default to account_name from env if neither provided
                $body['account_name'] = env('WA_ACCOUNT_NAME', 'OnStream');
            }

            // Send request
            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Api-Key' => $this->apiKey,
                ],
                'json' => $body,
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody(), true);

            if ($statusCode == 200) {
                Log::info('WhatsApp message sent successfully', [
                    'number' => $number,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'data' => $responseData
                ];
            } else {
                Log::warning('WhatsApp API returned non-200 status', [
                    'status' => $statusCode,
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send message',
                    'data' => $responseData
                ];
            }

        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();
            $responseData = null;

            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);
                
                // Try to find a meaningful error message
                if (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                } elseif (isset($responseData['body'])) {
                     // Check if body is JSON string
                     $bodyJson = json_decode($responseData['body'], true);
                     if (json_last_error() === JSON_ERROR_NONE && isset($bodyJson['error'])) {
                         $errorMessage = $bodyJson['error'];
                     }
                } elseif (isset($responseData['error'])) {
                    $errorMessage = $responseData['error'];
                }
            }

            Log::error('WhatsApp API request failed', [
                'number' => $number,
                'error' => $errorMessage
            ]);

            return [
                'success' => false,
                'message' => $errorMessage,
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'number' => $number,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Check if API key is configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->apiKey) && !empty($this->apiUrl);
    }
}
