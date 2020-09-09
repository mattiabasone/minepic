<?php

declare(strict_types=1);

namespace Minepic\Events;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}
