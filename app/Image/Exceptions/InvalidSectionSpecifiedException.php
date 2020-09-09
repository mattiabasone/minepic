<?php

declare(strict_types=1);

namespace Minepic\Image\Exceptions;

class InvalidSectionSpecifiedException extends \Exception
{
    protected $message = 'Invalid section specified';
}
