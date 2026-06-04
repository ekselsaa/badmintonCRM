<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (\Illuminate\Support\Facades\Schema::hasTable('bookings') || true) {
            try {
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
                    $db = \Illuminate\Support\Facades\DB::connection()->getPdo();
                    $db->sqliteCreateFunction('DATE_FORMAT', function ($date, $format) {
                        if (!$date) return null;
                        $format = str_replace(
                            ['%Y', '%m', '%d', '%H', '%i', '%s'],
                            ['Y', 'm', 'd', 'H', 'i', 's'],
                            $format
                        );
                        return date($format, strtotime($date));
                    });
                    $db->sqliteCreateFunction('HOUR', function ($time) {
                        if (!$time) return null;
                        $parts = explode(':', $time);
                        return isset($parts[0]) ? (int)$parts[0] : 0;
                    });
                }
            } catch (\Exception $e) {
                // Ignore if connection not ready
            }
        }
    }
}
