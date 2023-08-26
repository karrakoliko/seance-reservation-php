<?php

namespace Karrakoliko\SeanceReservation;

use Karrakoliko\SeanceReservation\PartnerSchedule\PartnerScheduleInterface;
use Karrakoliko\SeanceReservation\Slot\Slot;

class SeancePlanningService
{

    public function getAvailableSlots(
        \DateTimeImmutable       $date,
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

                $slotEndDt = (new \DateTime())->setTimestamp($slotEndTimeStamp);
                $slotStartDt = (new \DateTime())->setTimestamp($slotStartTimeStamp);

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