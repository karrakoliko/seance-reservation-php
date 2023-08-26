<?php

namespace Karrakoliko\SeanceReservation;

use DateTime;
use DateTimeImmutable;
use Karrakoliko\SeanceReservation\PartnerSchedule\PartnerScheduleInterface;
use Karrakoliko\SeanceReservation\Slot\Slot;

class SeancePlanningService
{

    public function getAvailableSlots(
        DateTimeImmutable        $date,
        int                      $seanceDurationSec,
        PartnerScheduleInterface $partnerSchedule,
        int                      $intervalSec
    )
    {

        $freeSegments = iterator_to_array($partnerSchedule->getFreeTimeForDate($date));

        $partnerWorkTime = $partnerSchedule->createWorkDayTimeSegmentForDate($date);

        foreach ($freeSegments as $segment) {

            $isSegmentExhausted = false;
            $isFirst = true;
            $lastSlotStart = null;

            while ($isSegmentExhausted === false) {

                if ($segment->getDurationSec() < $seanceDurationSec) {
                    $isSegmentExhausted = true;
                    continue;
                }

                $slotStartTimeStamp = $lastSlotStart === null ? $segment->getStartTimeStamp() : $lastSlotStart;

                if (!$isFirst) {
                    $slotStartTimeStamp += $intervalSec;
                } else {
                    $isFirst = false;
                }

                $slotEndTimeStamp = $slotStartTimeStamp + $seanceDurationSec;

                if (isset($_ENV['Karrakoliko/SeanceReservation/env'])) {

                    if (in_array($_ENV['Karrakoliko/SeanceReservation/env'], ['test', 'debug'])) {
                        // convenient vars for debugging

                        $slotEndDt = (new DateTime('@' . $slotEndTimeStamp));
                        $slotStartDt = (new DateTime('@' . $slotStartTimeStamp));
                    }
                }

                if ($slotEndTimeStamp > $segment->getEndTimeStamp()) {
                    break;
                }

                $slot = Slot::createFromTimeStamp($slotStartTimeStamp);

                $lastSlotStart = $slotStartTimeStamp;

                yield $slot;
            }

        }
    }

}