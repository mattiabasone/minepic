<?php

declare(strict_types=1);

namespace App\Minecraft;

/**
 * Class MojangAccountFactory.
 */
class MojangAccountFactory
{
    /**
     * Extract texture (skin/cape) ids from URL.
     *
     * @param string $url
     *
     * @return string
     */
    private static function extractTextureIdFromUrl(string $url): string
    {
        \preg_match('#'.env('MINECRAFT_TEXTURE_URL').'(.*)$#', $url, $matches);

        return $matches[1] ?? '';
    }

    /**
     * @param array $response
     *
     * @throws \JsonException
     *
     * @return MojangAccount|null
     */
    public static function makeFromApiResponse(array $response): ?MojangAccount
    {
        if (!\array_key_exists('id', $response) || !\array_key_exists('name', $response)) {
            return null;
        }

        $uuid = $response['id'];
        $username = $response['name'];
        $skin = '';
        $cape = '';

        if (\array_key_exists('properties', $response)) {
            $textures = \array_filter($response['properties'], static function ($entry) {
                return $entry['name'] === 'textures';
            });

            if (!empty($textures)) {
                $textureData = \json_decode(\base64_decode($textures[0]['value'], true), true, 512, JSON_THROW_ON_ERROR);

                if (isset($textureData['skin']['url'])) {
                    $skin = self::extractTextureIdFromUrl($textureData['skin']['url']);
                }

                if (isset($tmp['cape']['url'])) {
                    $cape = self::extractTextureIdFromUrl($textureData['cape']['url']);
                }
            }
        }

        return new MojangAccount($uuid, $username, $skin, $cape);
    }
}
