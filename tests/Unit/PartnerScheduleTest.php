<?php

namespace Karrakoliko\SeanceReservation\Tests\Unit;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Karrakoliko\SeanceReservation\PartnerSchedule\Exception\TimeIsAlreadyOccupiedException;
use Karrakoliko\SeanceReservation\PartnerSchedule\PartnerSchedule;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegment;

class PartnerScheduleTest extends TestCase
{

    public static function getFreeTimeForDateProvider(): array
    {
        return [
            [
                '2023-08-26T00:00:00+00:00',
                ['wd_start' => 8, 'wd_end' => 20], // wd = Work Day
                [
                    ['occupied_start_hours' => 13, 'occupied_total_hours' => 2]
                ],
                [[8, 13], [15, 20]]
            ],
            [
                '2023-08-26T00:00:00+00:00',
                ['wd_start' => 8, 'wd_end' => 20], // wd = Work Day
                [
                    ['occupied_start_hours' => 18, 'occupied_total_hours' => 2],
                ],
                [[8, 18]]
            ],
            [
                '2023-08-26T00:00:00+00:00',
                ['wd_start' => 8, 'wd_end' => 20], // wd = Work Day
                [
                    ['occupied_start_hours' => 18, 'occupied_total_hours' => 2],
                    ['occupied_start_hours' => 11, 'occupied_total_hours' => 1],
                ],
                [[8, 11], [12, 18]]
            ],
            [
                '2023-08-26T00:00:00+00:00',
                ['wd_start' => 8, 'wd_end' => 20], // wd = Work Day
                [
                    // no occupied time
                ],
                [[8, 20]]
            ]
        ];
    }

    public function testThrowsIfSameTimeOccupiedTwice()
    {
        $this->expectException(TimeIsAlreadyOccupiedException::class);

        $partner = new PartnerSchedule(10, 19);

        $date = new DateTimeImmutable();

        $partner->occupyTime(TimeSegment::createWithHoursFromTo($date, 13, 16));
        $partner->occupyTime(TimeSegment::createWithHoursFromTo($date, 14, 19));
    }

    /**
     * @dataProvider getFreeTimeForDateProvider
     * @return void
     * @throws Exception
     */
    public function testGetFreeTimeForDate(string $dateISO, array $workHours, array $occupiedHours, array $freeHours)
    {

        $date = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $dateISO);

        $partner = new PartnerSchedule($workHours['wd_start'], $workHours['wd_end']);

        foreach ($occupiedHours as $occupiedHoursPair) {

            $occupiedSegment = TimeSegment::createWithTime(
                $date,
                $occupiedHoursPair['occupied_start_hours'],
                0,
                $occupiedHoursPair['occupied_total_hours'] * 3600
            );

            $partner->occupyTime($occupiedSegment);
        }

        $freeSegmentsActual = iterator_to_array($partner->getFreeTimeForDate($date));

        $freeSegmentsExpected = array_map(function (array $hours) use ($date) {
            return TimeSegment::createWithHoursFromTo($date, $hours[0], $hours[1]);
        }, $freeHours);

        $this->assertEquals($freeSegmentsExpected, $freeSegmentsActual);
    }
}
