<?php

namespace IICN\Notification\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    use AdditionalTrait;

    public function toArray($request)
    {
        return [
            'id' => $this->id ?? 0,
            'name' => $this->name ?? '',
            'topic' => $this->topic ?? '',
            'condition' => $this->condition,
            'data' => $this->data ?? [],
            'model_id' => $this->model_id ?? 0,
            'model_type' => $this->model_type ?? '',
            'timezone' => $this->timezone ?? '',
            'send_date' => $this->send_date ?? '',
        ];
    }
}
