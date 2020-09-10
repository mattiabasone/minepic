<?php

declare(strict_types=1);

namespace Minepic\Minecraft;

class MinecraftDefaults
{
    public const STEVE_DEFAULT_SKIN_NAME = 'steve_default_skin';
    public const ALEX_DEFAULT_SKIN_NAME = 'alex_default_skin';

    /**
     * @throws \Exception
     *
     * @return string
     */
    public static function getRandomDefaultSkin(): string
    {
        return \random_int(0, 1) === 1 ? self::STEVE_DEFAULT_SKIN_NAME : self::ALEX_DEFAULT_SKIN_NAME;
    }
}
