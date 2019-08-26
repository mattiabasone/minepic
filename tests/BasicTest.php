<?php

declare(strict_types=1);

class BasicTest extends TestCase
{
    /** @test */
    public function shouldReturnHomePage(): void
    {
        $this->get('/');

        $this->assertEquals(200, $this->response->getStatusCode());
    }
}
