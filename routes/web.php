<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});
$router->get("releases", Controller::class . "@releases");
$router->get("anime/list", Controller::class . "@animes");
$router->get("anime/simulcast", Controller::class . "@simulcast");
$router->get("anime/search", Controller::class . "@search");
$router->get("anime/latino", Controller::class . "@latino");
$router->get("anime/trending", Controller::class . "@trending");
$router->get("anime/more-view", Controller::class . "@moreview");
$router->get("anime/{slug}", Controller::class . "@anime");
$router->get("anime/{slug}/episodes/{number}", Controller::class . "@episode");