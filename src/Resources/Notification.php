<?php

namespace IICN\Notification\Resources;

class Notification extends FcmResource
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ?string $title = "",
        public ?string $body = "",
        public ?string $image = "",
        public ?string $action = "",
        public ?string $name = "",
        public ?string $link = "",
        public ?string $ayeh = "",
    ) {
        //
    }

    /**
     * Set the notification title.
     */
    public function title(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the notification body.
     */
    public function body(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set the notification image.
     */
    public function image(?string $image): self
    {
        $this->image = $image;

        return $this;
    }


    /**
     * Set the notification action.
     */
    public function action(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set the notification name.
     */
    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the notification link.
     */
    public function link(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Set the notification link.
     */
    public function ayeh(?string $ayeh): self
    {
        $this->ayeh = $ayeh;

        return $this;
    }

    /**
     * Map the resource to an array.
     */
    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'body' => $this->body,
            'pic' => $this->image,
            'action' => $this->action,
            'name' => $this->name,
            'link' => $this->link,
            'ayeh' => $this->ayeh,
        ]);
    }
}
