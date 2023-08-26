<?php

namespace Karrakoliko\SeanceReservation\Slot;

readonly class Slot
{

    public \DateTimeImmutable $dateTime;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public static function createFromTimeStamp(int $timestamp): self
    {
        $dateTime = (new \DateTime())->setTimestamp($timestamp);
        return new self(\DateTimeImmutable::createFromMutable($dateTime));
    }

}