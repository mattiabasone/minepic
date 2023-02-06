<?php

declare(strict_types=1);

namespace Minepic\Minecraft;

use Illuminate\Contracts\Support\Arrayable;

class MojangAccount implements Arrayable
{
    /**
     * MinecraftAccount constructor.
     */
    public function __construct(
        private readonly string $uuid,
        private readonly string $username,
        private readonly string $skin = '',
        private readonly string $cape = ''
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

    /**
     * @return string
     */
    public function getSkin(): ?string
    {
        return $this->skin;
    }

    /**
     * @return string
     */
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
