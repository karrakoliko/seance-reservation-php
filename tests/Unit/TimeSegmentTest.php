<?php

namespace Karrakoliko\SeanceReservation\Tests\Unit;

use DateTimeImmutable;
use InvalidArgumentException;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegment;

class TimeSegmentTest extends TestCase
{

    public static function includesProvider(): array
    {
        return [
            [
                [
                    [16, 20],
                    [17, 18]
                ],
                true
            ],
            [
                [
                    [19, 20],
                    [17, 18]
                ],
                false
            ],
            [
                [
                    [8, 20],
                    [4, 9]
                ],
                true
            ]
        ];
    }

    public function testThrowsIfNegativeDurationGiven()
    {
        $this->expectException(InvalidArgumentException::class);
        $duration = -5;

        new TimeSegment(new DateTimeImmutable(), $duration);

    }

    public function testEndTimeCalculatedCorrectly()
    {
        $date = DateTimeImmutable::createFromFormat('d-m-y H:i', '23-08-23 16:00');
        $durationSec = 3600;
        $endTimeExpected = $date->getTimestamp() + $durationSec;

        $segment = new TimeSegment($date, $durationSec);

        $this->assertEquals($endTimeExpected, $segment->getEndTimeStamp());
    }


    /**
     * @dataProvider includesProvider
     * @return void
     */
    public function testIncludes(array $hoursSegments, bool $expected)
    {
        $date = DateTimeImmutable::createFromFormat('d-m-y H:i', '23-08-23 16:00');

        $tsA = TimeSegment::createWithHoursFromTo($date, $hoursSegments[0][0], $hoursSegments[0][1]);
        $tsB = TimeSegment::createWithHoursFromTo($date, $hoursSegments[1][0], $hoursSegments[1][1]);

        $actual = $tsA->includes($tsB);
        $this->assertEquals($expected, $actual);
    }

}
