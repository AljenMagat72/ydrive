<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

class LogChannel
{
  public function send($notifiable, Notification $notification)
  {
    $data = $notification->toArray($notifiable);
    
    Log::info('Notification', [
      'notification' => get_class($notification),
      'data' => $data,
    ]);
    
    event(new NotificationSent($notifiable, $notification, 'log', $data));
    
    return $data;
  }
}