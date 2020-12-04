<?php

namespace App\Message;

class Sms
{
    private $smsEntityId;

    public function __construct(int $smsEntityId)
    {
        $this->smsEntityId = $smsEntityId;
    }

    public function getContent(): int
    {
        return $this->smsEntityId;
    }
}
