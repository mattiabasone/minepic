<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/* @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['headers.cache', 'image.clean_params']], static function () use ($router) {
    // Avatar
    $router->group(['prefix' => 'avatar'], static function () use ($router) {
        $router->get(ROUTE_UUID_MATCH, 'Api\AvatarController@serveUuid');
        $router->get(ROUTE_USERNAME_MATCH, 'Api\AvatarController@serveUsername');

        $router->get(ROUTE_SIZE_MATCH.ROUTE_UUID_MATCH, 'Api\AvatarController@serveUuid');
        $router->get(ROUTE_SIZE_MATCH.ROUTE_USERNAME_MATCH, 'Api\AvatarController@serveUsername');
    });

    // Avatar (Isometric)
    $router->group(['prefix' => 'head'], static function () use ($router) {
        $router->get(ROUTE_UUID_MATCH, 'Api\IsometricAvatarController@serveUuid');
        $router->get(ROUTE_USERNAME_MATCH, 'Api\IsometricAvatarController@serveUsername');

        $router->get(ROUTE_SIZE_MATCH.ROUTE_UUID_MATCH, 'Api\IsometricAvatarController@serveUuid');
        $router->get(ROUTE_SIZE_MATCH.ROUTE_USERNAME_MATCH, 'Api\IsometricAvatarController@serveUsername');
    });

    // Skin Front
    $router->group(['prefix' => 'skin'], static function () use ($router) {
        $router->get(ROUTE_UUID_MATCH, 'Api\SkinFrontController@serveUuid');
        $router->get(ROUTE_USERNAME_MATCH, 'Api\SkinFrontController@serveUsername');

        $router->get(ROUTE_SIZE_MATCH.ROUTE_UUID_MATCH, 'Api\SkinFrontController@serveUuid');
        $router->get(ROUTE_SIZE_MATCH.ROUTE_USERNAME_MATCH, 'Api\SkinFrontController@serveUsername');
    });

    // Skin Back
    $router->group(['prefix' => 'skin-back'], static function () use ($router) {
        $router->get(ROUTE_UUID_MATCH, 'Api\SkinBackController@serveUuid');
        $router->get(ROUTE_USERNAME_MATCH, 'Api\SkinBackController@serveUsername');

        $router->get(ROUTE_SIZE_MATCH.ROUTE_UUID_MATCH, 'Api\SkinBackController@serveUuid');
        $router->get(ROUTE_SIZE_MATCH.ROUTE_USERNAME_MATCH, 'Api\SkinBackController@serveUsername');
    });
});

$router->get('update'.ROUTE_UUID_MATCH, 'JsonController@updateUser');

// Download
$router->get('/download'.ROUTE_UUID_MATCH, 'Api\DownloadTextureController@serve');

// HTML
$router->get('/', 'WebsiteController@index');
$router->get('/user'.ROUTE_UUID_MATCH, 'WebsiteController@user');
$router->get('/user'.ROUTE_USERNAME_MATCH, 'WebsiteController@userWithUsername');

// JSON
$router->group(['prefix' => 'api/v1'], static function () use ($router) {
    $router->get('user'.ROUTE_UUID_MATCH, 'JsonController@user');
    $router->get('user'.ROUTE_USERNAME_MATCH, 'JsonController@userWithUsername');
    $router->get('user'.ROUTE_UUID_MATCH.'/update', 'JsonController@updateUser');

    $router->get('uuid'.ROUTE_UUID_MATCH, 'JsonController@uuidToUsername');

    $router->get('typeahead'.ROUTE_USERNAME_MATCH, 'JsonController@userTypeahead');

    $router->get('stats/user/most-wanted', 'JsonController@getMostWantedUsers');
});
