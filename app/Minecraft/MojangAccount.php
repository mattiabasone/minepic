<?php
namespace App\Minecraft;

/**
 * Class MinecraftAccount
 * @package App\Objects
 */
class MojangAccount {

    /**
     * UUID of the account
     *
     * @var string
     */
    public $uuid = '';

    /**
     * Username of the account
     *
     * @var string
     */
    public $username = '';

    /**
     * Skin
     *
     * @var string
     */
    public $skin = '';

    /**
     * Cape
     *
     * @var string
     */
    public $cape = '';

    /**
     * @var int
     */
    public $updated = 0;

    /**
     * MinecraftAccount constructor.
     * @param array $fields
     */
    public function __construct(array $fields = []) {
        if (count($fields) > 0) {
            foreach ($fields as $name => $value) {
                if (property_exists($this, $name)) {
                    $this->{$name} = $value;
                }
            }
        }
    }

    /**
     * Load from API data response (JSON Decoded)
     *
     * @param array $response
     * @return bool
     */
    public function loadFromApiResponse(array $response) : bool {
        if (isset($response['properties'])) {
            foreach($response['properties'] as $property) {
                if ($property['name'] == 'textures') {
                    $tmp = json_decode(base64_decode($property['value']), true);

                    $this->username = $tmp['profileName'];
                    $this->uuid = $tmp['profileId'];
                    if (isset($tmp['textures']['SKIN']['url'])) {
                        preg_match("#".preg_quote(env('MINECRAFT_TEXTURE_URL'))."(.*)$#", $tmp['textures']['SKIN']['url'], $matches);
                        $this->skin = $matches[1];
                    } else {
                        $this->skin = false;
                    }
                    if (isset($tmp['textures']['CAPE']['url'])) {
                        preg_match("#".preg_quote(env('MINECRAFT_TEXTURE_URL'))."(.*)$#", $tmp['textures']['CAPE']['url'], $matches);
                        $this->cape = $matches[1];
                    } else {
                        $this->cape = false;
                    }
                    $this->updated = time();
                    return true;
                }
            }
        }
        return false;
    }
}