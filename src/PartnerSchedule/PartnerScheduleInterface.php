<?php

namespace Karrakoliko\SeanceReservation\PartnerSchedule;

use DateTimeImmutable;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegmentInterface;

interface PartnerScheduleInterface
{

    public function occupyTime(TimeSegmentInterface $timeSegment): void;

    /**
     * @param DateTimeImmutable $date
     * @return TimeSegmentInterface[]
     */
    public function getFreeTimeForDate(DateTimeImmutable $date): iterable;

    /**
     * @return TimeSegmentInterface[]
     */
    public function getOccupiedTimeSegmentsForDate(DateTimeImmutable $date): array;

    /**
     * @param DateTimeImmutable $date
     * @return TimeSegmentInterface
     */
    public function createWorkDayTimeSegmentForDate(DateTimeImmutable $date): TimeSegmentInterface;

}