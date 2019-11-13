<?php

declare(strict_types=1);

/**
 * Class SplashMessageTest.
 */
class SplashMessageTest extends TestCase
{
    /** @test */
    public function shouldGetMessage(): void
    {
        $this->assertIsString(\App\Misc\SplashMessage::get());
    }

    /** @test */
    public function shouldGet404Message(): void
    {
        $this->assertIsString(\App\Misc\SplashMessage::get404());
    }
}
