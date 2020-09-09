<?php

declare(strict_types=1);

namespace MinepicTests\Misc;

use MinepicTests\TestCase;

class SplashMessageTest extends TestCase
{
    /** @test */
    public function shouldGetMessage(): void
    {
        $this->assertIsString(\Minepic\Misc\SplashMessage::get());
    }

    /** @test */
    public function shouldGet404Message(): void
    {
        $this->assertIsString(\Minepic\Misc\SplashMessage::get404());
    }
}
