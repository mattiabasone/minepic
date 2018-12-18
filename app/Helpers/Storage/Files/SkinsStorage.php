<?php

declare(strict_types=1);

namespace App\Helpers\Storage\Files;

use App\Helpers\Storage\Storage;

class SkinsStorage extends Storage
{
    /**
     * Skins storage location.
     *
     * @var string
     */
    protected static $folder = 'skins';
}
