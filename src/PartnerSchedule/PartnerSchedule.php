<?php

namespace Karrakoliko\SeanceReservation\PartnerSchedule;

use DateTime;
use DateTimeImmutable;
use Karrakoliko\SeanceReservation\PartnerSchedule\Exception\TimeIsAlreadyOccupiedException;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegment;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegmentInterface;

class PartnerSchedule implements PartnerScheduleInterface
{
    /**
     * @var TimeSegmentInterface[]
     */
    private array $occupiedTimeSegments = [];
    private readonly int $workDayStartHours;
    private readonly int $workDayEndHours;

    public function __construct(int $workDayStartHours, int $workDayEndHours)
    {
        $this->workDayStartHours = $workDayStartHours;
        $this->workDayEndHours = $workDayEndHours;
    }

    public function occupyTime(TimeSegmentInterface $timeSegment): void
    {
        foreach ($this->occupiedTimeSegments as $currentlyOccupiedTime) {

            if ($currentlyOccupiedTime->includes($timeSegment)) {
                throw new TimeIsAlreadyOccupiedException(
                    sprintf('Time already occupied by segment %s',
                        $currentlyOccupiedTime->toHumanReadableString()
                    ));
            }

        }

        $this->occupiedTimeSegments[] = $timeSegment;

        // we have to sort as we rely on time segments order when loop over them
        usort($this->occupiedTimeSegments, function (TimeSegmentInterface $a, TimeSegmentInterface $b) {
            return $a->getStartTimeStamp() <=> $b->getStartTimeStamp();
        });
    }

    /**
     * @param DateTimeImmutable $date
     * @return TimeSegmentInterface[]
     */
    public function getFreeTimeForDate(DateTimeImmutable $date): iterable
    {
        $occupied = $this->getOccupiedTimeSegmentsForDate($date);

        $workDayTimeSegment = $this->createWorkDayTimeSegmentForDate($date);

        $prevEndTimeStamp = $workDayTimeSegment->getStartTimeStamp();
        $occupiedSegmentsExhausted = !count($occupied);

        while ($prevEndTimeStamp < $workDayTimeSegment->getEndTimeStamp()) {

            if ($occupiedSegmentsExhausted) {

                $lastEndDateTime = (new DateTime())->setTimestamp($prevEndTimeStamp);
                $secUntilWorkDayEnd = $workDayTimeSegment->getEndTimeStamp() - $prevEndTimeStamp;

                yield new TimeSegment(DateTimeImmutable::createFromMutable($lastEndDateTime), $secUntilWorkDayEnd);
                break;

            } else {

                foreach ($occupied as $k => $occupiedSegment) {

                    if (!$workDayTimeSegment->includes($occupiedSegment)) {
                        $occupiedSegmentsExhausted = true;
                        break;
                    }

                    $freeSegmentStart = (new DateTime())->setTimestamp($prevEndTimeStamp);
                    $freeSegmentDurationSec = $occupiedSegment->getStartTimeStamp() - $freeSegmentStart->getTimestamp();

                    $freeSegment = new TimeSegment(DateTimeImmutable::createFromMutable($freeSegmentStart), $freeSegmentDurationSec);

                    $prevEndTimeStamp = $occupiedSegment->getEndTimeStamp();

                    yield $freeSegment;

                    if ($k === array_key_last($occupied)) {
                        $occupiedSegmentsExhausted = true;
                    }

                }
            }

        }

    }

    public function getOccupiedTimeSegmentsForDate(DateTimeImmutable $date): array
    {
        return $this->occupiedTimeSegments;
    }

    /**
     * @param DateTimeImmutable $date
     * @return TimeSegmentInterface
     */
    public function createWorkDayTimeSegmentForDate(DateTimeImmutable $date): TimeSegmentInterface
    {
        return TimeSegment::createWithTime(
            $date,
            $this->workDayStartHours,
            0,
            ($this->workDayEndHours - $this->workDayStartHours) * 3600
        );
    }
}