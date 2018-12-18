<?php

declare(strict_types=1);

namespace App\Image\Exceptions;

class SkinNotFountException extends \Exception
{
    protected $message = 'UUID skin file not found';
}
