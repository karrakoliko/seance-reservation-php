<?php

namespace Karrakoliko\SeanceReservation\Slot;

use DateTimeImmutable;

readonly class Slot
{

    public DateTimeImmutable $dateTime;

    public function __construct(DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public static function createFromTimeStamp(int $timestamp): self
    {
        return new self(new DateTimeImmutable('@' . $timestamp));
    }

    public function getDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

}