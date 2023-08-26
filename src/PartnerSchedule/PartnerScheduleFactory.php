<?php

namespace Karrakoliko\SeanceReservation\PartnerSchedule;

use DateTimeImmutable;
use DateTimeInterface;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegment;

class PartnerScheduleFactory
{

    public static function createWithOccupiedHours(
        int    $occupiedHoursStart,
        int    $occupiedHoursTotal,
        string $dateISO8601 = 'today',
        int    $workDayStartHours = 10,
        int    $worDayEndHours = 20
    ): PartnerScheduleInterface
    {
        $date = $dateISO8601 === 'today' ? new DateTimeImmutable('now') : DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dateISO8601);

        $partner = new PartnerSchedule($workDayStartHours, $worDayEndHours);

        // time 13:00-15:00 is occupied
        $occupied = TimeSegment::createWithTime($date, $occupiedHoursStart, 0, 3600 * $occupiedHoursTotal);

        $partner->occupyTime($occupied);

        return $partner;
    }

}