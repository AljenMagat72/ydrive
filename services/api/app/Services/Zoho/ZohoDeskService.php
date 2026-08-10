<?php

namespace App\Services\Zoho;

use Illuminate\Support\Facades\Http;

/**
 * Portfolio mock: lightweight Zoho Desk ticket helper.
 */
class ZohoDeskService
{
    protected function http()
    {
        return Http::withToken('mock-zoho-desk-access-token')
            ->acceptJson()
            ->baseUrl(config('services.zoho.desk.base_url', 'https://desk.zoho.com/api/v1'));
    }

    public function createTicket(array $payload): array
    {
        $body = [
            'subject' => $payload['subject'] ?? 'YDrive Support Request',
            'description' => $payload['description'] ?? '',
            'departmentId' => config('services.zoho.desk.department_id'),
            'contact' => [
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'lastName' => $payload['last_name'] ?? 'Rider',
            ],
            'channel' => 'YDrive App',
            'priority' => $payload['priority'] ?? 'Medium',
        ];

        // Mock-friendly: return a synthetic ticket when credentials are empty.
        if (!config('services.zoho.desk.department_id')) {
            return [
                'id' => 'mock-ticket-' . substr(md5(json_encode($body)), 0, 10),
                'subject' => $body['subject'],
                'status' => 'Open',
            ];
        }

        $response = $this->http()->post('/tickets', $body);

        return $response->json() ?? [];
    }

    public function addComment(string $ticketId, string $content): array
    {
        if (str_starts_with($ticketId, 'mock-ticket-')) {
            return [
                'id' => 'mock-comment-' . substr(md5($ticketId . $content), 0, 8),
                'ticketId' => $ticketId,
                'content' => $content,
            ];
        }

        $response = $this->http()->post("/tickets/{$ticketId}/comments", [
            'isPublic' => true,
            'content' => $content,
        ]);

        return $response->json() ?? [];
    }
}
