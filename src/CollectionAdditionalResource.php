<?php

namespace IICN\Notification;


interface CollectionAdditionalResource
{
    public static function make($classResources, ...$parameters);

    public static function collection($classResources, $resource);
}
