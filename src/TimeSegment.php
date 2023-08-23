<?php

namespace Karrakoliko\SeanceReservation;

class TimeSegment implements TimeSegmentInterface
{
    private readonly \DateTimeImmutable $startTime;
    private readonly int $durationSec;

    private readonly \DateTimeImmutable $endTime;

    public function __construct(\DateTimeImmutable $startTime, int $durationSec)
    {

        if($durationSec <0){
            throw new \InvalidArgumentException("durationSec must be positive integer number");
        }

        $this->startTime = $startTime;
        $this->durationSec = $durationSec;
        $this->endTime = $this->calcEndTime();
    }

    public function getDurationSec(): int
    {
        return $this->durationSec;
    }

    public function getStartTimeStamp(): int
    {
        return $this->startTime->getTimestamp();
    }

    protected function calcEndTime(): \DateTimeImmutable {

        $dateTime = (new \DateTime())->setTimestamp($this->getStartTimeStamp() + $this->durationSec);
        return \DateTimeImmutable::createFromMutable($dateTime);
    }

    public function getEndTimeStamp(): int
    {
        return $this->endTime->getTimestamp();
    }
}