<?php

namespace WalkerChiu\DeviceSensor;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\DeviceSensor\Models\Entities\Device;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceRegister;
use WalkerChiu\DeviceSensor\Models\Entities\Data;

class DataTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\DeviceSensor\DeviceSensorServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * A basic functional test on Data.
     *
     * For WalkerChiu\DeviceSensor\Models\Entities\Data
     * 
     * @return void
     */
    public function testData()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-sensor.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-sensor.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-sensor.soft_delete', 1);

        // Give
        factory(Device::class)->create();
        factory(DeviceRegister::class)->create();
        $db_data_1 = factory(Data::class)->create();
        $db_data_2 = factory(Data::class)->create();
        $db_data_3 = factory(Data::class)->create();

        // Get records after creation
            // When
            $records = Data::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $db_data_2->delete();
            $records = Data::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Data::withTrashed()
                ->find($db_data_2->id)
                ->restore();
            $record_2 = Data::find($db_data_2->id);
            $records = Data::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);
    }
}
