<?php

namespace WalkerChiu\DeviceSensor;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\DeviceSensor\Models\Entities\Device;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceRegister;
use WalkerChiu\DeviceSensor\Models\Entities\Data;
use WalkerChiu\DeviceSensor\Models\Repositories\DataRepository;

class DataRepositoryTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected $repository;

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

        $this->repository = $this->app->make(DataRepository::class);
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
     * A basic functional test on DataRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\Repository
     *
     * @return void
     */
    public function testDataRepository()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-sensor.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-sensor.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-sensor.soft_delete', 1);

        // Give
        $faker = \Faker\Factory::create();
        $db_device = factory(Device::class)->create();
        $db_register = factory(DeviceRegister::class)->create(['device_id' => $db_device->id]);
        $id_list = [];
        for ($i=1; $i<=3; $i++) {
            $record = $this->repository->save([
                'device_id'   => $db_device->id,
                'register_id' => $db_register->id,
                'value'       => $faker->randomFloat(2),
                'trigger_at'  => '2019-01-01 01:00:00'
            ]);
            array_push($id_list, $record->id);
        }

        // Get and Count records after creation
            // When
            $records = $this->repository->get();
            $count   = $this->repository->count();
            // Then
            $this->assertCount(3, $records);
            $this->assertEquals(3, $count);

        // Find someone
            // When
            $record = $this->repository->first();
            // Then
            $this->assertNotNull($record);

            // When
            $record = $this->repository->find($faker->uuid());
            // Then
            $this->assertNull($record);

        // Delete someone
            // When
            $this->repository->deleteByIds([$id_list[0]]);
            $count = $this->repository->count();
            // Then
            $this->assertEquals(2, $count);

            // When
            $this->repository->deleteByExceptIds([$id_list[2]]);
            $count = $this->repository->count();
            $record = $this->repository->find($id_list[2]);
            // Then
            $this->assertEquals(1, $count);
            $this->assertNotNull($record);

            // When
            $count = $this->repository->where('id', '>', 0)->count();
            // Then
            $this->assertEquals(1, $count);

            // When
            $count = $this->repository->whereWithTrashed('id', '>', 0)->count();
            // Then
            $this->assertEquals(3, $count);

            // When
            $count = $this->repository->whereOnlyTrashed('id', '>', 0)->count();
            // Then
            $this->assertEquals(2, $count);

        // Force delete someone
            // When
            $this->repository->forcedeleteByIds([$id_list[2]]);
            $records = $this->repository->get();
            // Then
            $this->assertCount(0, $records);

        // Restore records
            // When
            $this->repository->restoreByIds([$id_list[0], $id_list[1]]);
            $count = $this->repository->count();
            // Then
            $this->assertEquals(2, $count);
    }

    /**
     * Unit test about Lang creation on DataRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\DeviceSensor\Models\Repositories\DataRepository
     * 
     * @return void
     */
    public function testcreateLangWithoutCheck()
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
        factory(Data::class)->create();

        // Find record
            // When
            $record = $this->repository->first();
            // Then
            $this->assertNotNull($record);
    }

    /**
     * Unit test about Query List on DataRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\DeviceSensor\Models\Repositories\DataRepository
     *
     * @return void
     */
    public function testQueryList()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-sensor.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-sensor.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-sensor.soft_delete', 1);

        $faker = \Faker\Factory::create();

        // Give
        $db_device = factory(Device::class)->create();
        $db_register = factory(DeviceRegister::class)->create(['device_id' => $db_device->id]);
        $db_morph_1 = factory(Data::class)->create(['device_id' => $db_device->id, 'register_id' => $db_register->id]);
        $db_morph_2 = factory(Data::class)->create(['device_id' => $db_device->id, 'register_id' => $db_register->id]);
        $db_morph_3 = factory(Data::class)->create(['device_id' => $db_device->id, 'register_id' => $db_register->id]);
        $db_morph_4 = factory(Data::class)->create(['device_id' => $db_device->id, 'register_id' => $db_register->id]);

        // Get query
            // When
            sleep(1);
            $this->repository->find($db_morph_3->id)->touch();
            $records = $this->repository->ofNormal()->get();
            // Then
            $this->assertCount(4, $records);

            // When
            $record = $records->first();
            // Then
            $this->assertArrayNotHasKey('deleted_at', $record->toArray());
            $this->assertEquals($db_morph_3->id, $record->id);

        // Get query of trashed records
            // When
            $this->repository->deleteByIds([$db_morph_4->id]);
            $this->repository->deleteByIds([$db_morph_1->id]);
            $records = $this->repository->ofTrash()->get();
            // Then
            $this->assertCount(2, $records);

            // When
            $record = $records->first();
            // Then
            $this->assertArrayHasKey('deleted_at', $record);
            $this->assertEquals($db_morph_1->id, $record->id);
    }
}
