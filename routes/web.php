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

$router->get('/', function () use ($router) {
    return response()->json([
        'name' => config('app.name'),
        'version' => config('app.version'),
        'framework' => $router->app->version(),
        'environment' => config('app.env'),
        'debug_mode' => config('app.debug'),
        'timestamp' => \Carbon\Carbon::now()->toDateTimeString(),
        'timezone' => config('app.timezone'),
    ], 200);
});
