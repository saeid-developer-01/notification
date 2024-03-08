<?php

namespace IICN\Notification\Http\Controllers\Notification;

use IICN\Notification\Http\Controllers\Controller;
use IICN\Notification\Http\Requests\StoreNotificationRequest;
use IICN\Notification\Http\Resources\NotificationResource;
use IICN\Notification\Models\Notification;

class Index extends Controller
{
    public function __invoke(StoreNotificationRequest $request)
    {
        $notifications = Notification::query()->paginate();

        return NotificationResource::collection($notifications);
    }
}
