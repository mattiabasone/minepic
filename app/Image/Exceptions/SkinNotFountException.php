<?php

declare(strict_types=1);

namespace Minepic\Image\Exceptions;

class SkinNotFountException extends \Exception
{
    protected $message = 'UUID skin file not found';
}
