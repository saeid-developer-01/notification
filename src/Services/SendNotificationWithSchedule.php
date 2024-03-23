<?php

namespace IICN\Notification\Services;

use IICN\Notification\Constants\Topics;
use IICN\Notification\SendNotification;
use IICN\Schedule\ScheduleBuilder;
use Illuminate\Support\Facades\Notification as NotificationSender;
use IICN\Notification\Resources\Notification as NotificationResource;
use Kreait\Firebase\Messaging\MessageTarget;

class SendNotificationWithSchedule extends ScheduleBuilder
{

    public function __construct(public string $conditions, public array $resources)
    {

    }

    public function run()
    {
        $notificationResource = NotificationResource::create(...$this->resources);

        $conditions = $this->addTimezoneToCondition($this->conditions);

        NotificationSender::route('fcm', $conditions)
            ->route('FCMTargetType', MessageTarget::CONDITION)
            ->notify(new SendNotification($notificationResource));
    }

    public function addTimezoneToCondition(string $conditions): string
    {
        $timezones = config('notification.timezone_model')::query()->where('tz', $this->timezone)->orWhere('time_offset', $this->timezone)->pluck('utc')->toArray();

        $condition = "";

        foreach (array_unique($timezones) as $key => $timezone) {
            if ($key === array_key_first($timezones)) {
                $condition .= "('" . Topics::PRE_TIMEZONE . "{$timezone}' in topics";
            } else {
                $condition .= " || '" . Topics::PRE_TIMEZONE . "{$timezone}' in topics";
            }

            if ($key === array_key_last($timezones)) {
                $condition .= ")";
            }
        }

        if ($conditions) {
            return $conditions . " && " . $condition;
        } else {
            return $condition;
        }
    }
}
