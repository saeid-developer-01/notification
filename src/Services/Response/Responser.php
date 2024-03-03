<?php

namespace IICN\Notification\Services\Response;

use IICN\Notification\NotificationResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Responser extends Response implements NotificationResponse
{
    public function success(?string $message = null, array $data = [], int $status = 200, array $headers = []): JsonResponse
    {
        $data = [
            'message' => $message,
            'data' => $data,
            'status' => true
        ];

        return response()->json($data, $status, $headers);
    }

    public function error(?string $message = null, ?array $data = [], int $status = 422, array $headers = []): JsonResponse
    {
        $data = [
            'message' => $message,
            'exception' => $data,
            'status' => false
        ];

        return response()->json($data, $status, $headers);
    }

    public function data(array $data, ?string $message = null, int $status = 200, array $headers = []): JsonResponse
    {
        $data = [
            'message' => $message,
            'data' => $data,
            'status' => true
        ];

        return response()->json($data, $status, $headers);
    }
}
