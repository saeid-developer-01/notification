<?php

namespace IICN\Notification\Http\Resources;

use IICN\Notification\CollectionAdditionalResource;

trait AdditionalTrait
{
    public static function make(...$parameters) {
        $selectedTrait = config('notification.additional_resource_class');

        if (class_exists($selectedTrait) and app($selectedTrait) instanceof CollectionAdditionalResource) {
            return $selectedTrait::make(parent::make(...$parameters), ...$parameters);
        }
    }

    public static function collection($resource) {
        $selectedTrait = config('notification.additional_resource_class');

        if (class_exists($selectedTrait) and app($selectedTrait) instanceof CollectionAdditionalResource) {
            return $selectedTrait::collection(parent::collection($resource), $resource);
        }
    }
}
