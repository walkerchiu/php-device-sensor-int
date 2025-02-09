<?php

namespace WalkerChiu\DeviceSensor;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\DeviceSensor\Models\Entities\Device;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceState;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceStateLang;

class DeviceStateTest extends \Orchestra\Testbench\TestCase
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
     * A basic functional test on DeviceState.
     *
     * For WalkerChiu\DeviceSensor\Models\Entities\DeviceState
     * 
     * @return void
     */
    public function testDeviceState()
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
        $record_1 = factory(DeviceState::class)->create();
        $record_2 = factory(DeviceState::class)->create();
        $record_3 = factory(DeviceState::class)->create(['is_enabled' => 1]);

        // Get records after creation
            // When
            $records = DeviceState::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $record_2->delete();
            $records = DeviceState::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            DeviceState::withTrashed()
                       ->find(2)
                       ->restore();
            $record_2 = DeviceState::find(2);
            $records = DeviceState::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Return Lang class
            // When
            $class = $record_2->lang();
            // Then
            $this->assertEquals($class, DeviceStateLang::class);

        // Scope query on enabled records
            // When
            $records = DeviceState::ofEnabled()
                                  ->get();
            // Then
            $this->assertCount(1, $records);

        // Scope query on disabled records
            // When
            $records = DeviceState::ofDisabled()
                                  ->get();
            // Then
            $this->assertCount(2, $records);
    }
}
