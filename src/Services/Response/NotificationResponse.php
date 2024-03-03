<?php
namespace IICN\Notification\Services\Response;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \IICN\Notification\Services\Response\Responser success(?string $message = null, array $data = [], int $status = 200, array $headers = [])
 * @method static \IICN\Notification\Services\Response\Responser error(?string $message = null, ?string $exception = null, int $status = 422, array $headers = [])
 * @method static \IICN\Notification\Services\Response\Responser data(array $data, ?string $message = null, int $status = 200, array $headers = [])
 *
 * @see \IICN\Notification\Services\Response\Responser
 */
class NotificationResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'notificationResponse';
    }
}
