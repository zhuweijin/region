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

$app->get('/', function () use ($app) {
    return "Hello World";
});

$app->get('/v1/regions', 'RegionController@regions');

\DB::listen(function($sql) {
    app('log')->info(json_encode($sql));
});
