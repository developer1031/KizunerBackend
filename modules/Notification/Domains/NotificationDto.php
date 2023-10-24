<?php

namespace Modules\Notification\Domains;

class NotificationDto
{
    public  $userId;
    public  $title;
    public  $body;
    public  $payload;
    public  $type;
    public  $uploadableId;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return self
     */
    public function setUserId($userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return self
     */
    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return self
     */
    public function setBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param mixed $payload
     * @return self
     */
    public function setPayload($payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return self
     */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUploadableId()
    {
        return $this->uploadableId;
    }

    /**
     * @param mixed $uploadableId
     * @return self
     */
    public function setUploadableId($uploadableId): self
    {
        $this->uploadableId = $uploadableId;
        return $this;
    }
}
