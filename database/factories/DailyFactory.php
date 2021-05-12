<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Daily::class, function (Faker $faker) {
    return [
        'name' => $faker->title,
        'description' => $faker->text,
        'status' => 1,

        // 'priority' => 100,
        'hubstaff' => null,
        'date' => null,
        // 'completed_date' => null,
    ];
});
