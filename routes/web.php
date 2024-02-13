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
$router->group(['middleware' => 'cors'], function () use ($router) {
    $router->get("releases", 'Controller@releases');
    $router->get("anime/list", 'Controller@animes');
    $router->get("anime/simulcast", 'Controller@simulcast');
    $router->get("anime/search", 'Controller@search');
    $router->get("anime/latino", 'Controller@latino');
    $router->get("anime/trending", 'Controller@trending');
    $router->get("anime/more-view", 'Controller@moreview');
    $router->get("anime/{slug}", 'Controller@anime');
    $router->get("anime/{slug}/episodes/{number}", 'Controller@episode');
});