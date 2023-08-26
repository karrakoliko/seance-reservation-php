<?php

namespace Karrakoliko\SeanceReservation\Tests\Unit;

use DateTimeImmutable;
use Exception;
use Karrakoliko\SeanceReservation\PartnerSchedule\PartnerSchedule;
use Karrakoliko\SeanceReservation\SeancePlanner;
use Karrakoliko\SeanceReservation\Slot\Slot;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegment;

class SeancePlannerTest extends TestCase
{
    public static function getAvailableSlotsProvider(): array
    {
        return [
            [
                45,
                [
                    'wd_start_hours' => 19,
                    'wd_end_hours' => 20,
                ],
                [
                    '19:00',
                    '19:15'
                ],
                [
                    // no occupied hours
                ]
            ],
            [
                45,
                [
                    'wd_start_hours' => 10,
                    'wd_end_hours' => 20,
                ],
                [
                    '10:00', '10:15', '10:30', '10:45', '11:00', '11:15', '11:30', '11:45', '12:00', '12:15', '14:00', '14:15', '14:30', '14:45', '15:00', '15:15', '15:30', '15:45', '16:00', '16:15', '16:30', '16:45', '17:00', '17:15', '17:30', '17:45', '18:00', '18:15', '18:30', '18:45', '19:00', '19:15'
                ],
                [
                    [
                        'occupied_time_from' => '13:00',
                        'occupied_duration_sec' => 3600,
                    ]
                ]
            ],
            'spec' => [
                45,
                [
                    'wd_start_hours' => 10,
                    'wd_end_hours' => 20,
                ],
                [
                    '11:00', '11:15', '11:30', '12:45', '13:00', '13:15', '13:30', '13:45', '14:00', '14:15', '14:30', '14:45', '16:30', '16:45', '17:00', '17:15', '17:30', '17:45', '18:00', '18:15', '18:30', '18:45', '19:00'
                ],
                [
                    [
                        'occupied_time_from' => '10:00',
                        'occupied_duration_sec' => 3600,
                    ],
                    [
                        'occupied_time_from' => '12:15',
                        'occupied_duration_sec' => 1800,
                    ],
                    [
                        'occupied_time_from' => '15:30',
                        'occupied_duration_sec' => 3600,
                    ],
                    [
                        'occupied_time_from' => '19:45',
                        'occupied_duration_sec' => 7200,
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider getAvailableSlotsProvider
     * @return void
     * @throws Exception
     */
    public function testGetAvailableSlots(
        int   $seanceDurationMin,
        array $partnerWorDayHours,
        array $slotsExpected,
        array $occupiedHours = []
    )
    {
        $planner = new SeancePlanner();

        $date = new DateTimeImmutable();

        $partnerSchedule = new PartnerSchedule(
            $partnerWorDayHours['wd_start_hours'],
            $partnerWorDayHours['wd_end_hours']
        );

        foreach ($occupiedHours as $occupiedHourPair) {

            list($hoursFrom, $minutesFrom) = explode(':', $occupiedHourPair['occupied_time_from']);
            $partnerSchedule->occupyTime(
                TimeSegment::createWithTime(
                    $date,
                    $hoursFrom,
                    $minutesFrom,
                    $occupiedHourPair['occupied_duration_sec'],
                )
            );
        }

        $slots = $planner->getAvailableSlots($date, $seanceDurationMin * 60, $partnerSchedule, 15 * 60);

        $slotsArr = iterator_to_array($slots);

        $slotsArr = array_map(function (Slot $slot) {
            return $slot->getDateTime()->format('H:i');
        }, $slotsArr);

        $this->assertEquals($slotsExpected, $slotsArr);

    }
}
