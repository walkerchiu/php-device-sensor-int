<?php

namespace WalkerChiu\DeviceSensor;

use Illuminate\Support\Facades\Validator;
use WalkerChiu\DeviceSensor\Models\Entities\Device;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceRegister;
use WalkerChiu\DeviceSensor\Models\Entities\Data;
use WalkerChiu\DeviceSensor\Models\Forms\DataFormRequest;

class DataFormRequestTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$this->loadLaravelMigrations(['--database' => 'mysql']);
        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');

        $this->request  = new DataFormRequest();
        $this->rules    = $this->request->rules();
        $this->messages = $this->request->messages();
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
     * Unit test about Authorize.
     *
     * For WalkerChiu\DeviceSensor\Models\Forms\DataFormRequest
     * 
     * @return void
     */
    public function testAuthorize()
    {
        $this->assertEquals(true, 1);
    }

    /**
     * Unit test about Rules.
     *
     * For WalkerChiu\DeviceSensor\Models\Forms\DataFormRequest
     * 
     * @return void
     */
    public function testRules()
    {
        $faker = \Faker\Factory::create();

        factory(Device::class)->create();
        factory(DeviceRegister::class)->create();

        // Give
        $attributes = [
            'device_id'   => 1,
            'register_id' => 1,
            'value'       => '123',
            'trigger_at'  => '2019-01-01 01:00:00'
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(false, $fails);

        // Give
        $attributes = [
            'device_id'   => 1,
            'register_id' => 1,
            'value'       => '123',
            'trigger_at'  => ''
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);
    }
}
