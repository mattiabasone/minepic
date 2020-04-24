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

/* @var \Illuminate\Routing\Router $router */

$router->group(['middleware' => ['headers.cache', 'image.clean_params']], static function () use ($router) {
    // Avatar
    $router->group(['prefix' => 'avatar'], static function () use ($router) {
        $router->get('/{uuid:[0-9a-fA-F]{32}}', 'Api\AvatarController@serveUuid');
        $router->get('/{username:[a-zA-Z0-9_]+}', 'Api\AvatarController@serveUsername');

        $router->get('/{size:[\d]+}/{uuid:[0-9a-fA-F]{32}}', 'Api\AvatarController@serveUuid');
        $router->get('/{size:[\d]+}/{username:[a-zA-Z0-9_]+}', 'Api\AvatarController@serveUsername');
    });

    // Avatar (Isometric)
    $router->group(['prefix' => 'head'], static function () use ($router) {
        $router->get('/{uuid:[0-9a-fA-F]{32}}', 'Api\IsometricAvatarController@serveUuid');
        $router->get('/{username:[a-zA-Z0-9_]+}', 'Api\IsometricAvatarController@serveUsername');

        $router->get('/{size:[\d]}/{uuid:[0-9a-fA-F]{32}}', 'Api\IsometricAvatarController@serveUuid');
        $router->get('/{size:[\d]+}/{username:[a-zA-Z0-9_]+}', 'Api\IsometricAvatarController@serveUsername');
    });

    // Skin Front
    $router->group(['prefix' => 'skin'], static function () use ($router) {
        $router->get('/{uuid:[0-9a-fA-F]{32}}', 'Api\SkinFrontController@serveUuid');
        $router->get('/{username:[a-zA-Z0-9_]+}', 'Api\SkinFrontController@serveUsername');

        $router->get('/{size:[\d]+}/{uuid:[0-9a-fA-F]{32}}', 'Api\SkinFrontController@serveUuid');
        $router->get('/{size:[\d]+}/{username:[a-zA-Z0-9_]+}', 'Api\SkinFrontController@serveUsername');
    });

    // Skin Back
    $router->group(['prefix' => 'skin-back'], static function () use ($router) {
        $router->get('/{uuid:[0-9a-fA-F]{32}}', 'Api\SkinBackController@serveUuid');
        $router->get('/{username:[a-zA-Z0-9_]+}', 'Api\SkinBackController@serveUsername');

        $router->get('/{size:[\d]+}/{uuid:[0-9a-fA-F]{32}}', 'Api\SkinBackController@serveUuid');
        $router->get('/{size:[\d]+}/{username:[a-zA-Z0-9_]+}', 'Api\SkinBackController@serveUsername');
    });
});


// Download
$router->get('/download/{uuidOrName}', 'Api\DownloadTextureController@serve');

// HTML
$router->get('/', 'WebsiteController@index');
$router->get('/user/{uuidOrName}', 'WebsiteController@user');

// JSON
$router->group(['prefix' => 'api/v1'], static function () use ($router) {
    $router->get('user/{uuidOrName}', 'JsonController@user');
    $router->get('user/{uuidOrName}/update', 'JsonController@updateUser');

    $router->get('uuid/{uuid}', 'JsonController@uuidToUsername');

    $router->get('typeahead/{username}', 'JsonController@userTypeahead');

    $router->get('stats/user/most-wanted', 'JsonController@getMostWantedUsers');
});
