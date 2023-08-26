#!/usr/bin/env php
<?php

require_once(realpath(__DIR__ . '/../vendor/autoload.php'));
require_once(realpath(__DIR__ . '/helper/showSlots.php'));

use Karrakoliko\SeanceReservation\PartnerSchedule\PartnerSchedule;
use Karrakoliko\SeanceReservation\SeancePlanner;
use Karrakoliko\SeanceReservation\TimeSegment\TimeSegment;

/**
 * Ищем свободные слоты на сегодня
 */
$date = new DateTimeImmutable();

/**
 * Длительность сеанса - 45 мин
 */
$seanceDuration = 45 * 60;

/**
 * Кратность сеансов (интервал) - 15 мин
 */
$seanceInterval = 15 * 60;

/**
 * Режим работы партнёра 10-20ч
 */
$partnerSchedule = new PartnerSchedule(10, 20);

/**
 * Временной отрезок (TimeSegment) с 13 до 14 занят
 */
$partnerSchedule->occupyTime(
    TimeSegment::createWithTime(
        $date,
        13,
        0,
        3600 // 1 час в секундах
    )
);

$planner = new SeancePlanner();

$slots = $planner->getAvailableSlots(
    $date,
    $seanceDuration,
    $partnerSchedule,
    $seanceInterval
);

showSlots($slots, $date,$partnerSchedule);

