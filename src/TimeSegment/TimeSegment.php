<?php

namespace Karrakoliko\SeanceReservation\TimeSegment;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

readonly class TimeSegment implements TimeSegmentInterface
{
    private DateTimeImmutable $startTime;
    private int $durationSec;

    private DateTimeImmutable $endTime;

    public function __construct(DateTimeImmutable $startTime, int $durationSec)
    {

        if ($durationSec < 1) {
            throw new InvalidArgumentException("durationSec must be positive integer number");
        }

        $this->startTime = $startTime;
        $this->durationSec = $durationSec;
        $this->endTime = $this->calcEndTime();
    }

    protected function calcEndTime(): DateTimeImmutable
    {

        $endTimeStamp = $this->getStartTimeStamp() + $this->durationSec;
        /** @noinspection PhpUnhandledExceptionInspection */
        return new DateTimeImmutable('@' . $endTimeStamp);
    }

    public function getStartTimeStamp(): int
    {
        return $this->startTime->getTimestamp();
    }

    public static function createWithTime(DateTimeImmutable $date, int $hours, int $minutes, int $durationSec): TimeSegment
    {
        $dt = DateTime::createFromImmutable($date);
        $dt->setTime($hours, $minutes);

        return new self(DateTimeImmutable::createFromMutable($dt), $durationSec);

    }

    public static function createWithHoursFromTo(DateTimeImmutable $date, int $fromHours, int $toHours): TimeSegment
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

    /** @noinspection PhpUnused */
    public function getDurationSec(): int
    {
        return $this->durationSec;
    }

    public function toHumanReadableString(): string
    {
        $from = $this->startTime->format('H:i');
        $to = $this->endTime->format('H:i');

        $date = $this->startTime->format('d-M-y');
        return sprintf("%s - %s (%s)", $from, $to, $date);
    }
}