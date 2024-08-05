<?php

namespace IICN\Notification;

use IICN\Notification\Constants\Topics;
use IICN\Notification\Resources\FcmResource;
use Kreait\Firebase\Messaging\MessageTarget;

class NotificationSender
{
    public static function toTopics(FcmResource $notificationResource, string $conditions = "")
    {
        $hasContentTest = str_contains($conditions, "'" . config("notification.topic_names.test", Topics::TEST) . "' in topics");

        if (config("notification.default_send_to_test_topic", true) and !$hasContentTest) {
            $conditions = explode("&&", $conditions);
            $conditions[] = "'" . config("notification.topic_names.test", Topics::TEST) . "' in topics";
            $conditions = implode(" && ", $conditions);
            $conditions = str_replace("&&  ", "&& ", $conditions);
            $conditions = str_replace("  &&", " &&", $conditions);
        }

        \Illuminate\Support\Facades\Notification::route('fcm', $conditions)
            ->route('FCMTargetType', MessageTarget::CONDITION)
            ->notify(new SendNotification($notificationResource));
    }
}
