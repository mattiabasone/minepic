<?php

declare(strict_types=1);

namespace Minepic\Minecraft;

class MojangAccountFactory
{
    /**
     * @throws \JsonException
     */
    public static function makeFromApiResponse(array $response): MojangAccount
    {
        $uuid = $response['id'];
        $username = $response['name'];
        $skin = '';
        $cape = '';

        if (!\array_key_exists('properties', $response)) {
            return new MojangAccount($uuid, $username, $skin, $cape);
        }

        $textures = array_filter($response['properties'], static function (array $entry) {
            return $entry['name'] === 'textures';
        });

        if (!empty($textures)) {
            $textureData = json_decode((string) base64_decode($textures[0]['value'], true), associative: true, flags: \JSON_THROW_ON_ERROR);

            if (isset($textureData['textures']['SKIN']['url'])) {
                $skin = self::extractTextureIdFromUrl($textureData['textures']['SKIN']['url']);
            }

            if (isset($textureData['textures']['CAPE']['url'])) {
                $cape = self::extractTextureIdFromUrl($textureData['textures']['CAPE']['url']);
            }
        }

        return new MojangAccount($uuid, $username, $skin, $cape);
    }

    /**
     * Extract texture (skin/cape) ids from URL.
     */
    private static function extractTextureIdFromUrl(string $url): string
    {
        preg_match('#'.env('MINECRAFT_TEXTURE_URL').'(.*)$#', $url, $matches);

        return $matches[1] ?? '';
    }
}
