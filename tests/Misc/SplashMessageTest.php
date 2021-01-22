<?php

declare(strict_types=1);

namespace MinepicTests\Misc;

use MinepicTests\TestCase;

class SplashMessageTest extends TestCase
{
    public function testShouldGetMessage(): void
    {
        $this->assertIsString(\Minepic\Misc\SplashMessage::get());
    }

    public function testShouldGet404Message(): void
    {
        $this->assertIsString(\Minepic\Misc\SplashMessage::get404());
    }
}
