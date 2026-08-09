<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class LoginCodeNotification extends Notification
{
  use Queueable;

  private string $code;

  /**
   * Create a new notification instance.
   */
  public function __construct(string $code)
  {
    $this->code = $code;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    return [TwilioChannel::class];
  }

  public function toTwilio(object $notifiable): TwilioMessage
  {
    return (new TwilioSmsMessage)
    ->from(config('twilio-notification-channel.driver_notification_number'))
    ->content("Your verification code is: $this->code");
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    return [
      "phone_number" => $notifiable->phone_number,
      "code" => $this->code
    ];
  }
}
