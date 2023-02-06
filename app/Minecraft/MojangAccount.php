<?php

declare(strict_types=1);

namespace Minepic\Minecraft;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, null|string>
 */
readonly class MojangAccount implements Arrayable
{
    public function __construct(
        private string $uuid,
        private string $username,
        private string $skin = '',
        private string $cape = ''
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getSkin(): ?string
    {
        return $this->skin;
    }

    public function getCape(): ?string
    {
        return $this->cape;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'username' => $this->username,
            'skin' => $this->skin,
            'cape' => $this->cape,
        ];
    }
}
