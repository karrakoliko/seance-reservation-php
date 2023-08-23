<?php

namespace Karrakoliko\SeanceReservation\Tests\Unit;

use Karrakoliko\SeanceReservation\TimeSegment;
use PHPUnit\Framework\TestCase;

class TimeSegmentTest extends TestCase
{

    public function testThrowsIfNegativeDurationGiven()
    {
        $this->expectException(\InvalidArgumentException::class);
        $duration = -5;

        new TimeSegment(new \DateTimeImmutable(), $duration);

    }

    public function testEndTimeCalculatedCorrectly()
    {
        $date = \DateTimeImmutable::createFromFormat('d-m-y H:i', '23-08-23 16:00');
        $durationSec = 3600;
        $endTimeExpected = $date->getTimestamp() + $durationSec;

        $segment = new TimeSegment($date, $durationSec);

        $this->assertEquals($endTimeExpected, $segment->getEndTimeStamp());
    }

}
