<?php

namespace IICN\Notification\Services\Notification\FCM;

use IICN\Notification\Services\Notification\ResponseNotification;

class FCMNotificationResponse extends ResponseNotification
{
    public function __construct(
        public string $title = "",
        public string $body = "",
        public string $pic = "",
        public string $action = "",
        public string $name = "",
        public string $link = "",
        public string $ayeh = "",
    )
    {

    }
}
