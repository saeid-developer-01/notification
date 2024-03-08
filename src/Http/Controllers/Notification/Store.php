<?php

namespace IICN\Notification\Http\Controllers\Notification;

use IICN\Notification\Http\Controllers\Controller;
use IICN\Notification\Http\Requests\StoreNotificationRequest;
use IICN\Notification\Models\Notification;
use IICN\Notification\SendNotification;
use IICN\Notification\Services\Response\NotificationResponse;
use Illuminate\Support\Facades\Auth;

class Store extends Controller
{
    public function __invoke(StoreNotificationRequest $request)
    {
        $notifications = $this->createCollectionNotifications($request);

        $this->sendNotifications($notifications);

        return NotificationResponse::success(trans('notification::messages.send_notification_success'));
    }

    private function sendNotifications($notifications)
    {
        $notifications->map(function (Notification $notification) {
            $notificationResource = \IICN\Notification\Resources\Notification::create(
                title: $notification->title,
                body: $notification->content,
                image: $notification->image_url,
                action: $notification->data['action'] ?? "",
                name: $notification->data['name'] ?? "",
                link: $notification->data['link'] ?? "",
                ayeh: $notification->data['ayeh'] ?? "",
            );

            if (!$notification->send_date) {
                \Illuminate\Support\Facades\Notification::route('fcm', $notification->condition)
                    ->route('FCMTargetType', \Kreait\Firebase\Messaging\MessageTarget::CONDITION)
                    ->notify(new SendNotification($notificationResource));
            }
        });
    }


    private function createCollectionNotifications($request)
    {
        $data = [
            'link' => $request->validated('link'),
            'action' => $request->validated('action'),
            'ayeh' => $request->validated('ayeh'),
            'name' => $request->validated('name'),
        ];


        //TODO:: apply users and timezone

        $conditions = "'tz_test' in topic";
        if ($request->validated('platform') === 'android') {
            $conditions .= " && 'os_1' in topics";
        } elseif ($request->validated('platform') === 'ios') {
            $conditions .= " && 'os_2' in topics";
        }

        if ($request->validated('platform') and $request->validated('version')) {
            $conditions .= " && 'vc_{$request->validated('version')}' in topics";
        }

        if ($languages = $request->validated('languages')) {
            foreach ($languages as $key => $lang) {
                if ($key === array_key_first($languages)) {
                    $conditions .= " && ('lang_{$lang}' in topics";
                } else {
                    $conditions .= " || 'lang_{$lang}' in topics";
                }

                if ($key === array_key_last($languages)) {
                    $conditions .= ")";
                }
            }
        }

        if ($destinations = $request->validated('destinations')) {
            foreach ($destinations as $key => $country) {
                if ($key === array_key_first($destinations)) {
                    $conditions .= " && ('c{$country}' in topics";
                } else {
                    $conditions .= " || 'c{$country}' in topics";
                }

                if ($key === array_key_last($destinations)) {
                    $conditions .= ")";
                }
            }
        }

        $notification = Notification::query()->create([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'image_url' => $request->validated('image_url'),
            'condition' => $conditions,
            'data' => $data,
            'user_id' => Auth::guard(config('notification.guard'))->id(),
            'send_date' => $request->validated('send_date'),
        ]);

        return collect([$notification]);
    }
}
