<?php

namespace IICN\Notification;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\MessageTarget;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\SendReport;

class FcmChannel
{
    /**
     * The maximum number of tokens we can use in a single request
     *
     * @var int
     */
    const TOKENS_PER_REQUEST = 500;

    /**
     * Create a new channel instance.
     */
    public function __construct(protected Dispatcher $events, protected Messaging $client)
    {
        //
    }

    /**
     * Send the given notification.
     */
    public function send(mixed $notifiable, Notification $notification): Collection|array|null
    {
        $tokens = Arr::wrap($notifiable->routeNotificationFor('fcm', $notification));

        if (empty($tokens)) {
            return null;
        }

        $fcmMessage = $notification->toFcm($notifiable);

        if (isset($notifiable->routes['fcm']) && isset($notifiable->routes['FCMTargetType'])) {
            if ($notifiable->routes['FCMTargetType'] === MessageTarget::CONDITION) {
                $fcmMessage->condition = $notifiable->routes['fcm'];
                $target = MessageTarget::with(MessageTarget::CONDITION, $fcmMessage->condition);
            } elseif ($notifiable->routes['FCMTargetType'] === MessageTarget::TOPIC) {
                $fcmMessage->topic = $notifiable->routes['fcm'];
                $target = MessageTarget::with(MessageTarget::TOPIC, $fcmMessage->topic);
            }
        }

        if ($fcmMessage->condition or $fcmMessage->topic) {
            try {
                return $this->client->send($fcmMessage);
            } catch (\Throwable $exception) {
                $report = SendReport::failure($target, $exception, $fcmMessage);
                $this->dispatchFailedNotification($notifiable, $notification, $report);
                return null;
            }
        } else {
            return Collection::make($tokens)
                ->chunk(self::TOKENS_PER_REQUEST)
                ->map(fn ($tokens) => ($fcmMessage->client ?? $this->client)->sendMulticast($fcmMessage, $tokens->all()))
                ->map(fn (MulticastSendReport $report) => $this->checkMulticastReportForFailures($notifiable, $notification, $report));
        }
    }

    /**
     * Handle the report for the notification and dispatch any failed notifications.
     */
    protected function checkReportForFailures(mixed $notifiable, Notification $notification, SendReport $report): SendReport
    {
        Collection::make($report->getItems())
            ->filter(fn (SendReport $report) => $report->isFailure())
            ->each(fn (SendReport $report) => $this->dispatchFailedNotification($notifiable, $notification, $report));

        return $report;
    }

    /**
     * Handle the report for the notification and dispatch any failed notifications.
     */
    protected function checkMulticastReportForFailures(mixed $notifiable, Notification $notification, MulticastSendReport $report): MulticastSendReport
    {
        Collection::make($report->getItems())
            ->filter(fn (SendReport $report) => $report->isFailure())
            ->each(fn (SendReport $report) => $this->dispatchFailedNotification($notifiable, $notification, $report));

        return $report;
    }

    /**
     * Dispatch failed event.
     */
    protected function dispatchFailedNotification(mixed $notifiable, Notification $notification, SendReport $report): void
    {
        $this->events->dispatch(new NotificationFailed($notifiable, $notification, self::class, [
            'report' => $report,
        ]));
    }
}
