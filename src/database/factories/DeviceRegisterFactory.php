<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\Core\Models\Constants\DataType;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceRegister;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceRegisterLang;

$factory->define(DeviceRegister::class, function (Faker $faker) {
    return [
        'device_id'  => 1,
        'serial'     => $faker->isbn10,
        'identifier' => $faker->slug,
        'mean'       => $faker->slug,
        'data_type'  => $faker->randomElement(DataType::getCodes())
    ];
});

$factory->define(DeviceRegisterLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
