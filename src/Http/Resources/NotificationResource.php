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
            'title' => $this->title ?? "",
            'condition' => $this->condition ?? "",
            'image_url' => $this->image_url ?? "",
            'user_id' => $this->user_id ?? 0,
            'data' => $this->data ?? [],
            'model_id' => $this->model_id ?? 0,
            'model_type' => $this->model_type ?? "",
            'timezone' => $this->timezone ?? '',
            'send_date' => $this->send_date ?? '',
        ];
    }
}
