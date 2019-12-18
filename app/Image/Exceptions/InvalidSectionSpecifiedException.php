<?php

declare(strict_types=1);

namespace App\Image\Exceptions;

class InvalidSectionSpecifiedException extends \Exception
{
    protected $message = 'Invalid section specified';
}
