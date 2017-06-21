<?php

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
$app->get('/', 'Website@index');
$app->get('/user/{uuidOrName}', 'Website@user');

// JSON
$app->group(['prefix' => 'json'], function () use ($app) {
    $app->get('user/{uuidOrName}', 'Json@user');

    $app->get('uuid/{uuid}', 'Json@uuidToUsername');

    $app->get('typeahead/{username}', 'Json@userTypeahead');
});

// Avatar
$app->get('/avatar/{uuidOrName}', 'Api@serveAvatar');
$app->get('/avatar/{size}/{uuidOrName}', 'Api@avatarWithSize');

// Avatar (Isometric)
$app->get('/head/{uuidOrName}', 'Api@serveIsometricAvatar');
$app->get('/head/{size}/{uuidOrName}', 'Api@isometricAvatarWithSize');

// Skin
$app->get('/skin/{uuidOrName}', 'Api@serveSkin');
$app->get('/skin/{size}/{uuidOrName}', 'Api@skinFrontWithSize');

$app->get('/skin-back/{uuidOrName}', 'Api@skinBackWithoutSize');
$app->get('/skin-back/{size}/{uuidOrName}', 'Api@skinBackWithSize');

// Download
$app->get('/download/{uuidOrName}', 'Api@downloadTexture');

// Update
$app->get('/update/{uuidOrName}', 'Api@update');