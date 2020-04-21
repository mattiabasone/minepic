<?php

declare(strict_types=1);

namespace App\Minecraft;

use Illuminate\Support\Facades\Log;

/**
 * Class MojangAccount.
 */
class MojangAccount
{
    /**
     * UUID of the account.
     *
     * @var string
     */
    public string $uuid = '';

    /**
     * Username of the account.
     *
     * @var string
     */
    public string $username = '';

    /**
     * Skin.
     *
     * @var string
     */
    public string $skin = '';

    /**
     * Cape.
     *
     * @var string
     */
    public string $cape = '';

    /**
     * @var int
     */
    public int $updated = 0;

    /**
     * MinecraftAccount constructor.
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        if (\count($fields) > 0) {
            foreach ($fields as $name => $value) {
                if (\property_exists($this, $name)) {
                    $this->{$name} = $value;
                }
            }
        }
    }

    /**
     * Load from API data response (JSON Decoded).
     *
     * @param array $response Decoded json response
     * @return bool
     */
    public function loadFromApiResponse(array $response): bool
    {
        if (isset($response['properties'])) {
            foreach ($response['properties'] as $property) {
                if ($property['name'] == 'textures') {
                    $tmp = \json_decode(\base64_decode($property['value'], true), true);
                    try {
                        $this->username = $response['name'];
                        $this->uuid = $response['id'];
                        if (isset($tmp['skin']['url'])) {
                            \preg_match('#' . \preg_quote(env('MINECRAFT_TEXTURE_URL')) . '(.*)$#', $tmp['skin']['url'], $matches);
                            $this->skin = $matches[1];
                        } else {
                            $this->skin = '';
                        }
                        if (isset($tmp['cape']['url'])) {
                            \preg_match('#' . \preg_quote(env('MINECRAFT_TEXTURE_URL')) . '(.*)$#', $tmp['cape']['url'], $matches);
                            $this->cape = $matches[1];
                        } else {
                            $this->cape = '';
                        }
                        $this->updated = \time();

                        return true;
                    } catch (\Throwable $exception) {
                        Log::error("Failed with api response: Full data: ".json_encode($response). " -  Temp Data: ".json_encode($tmp));
                        Log::error($exception->getMessage()." ".$exception->getTraceAsString());
                    }
                }
            }
        }

        return false;
    }
}
