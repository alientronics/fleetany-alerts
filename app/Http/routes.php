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
    return $app->version();
});

$app->group(['prefix' => 'api/v1','namespace' => 'App\Http\Controllers', 'middleware' => 'auth'], function ($app) {

    /* basic crud for keys */
    $app->get('alerts/{entity_key}/{entity_id}/{company_id}', 'AlertsController@getAlerts');
    $app->get(
        'alerts/{entity_key}/{entity_id}/type/{alert_type}/{company_id}',
        'AlertsController@getAlertTypeReport'
    );
    $app->get('alerts/{entity_key}/{entity_id}', 'AlertsController@get');
    $app->post('alert', 'AlertsController@create');
});
