<?php

namespace IICN\Notification\Http\Controllers\Notification;

use IICN\Notification\Constants\Topics;
use IICN\Notification\Http\Controllers\Controller;
use IICN\Notification\Http\Requests\StoreNotificationRequest;
use IICN\Notification\Models\Notification;
use IICN\Notification\Resources\FcmResource;
use IICN\Notification\SendNotification;
use IICN\Notification\Services\Response\NotificationResponse;
use IICN\Notification\Services\SendNotificationWithSchedule;
use IICN\Notification\Resources\Notification as NotificationResource;
use Illuminate\Support\Facades\Notification as NotificationSender;
use IICN\Schedule\TaskScheduler;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Collection;
use Kreait\Firebase\Messaging\MessageTarget;

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
            $resources = [
                $notification->title,
                $notification->content,
                $notification->image_url,
                $notification->data['action'] ?? "",
                $notification->data['name'] ?? "",
                $notification->data['link'] ?? "",
                $notification->data['ayeh'] ?? "",
            ];

            $notificationResource = NotificationResource::create(...$resources);

            if ($notification->users()->count() > 0) {
                foreach ($notification->users as $user) {
                    $user->notify(new SendNotification($notificationResource));
                }
            } elseif($notification->send_date) {
                $this->sendWithSchedule($resources, $notification);
            } else {
                $this->sendNowWithCondition($notificationResource, $notification->condition);
            }
        });
    }

    private function sendNowWithCondition(FcmResource $notificationResource, string $conditions)
    {
        NotificationSender::route('fcm', $conditions)
            ->route('FCMTargetType', MessageTarget::CONDITION)
            ->notify(new SendNotification($notificationResource));
    }

    private function sendWithSchedule(array $resources, Notification $notification)
    {
        TaskScheduler::do(SendNotificationWithSchedule::class, [$notification->condition, $resources])->at($notification->send_date);
    }

    private function createCollectionNotifications($request): Collection
    {
        if ($request->validated('special_users') and count($request->validated('users'))) {
            $users = config('notification.user_model')::whereIn('email', $request->validated('users'))->get();
        } else {
            $conditions = $this->createCondition($request);
        }

        $data = [
            'link' => $request->validated('link'),
            'action' => $request->validated('action'),
            'ayeh' => $request->validated('ayeh'),
            'name' => $request->validated('name'),
            'emails' => $request->validated('users'),
        ];

        $notification = Notification::query()->create([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'image_url' => $request->validated('image_url'),
            'condition' => $conditions ?? null,
            'data' => $data,
            'user_id' => Auth::guard(config('notification.guard'))->id(),
            'send_date' => $request->validated('send_date'),
        ]);

        if (isset($users)) {
            $notification->users()->attach($users->pluck('id')->toArray());
        }

        return collect([$notification]);
    }

    private function createCondition($request): string
    {
        $conditions  = [];

        if ($request->validated('test')) {
            $conditions[] = "'" . Topics::TEST . "' in topics";
        }

        if ($request->validated('platform') === 'android') {
            $conditions[] = "'" . Topics::ANDROID_PLATFORM . "' in topics";
        } elseif ($request->validated('platform') === 'ios') {
            $conditions[] = "'" . Topics::IOS_PLATFORM . "' in topics";
        }

        if ($request->validated('platform') and $request->validated('version')) {
            $conditions[] = "'" . Topics::PRE_VERSION . "{$request->validated('version')}' in topics";
        }

        if ($languages = $request->validated('languages')) {
            $languageCondition = $this->multiCondition($languages, Topics::PRE_LANGUAGE);

            if ($languageCondition) {
                $conditions[] = $languageCondition;
            }
        }

        if ($destinations = $request->validated('destinations')) {
            $destinationCondition = $this->multiCondition($destinations, Topics::PRE_DESTINATION);

            if ($destinationCondition) {
                $conditions[] = $destinationCondition;
            }
        }

        return implode(' && ', $conditions);
    }

    protected function multiCondition(array $items, string $preTopic = ""): string|null
    {
        $condition = "";

        foreach ($items as $key => $item) {
            if ($key === array_key_first($items)) {
                $condition .= "('{$preTopic}{$item}' in topics";
            } else {
                $condition .= " || '{$preTopic}{$item}' in topics";
            }

            if ($key === array_key_last($items)) {
                $condition .= ")";
            }
        }

        return $condition ?: null;
    }
}
