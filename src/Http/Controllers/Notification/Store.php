<?php

namespace IICN\Notification\Http\Controllers\Notification;

use IICN\Notification\Http\Controllers\Controller;
use IICN\Notification\Http\Requests\StoreNotificationRequest;
use IICN\Notification\Http\Resources\NotificationResource;
use IICN\Notification\Models\Notification;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class Store extends Controller
{
    public function __invoke(StoreNotificationRequest $request): AnonymousResourceCollection
    {
        $data = [
            'link' => $request->validated('link'),
            'action' => $request->validated('action'),
            'ayeh' => $request->validated('ayeh'),
            'name' => $request->validated('name'),
        ];

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

        return NotificationResource::collection($notification);
    }
}
