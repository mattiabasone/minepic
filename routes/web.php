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

// Avatar
$router->group(['prefix' => 'avatar'], static function () use ($router) {
    $router->get('/{uuidOrName}', 'Api\AvatarController@serve');
    $router->get('/{size}/{uuidOrName}', 'Api\AvatarController@serveWithSize');
});

// Avatar (Isometric)
$router->group(['prefix' => 'head'], static function () use ($router) {
    $router->get('/{uuidOrName}', 'Api\IsometricAvatarController@serve');
    $router->get('/{size}/{uuidOrName}', 'Api\IsometricAvatarController@serveWithSize');
});

// Skin Front
$router->group(['prefix' => 'skin'], static function () use ($router) {
    $router->get('/{uuidOrName}', 'Api\SkinFrontController@serve');
    $router->get('/{size}/{uuidOrName}', 'Api\SkinFrontController@serveWithSize');
});

// Skin Back
$router->group(['prefix' => 'skin-back'], static function () use ($router) {
    $router->get('/{uuidOrName}', 'Api\SkinBackController@serve');
    $router->get('/{size}/{uuidOrName}', 'Api\SkinBackController@serveWithSize');
});

// Download
$router->get('/download/{uuidOrName}', 'Api\DownloadTextureController@serve');

// HTML
$router->get('/', 'WebsiteController@index');
$router->get('/user/{uuidOrName}', 'WebsiteController@user');

// JSON
$router->group(['prefix' => 'json'], static function () use ($router) {
    $router->get('user/{uuidOrName}', 'JsonController@user');
    $router->get('user/{uuidOrName}/update', 'JsonController@updateUser');

    $router->get('stats/user/get-most-wanted', 'JsonController@getMostWantedUsers');

    $router->get('uuid/{uuid}', 'JsonController@uuidToUsername');

    $router->get('typeahead/{username}', 'JsonController@userTypeahead');
});
