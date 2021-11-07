<?php

declare(strict_types=1);

namespace Minepic\Minecraft;

use Illuminate\Contracts\Support\Arrayable;

class MojangAccount implements Arrayable
{
    /**
     * MinecraftAccount constructor.
     *
     * @param string $uuid
     * @param string $username
     * @param string $skin
     * @param string $cape
     */
    public function __construct(
        private string $uuid,
        private string $username,
        private string $skin = '',
        private string $cape = ''
    ) {
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
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

    /**
     * @return array
     */
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
