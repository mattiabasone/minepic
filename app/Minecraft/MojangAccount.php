<?php

declare(strict_types=1);

namespace Minepic\Minecraft;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class MojangAccount.
 */
class MojangAccount implements Arrayable
{
    /**
     * UUID of the account.
     *
     * @var string
     */
    private string $uuid;

    /**
     * Username of the account.
     *
     * @var string
     */
    private string $username;

    /**
     * Skin.
     *
     * @var string
     */
    private string $skin;

    /**
     * Cape.
     *
     * @var string
     */
    private string $cape;

    /**
     * MinecraftAccount constructor.
     *
     * @param string $uuid
     * @param string $username
     * @param string $skin
     * @param string $cape
     */
    public function __construct(string $uuid, string $username, string $skin = '', string $cape = '')
    {
        $this->uuid = $uuid;
        $this->username = $username;
        $this->skin = $skin;
        $this->cape = $cape;
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
