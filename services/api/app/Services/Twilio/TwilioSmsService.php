<?php

namespace App\Services\Twilio;

use App\Http\Integrations\Twilio\TwilioApi;

/**
 * Portfolio mock: direct Twilio REST SMS helper (alongside notification channel).
 */
class TwilioSmsService
{
    public function __construct(protected TwilioApi $twilioApi)
    {
    }

    public function enabled(): bool
    {
        return (bool) config('services.twilio.enabled', false);
    }

    public function send(string $to, string $body, ?string $from = null): array
    {
        if (!$this->enabled()) {
            return [
                'sid' => 'SM_mock_' . substr(md5($to . $body), 0, 12),
                'status' => 'mock-skipped',
                'to' => $to,
                'body' => $body,
            ];
        }

        $payload = [
            'To' => $this->resolveTo($to),
            'Body' => $body,
        ];

        $messagingServiceSid = config('services.twilio.sms_service_sid');

        if ($messagingServiceSid) {
            $payload['MessagingServiceSid'] = $messagingServiceSid;
        } else {
            $payload['From'] = $from ?: config('services.twilio.from')
                ?: config('services.twilio.user_notification_number');
        }

        $response = $this->twilioApi->messages()->send($payload);

        return $response->json() ?? [];
    }

    public function notifyUser(string $to, string $body): array
    {
        return $this->send(
            $to,
            $body,
            config('services.twilio.user_notification_number')
        );
    }

    public function notifyDriver(string $to, string $body): array
    {
        return $this->send(
            $to,
            $body,
            config('services.twilio.driver_notification_number')
        );
    }

    protected function resolveTo(string $to): string
    {
        $debugTo = config('services.twilio.debug_to');

        return $debugTo ?: $to;
    }
}
