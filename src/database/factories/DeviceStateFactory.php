<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceState;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceStateLang;

$factory->define(DeviceState::class, function (Faker $faker) {
    return [
        'device_id'  => 1,
        'serial'     => $faker->isbn10,
        'identifier' => $faker->slug,
        'mean'       => $faker->slug
    ];
});

$factory->define(DeviceStateLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
