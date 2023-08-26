<?php

use Karrakoliko\SeanceReservation\PartnerSchedule\PartnerScheduleInterface;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegmentInterface;

function showSlots(iterable $slots, DateTimeImmutable $date, PartnerScheduleInterface $partnerSchedule)
{
    echo sprintf("Режим работы партнёра: %s", $partnerSchedule->createWorkDayTimeSegmentForDate($date)->toHumanReadableString());
    echo PHP_EOL;

    echo sprintf("Занятое время:");
    echo PHP_EOL;

    foreach ($partnerSchedule->getOccupiedTimeSegmentsForDate($date) as $occupiedSegment){

        echo sprintf('* %s', $occupiedSegment->toHumanReadableString());
        echo PHP_EOL;

    }

    echo sprintf(
        "Доступные слоты на %s:",
        $date->format("d/m/y")
    );
    echo PHP_EOL;

    foreach ($slots as $slot) {
        echo sprintf(
            "* %s ",
            $slot->getDateTime()->format('H:i')
        );
    }

    echo PHP_EOL;
}