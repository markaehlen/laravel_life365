<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Module::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'is_active' => true,
    ];
});
