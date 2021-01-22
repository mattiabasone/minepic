<?php

declare(strict_types=1);

namespace MinepicTests;

class BasicTest extends TestCase
{
    public function testShouldReturnHomePage(): void
    {
        $this->get('/');

        $this->assertEquals(200, $this->response->getStatusCode());
    }
}
