<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Company::class, function ($faker) {
    return [
        'name' => $faker->name,
        'api_token' => $faker->name,
    ];
});

$factory->define(App\User::class, function ($faker) {
    return [
        'name' => $faker->name,
        'api_token' => $faker->name,
    ];
});

$factory->define(App\Entities\Alerts::class, function ($faker) {
    return [
        'alert_type_id' => 1,
        'entity_key' => 'tire',
        'entity_id' => 1,
    ];
});