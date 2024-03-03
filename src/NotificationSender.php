<?php
namespace IICN\Notification;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \IICN\Notification\Services\NotificationSender create(int $subscription_id, ?int $durationDay = null, ?array $remainingNumber = null)
 *
 * @see \IICN\Notification\Services\NotificationSender
 */
class NotificationSender extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'notificationSender';
    }
}
