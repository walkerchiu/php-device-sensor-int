<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\DeviceSensor\Models\Entities\Device;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceLang;

$factory->define(Device::class, function (Faker $faker) {
    return [
        'serial'     => $faker->isbn10,
        'identifier' => $faker->slug,
        'order'      => $faker->randomNumber,
        'slave_id'   => $faker->numberBetween($min = 1, $max = 255),
        'ip'         => $faker->ipv4,
        'port'       => $faker->numberBetween($min = 1, $max = 65535)
    ];
});

$factory->define(DeviceLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
