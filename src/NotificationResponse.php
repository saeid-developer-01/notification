<?php

namespace IICN\Notification;


interface NotificationResponse
{
    public function success(string $message, array $data, int $status, array $headers);

    public function error(string $message, array $exception, int $status, array $headers);

    public function data(array $data, string $message, int $status, array $headers);
}
