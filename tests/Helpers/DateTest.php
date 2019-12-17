<?php

declare(strict_types=1);

class DateTest extends TestCase
{
    public function unixTimeStampsDataProvider(): array
    {
        return [
            [1576625018, '12/17/2019'],
            [0, 'Never'],
        ];
    }

    /**
     * @test
     * @dataProvider unixTimeStampsDataProvider
     */
    public function shouldHumanizeTimestamp($timestamp, $expected): void
    {
        $this->assertEquals($expected, \App\Helpers\Date::humanizeTimestamp($timestamp));
    }
}
