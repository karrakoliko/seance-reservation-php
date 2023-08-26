<?php

namespace Karrakoliko\SeanceReservation\TimeSegment;

interface TimeSegmentInterface
{

    public function getDurationSec(): int;

    public function getStartTimeStamp(): int;

    public function getEndTimeStamp(): int;

    public function includes(TimeSegmentInterface $timeSegment): bool;

    public function toHumanReadableString(): string;

}