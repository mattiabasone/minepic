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
$router->get('/', 'Website@index');
$router->get('/user/{uuidOrName}', 'Website@user');

// JSON
$router->group(['prefix' => 'json'], function () use ($router) {
    $router->get('user/{uuidOrName}', 'Json@user');

    $router->get('uuid/{uuid}', 'Json@uuidToUsername');

    $router->get('typeahead/{username}', 'Json@userTypeahead');
});

// Avatar
$router->get('/avatar/{uuidOrName}', 'Api@serveAvatar');
$router->get('/avatar/{size}/{uuidOrName}', 'Api@avatarWithSize');

// Avatar (Isometric)
$router->get('/head/{uuidOrName}', 'Api@serveIsometricAvatar');
$router->get('/head/{size}/{uuidOrName}', 'Api@isometricAvatarWithSize');

// Skin
$router->get('/skin/{uuidOrName}', 'Api@serveSkin');
$router->get('/skin/{size}/{uuidOrName}', 'Api@skinFrontWithSize');

$router->get('/skin-back/{uuidOrName}', 'Api@skinBackWithoutSize');
$router->get('/skin-back/{size}/{uuidOrName}', 'Api@skinBackWithSize');

// Download
$router->get('/download/{uuidOrName}', 'Api@downloadTexture');

// Update
$router->get('/update/{uuidOrName}', 'Api@update');
