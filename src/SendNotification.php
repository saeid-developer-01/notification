<?php

namespace IICN\Notification;

use IICN\Notification\Messages\FcmMessage;
use IICN\Notification\Resources\FcmResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SendNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public FcmResource $resource)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->data($this->resource->toArray())
            ->custom([
                'notification' => [],
                'android' => [
                    "priority"=> "high",
                    "ttl"=> "86400s",
                ],
                'apns' => [
                    "headers" => [
                        "apns-priority"=> "5",
                    ],
                    "payload" => [
                        "aps" => [
                            "content-available" => 1,
                        ]
                    ]
                ]
            ]);
    }
}
