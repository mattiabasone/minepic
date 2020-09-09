<?php

declare(strict_types=1);

namespace Minepic\Misc;

/**
 * Class SplashMessage.
 */
class SplashMessage
{
    /**
     * @var array
     */
    protected static $messages = [
        'Over 9000 avatars!!',
        'Serving since 2013',
        'Just select and copy the URL, come on!',
        'How can I help you?',
        'RTFM!',
        'Whoooooops!',
        'Many avatars, Wow!',
    ];

    /**
     * @var array
     */
    protected static $messages404 = [
        'Oooops! 404 :(',
        'Page is gone',
        'This page has been stolen by an Enderman',
        'This page has a drop rate of 0%',
        'This is not a page',
    ];

    /**
     * get random message.
     */
    public static function get(): string
    {
        return self::$messages[\array_rand(self::$messages)];
    }

    public static function get404()
    {
        return self::$messages404[\array_rand(self::$messages404)];
    }
}
