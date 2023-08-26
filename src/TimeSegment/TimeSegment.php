<?php

namespace Karrakoliko\SeanceReservation\TimeSegment;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

class TimeSegment implements TimeSegmentInterface
{
    private readonly DateTimeImmutable $startTime;
    private readonly int $durationSec;

    private readonly DateTimeImmutable $endTime;

    public function __construct(DateTimeImmutable $startTime, int $durationSec)
    {

        if ($durationSec < 0) {
            throw new InvalidArgumentException("durationSec must be positive integer number");
        }

        $this->startTime = $startTime;
        $this->durationSec = $durationSec;
        $this->endTime = $this->calcEndTime();
    }

    protected function calcEndTime(): DateTimeImmutable
    {

        $dateTime = (new DateTime())->setTimestamp($this->getStartTimeStamp() + $this->durationSec);
        return DateTimeImmutable::createFromMutable($dateTime);
    }

    public function getStartTimeStamp(): int
    {
        return $this->startTime->getTimestamp();
    }

    public static function createWithTime(DateTimeImmutable $date, int $hours, int $minutes, int $durationSec)
    {
        $dt = DateTime::createFromImmutable($date);
        $dt->setTime($hours, $minutes);

        return new self(DateTimeImmutable::createFromMutable($dt), $durationSec);

    }

    public static function createWithHoursFromTo(DateTimeImmutable $date, int $fromHours, int $toHours)
    {
        $dt = DateTime::createFromImmutable($date);
        $dt->setTime($fromHours, 0);

        return new self(DateTimeImmutable::createFromMutable($dt), ($toHours - $fromHours) * 3600);

    }

    public function includes(TimeSegmentInterface $timeSegment): bool
    {

        $startsWithin = $timeSegment->getStartTimeStamp() >= $this->getStartTimeStamp() &&
            $timeSegment->getStartTimeStamp() <= $this->getEndTimeStamp();

        $endsWithin = $timeSegment->getEndTimeStamp() >= $this->getStartTimeStamp() &&
            $timeSegment->getEndTimeStamp() <= $this->getEndTimeStamp();

        return $startsWithin || $endsWithin;
    }

    public function getEndTimeStamp(): int
    {
        return $this->endTime->getTimestamp();
    }

    public function getDurationSec(): int
    {
        return $this->durationSec;
    }

    public function toHumanReadableString(): string
    {
        $from = $this->startTime->format('H:i');
        $to = $this->endTime->format('H:i');

        $date = $this->startTime->format('d-M-y');
        return sprintf("%s - %s at %s", $from, $to, $date);
    }
}