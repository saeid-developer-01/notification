<?php

namespace IICN\Notification\Services\Notification;

class ResponseNotification
{
    /**
     * Return contructor parameters array
     * @return array
     */
    public function getParameters()
    {
        $ref = new \ReflectionClass($this);

        if (! $ref->isInstantiable()) {
            return [];
        }
        else {
            $constructor = $ref->getConstructor();
            return $constructor->getParameters();
        }
    }


    public function response()
    {
        $data = [];

        foreach ($this->getParameters() as $reflectionParameter) {
            $data[$reflectionParameter->name] = $this->{$reflectionParameter->name};
        }

        return $data;
    }
}
