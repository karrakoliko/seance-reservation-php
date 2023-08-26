<?php

namespace Karrakoliko\SeanceReservation\Tests\Unit;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $_ENV['Karrakoliko/SeanceReservation/env'] = 'test';
    }


}