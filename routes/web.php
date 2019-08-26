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

// HTML
$router->get('/', 'WebsiteController@index');
$router->get('/user/{uuidOrName}', 'WebsiteController@user');

// JSON
$router->group(['prefix' => 'json'], function () use ($router) {
    $router->get('user/{uuidOrName}', 'JsonController@user');

    $router->get('uuid/{uuid}', 'JsonController@uuidToUsername');

    $router->get('typeahead/{username}', 'JsonController@userTypeahead');
});

// Avatar
$router->get('/avatar/{uuidOrName}', 'ApiController@serveAvatar');
$router->get('/avatar/{size}/{uuidOrName}', 'ApiController@avatarWithSize');

// Avatar (Isometric)
$router->get('/head/{uuidOrName}', 'ApiController@serveIsometricAvatar');
$router->get('/head/{size}/{uuidOrName}', 'ApiController@isometricAvatarWithSize');

// Skin
$router->get('/skin/{uuidOrName}', 'ApiController@serveSkin');
$router->get('/skin/{size}/{uuidOrName}', 'ApiController@skinFrontWithSize');

$router->get('/skin-back/{uuidOrName}', 'ApiController@skinBackWithoutSize');
$router->get('/skin-back/{size}/{uuidOrName}', 'ApiController@skinBackWithSize');

// Download
$router->get('/download/{uuidOrName}', 'ApiController@downloadTexture');

// Update
$router->get('/update/{uuidOrName}', 'ApiController@update');
