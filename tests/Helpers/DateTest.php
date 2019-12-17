<?php

declare(strict_types=1);

class DateTest extends TestCase
{
    public function unixTimeStampsDataProvider(): array
    {
        return [
            [1576625018, '2019-12-17 23:23:38 GMT'],
            [0, 'Never'],
        ];
    }

    /**
     * @test
     * @dataProvider unixTimeStampsDataProvider
     */
    public function shouldHumanizeTimestamp(int $timestamp, string $expected): void
    {
        $this->assertEquals($expected, \App\Helpers\Date::humanizeTimestamp($timestamp));
    }
}
