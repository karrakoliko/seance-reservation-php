<?php

namespace Karrakoliko\SeanceReservation;

interface TimeSegmentInterface
{

    public function getDurationSec(): int;

    public function getStartTimeStamp(): int;

    public function getEndTimeStamp(): int;

}