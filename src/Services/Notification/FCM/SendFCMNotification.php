<?php

namespace IICN\Notification\Services\Notification\FCM;

use Modules\Core\Services\Notification\ResponseNotification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class SendFCMNotification
{
    private string $condition = "";

    private string $topic = "";

    public function __construct()
    {

    }

    public function setCondition(string $condition)
    {
        $this->condition = $condition;

        return $this;
    }

    public function setTopic(string $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    public function send(ResponseNotification $notificationResponse)
    {
        $fcm = (new FcmMessage(notification: new FcmNotification()));

        if (!empty($this->condition)) {
            $fcm->condition($this->condition);
        }

        if (!empty($this->topic)) {
            $fcm->topic($this->topic);
        }

        return $fcm->data($notificationResponse->response());
    }
}
