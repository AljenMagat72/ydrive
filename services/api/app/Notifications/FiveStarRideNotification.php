<?php

namespace App\Notifications;

use App\Channels\LogChannel;
use Illuminate\Bus\Queueable;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;
use Illuminate\Notifications\Notification;

class FiveStarRideNotification extends Notification
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct()
  {
    //
  }

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    return [TwilioChannel::class, LogChannel::class];
  }

  public function toTwilio(object $notifiable): TwilioMessage
  {
    return (new TwilioSmsMessage)
      ->from(config('twilio-notification-channel.user_notification_number'))
      ->content(__('client_notifications.five_star_review'));
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    $phoneNumber = $notifiable->routeNotificationFor('twilio');

    return [
      'phone_number' => $phoneNumber,
      'message' => __('client_notifications.five_star_review'),
      'notification_type' => 'ride_review',
    ];
  }
}
