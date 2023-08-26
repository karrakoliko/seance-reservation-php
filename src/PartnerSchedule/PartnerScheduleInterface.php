<?php

namespace Karrakoliko\SeanceReservation\PartnerSchedule;

use DateTimeImmutable;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegmentInterface;

interface PartnerScheduleInterface
{

    /** @noinspection PhpUnused */
    public function occupyTime(TimeSegmentInterface $timeSegment): void;

    /**
     * @param DateTimeImmutable $date
     * @return TimeSegmentInterface[]
     */
    public function getFreeTimeForDate(DateTimeImmutable $date): iterable;

    /**
     * @return TimeSegmentInterface[]
     * @noinspection PhpUnused
     */
    public function getOccupiedTimeSegmentsForDate(DateTimeImmutable $date): array;

    /**
     * @param DateTimeImmutable $date
     * @return TimeSegmentInterface
     * @noinspection PhpUnused
     */
    public function createWorkDayTimeSegmentForDate(DateTimeImmutable $date): TimeSegmentInterface;

}