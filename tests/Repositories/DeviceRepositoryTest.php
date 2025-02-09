<?php

namespace WalkerChiu\DeviceSensor;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\DeviceSensor\Models\Entities\Device;
use WalkerChiu\DeviceSensor\Models\Entities\DeviceLang;
use WalkerChiu\DeviceSensor\Models\Repositories\DeviceRepository;

class DeviceRepositoryTest extends \Orchestra\Testbench\TestCase
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

        $this->repository = $this->app->make(DeviceRepository::class);
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
     * A basic functional test on DeviceRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\Repository
     *
     * @return void
     */
    public function testDeviceRepository()
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
        for ($i=1; $i<=3; $i++)
            $this->repository->save([
                'serial'     => $faker->isbn10,
                'identifier' => $faker->slug,
                'order'      => $faker->randomNumber,
                'slave_id'   => $faker->numberBetween($min = 1, $max = 255),
                'ip'         => $faker->ipv4,
                'port'       => $faker->randomNumber
            ]);

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
            $record = $this->repository->find(4);
            // Then
            $this->assertNull($record);

        // Delete someone
            // When
            $this->repository->deleteByIds([1]);
            $count = $this->repository->count();
            // Then
            $this->assertEquals(2, $count);

            // When
            $this->repository->deleteByExceptIds([3]);
            $count = $this->repository->count();
            $record = $this->repository->find(3);
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
            $this->repository->forcedeleteByIds([3]);
            $records = $this->repository->get();
            // Then
            $this->assertCount(0, $records);

        // Restore records
            // When
            $this->repository->restoreByIds([1, 2]);
            $count = $this->repository->count();
            // Then
            $this->assertEquals(2, $count);
    }

    /**
     * Unit test about Lang creation on DeviceRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\DeviceSensor\Models\Repositories\DeviceRepository
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

        // Find record
            // When
            $record = $this->repository->first();
            // Then
            $this->assertNotNull($record);

        // Create Lang
            // When
            $lang = $this->repository->createLangWithoutCheck(['morph_type' => get_class($record), 'morph_id' => $record->id, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
            // Then
            $this->assertInstanceOf(DeviceLang::class, $lang);
    }

    /**
     * Unit test about Enable and Disable on DeviceRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\DeviceSensor\Models\Repositories\DeviceRepository
     *
     * @return void
     */
    public function testEnableAndDisable()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-sensor.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-sensor.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-sensor.soft_delete', 1);

        // Give
        factory(Device::class)->create(['is_enabled' => 1]);
        factory(Device::class, 3)->create();

        // Count records
            // When
            $count = $this->repository->count();
            $count_enabled = $this->repository->ofEnabled()->count();
            $count_disabled = $this->repository->ofDisabled()->count();
            // Then
            $this->assertEquals(4, $count);
            $this->assertEquals(1, $count_enabled);
            $this->assertEquals(3, $count_disabled);

        // Enable records
            // When
            $this->repository->whereToEnable('id', '>', 3);
            $count_enabled = $this->repository->ofEnabled()->count();
            $count_disabled = $this->repository->ofDisabled()->count();
            // Then
            $this->assertEquals(2, $count_enabled);
            $this->assertEquals(2, $count_disabled);

        // Disable records
            // When
            $this->repository->whereToDisable('id', '>', 0);
            $count_enabled = $this->repository->ofEnabled()->count();
            $count_disabled = $this->repository->ofDisabled()->count();
            // Then
            $this->assertEquals(0, $count_enabled);
            $this->assertEquals(4, $count_disabled);
    }

    /**
     * Unit test about Query List on DeviceRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\DeviceSensor\Models\Repositories\DeviceRepository
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
        factory(Device::class, 4)->create();

        // Get query
            // When
            sleep(1);
            $this->repository->find(3)->touch();
            $records = $this->repository->ofNormal()->get();
            // Then
            $this->assertCount(4, $records);

            // When
            $record = $records->first();
            // Then
            $this->assertArrayNotHasKey('deleted_at', $record->toArray());
            $this->assertEquals(3, $record->id);

        // Get query of trashed records
            // When
            $this->repository->deleteByIds([4]);
            $this->repository->deleteByIds([1]);
            $records = $this->repository->ofTrash()->get();
            // Then
            $this->assertCount(2, $records);

            // When
            $record = $records->first();
            // Then
            $this->assertArrayHasKey('deleted_at', $record);
            $this->assertEquals(1, $record->id);
    }

    /**
     * Unit test about FormTrait on DeviceRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\DeviceSensor\Models\Repositories\DeviceRepository
     *     WalkerChiu\Core\Models\Forms\FormTrait
     *
     * @return void
     */
    public function testFormTrait()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-device-sensor.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-device-sensor.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-device-sensor.soft_delete', 1);

        $faker = \Faker\Factory::create();

        // Name
            // Give
            factory(Device::class, 2)->create();
            $this->repository->createLangWithoutCheck(['morph_id' => 1, 'morph_type' => DeviceRegister::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
            $this->repository->createLangWithoutCheck(['morph_id' => 2, 'morph_type' => Device::class, 'code' => 'zh_tw', 'key' => 'name', 'value' => '您好']);
            // When
            $result_1 = $this->repository->checkExistName('en_us', null, 'Hello');
            $result_2 = $this->repository->checkExistName('en_us', null, 'Hi');
            $result_3 = $this->repository->checkExistName('en_us', 1, 'Hello');
            $result_4 = $this->repository->checkExistName('en_us', 1, '您好');
            $result_5 = $this->repository->checkExistName('zh_tw', 1, '您好');
            $result_6 = $this->repository->checkExistNameOfEnabled('en_us', null, 'Hello');
            // Then
            $this->assertTrue($result_1);
            $this->assertTrue(!$result_2);
            $this->assertTrue(!$result_3);
            $this->assertTrue(!$result_4);
            $this->assertTrue($result_5);
            $this->assertTrue(!$result_6);

        // Serial, Identifier
            // Give
            factory(Device::class)->create(['serial' => '123', 'identifier' => 'A123']);
            factory(Device::class)->create(['serial' => '124', 'identifier' => 'A124']);
            factory(Device::class)->create(['serial' => '125', 'identifier' => 'A125', 'is_enabled' => 0]);

            // When
            $result_1 = $this->repository->checkExistSerial(null, '123');
            $result_2 = $this->repository->checkExistSerial(3, '123');
            $result_3 = $this->repository->checkExistSerial(3, '124');
            $result_4 = $this->repository->checkExistSerialOfEnabled(4, '124');
            $result_5 = $this->repository->checkExistSerialOfEnabled(4, '125');
            // Then
            $this->assertTrue($result_1);
            $this->assertTrue(!$result_2);
            $this->assertTrue($result_3);
            $this->assertTrue(!$result_4);
            $this->assertTrue(!$result_5);

            // When
            $result_1 = $this->repository->checkExistIdentifier(null, 'A123');
            $result_2 = $this->repository->checkExistIdentifier(3, 'A123');
            $result_3 = $this->repository->checkExistIdentifier(3, 'A124');
            $result_4 = $this->repository->checkExistIdentifierOfEnabled(4, 'A124');
            $result_5 = $this->repository->checkExistIdentifierOfEnabled(4, 'A125');
            // Then
            $this->assertTrue($result_1);
            $this->assertTrue(!$result_2);
            $this->assertTrue($result_3);
            $this->assertTrue(!$result_4);
            $this->assertTrue(!$result_5);
    }

    /**
     * Unit test about Auto Complete on DeviceRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\DeviceSensor\Models\Repositories\DeviceRepository
     *
     * @return void
     */
    public function testAutoComplete()
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
        $db_morph_1 = factory(Device::class)->create(['serial' => 'A123', 'is_enabled' => 1]);
        $db_morph_2 = factory(Device::class)->create(['serial' => 'A124', 'is_enabled' => 1]);
        $db_lang_1 = $this->repository->createLangWithoutCheck(['morph_id' => $db_morph_1->id, 'morph_type' => Device::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello']);
        $db_lang_2 = $this->repository->createLangWithoutCheck(['morph_id' => $db_morph_1->id, 'morph_type' => Device::class, 'code' => 'en_us', 'key' => 'description', 'value' => 'Good Morning!']);
        $db_lang_3 = $this->repository->createLangWithoutCheck(['morph_id' => $db_morph_1->id, 'morph_type' => Device::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Hello World']);
        $db_lang_4 = $this->repository->createLangWithoutCheck(['morph_id' => $db_morph_1->id, 'morph_type' => Device::class, 'code' => 'zh_tw', 'key' => 'name', 'value' => '您好']);
        $db_lang_5 = $this->repository->createLangWithoutCheck(['morph_id' => $db_morph_1->id, 'morph_type' => Device::class, 'code' => 'zh_tw', 'key' => 'name', 'value' => '早安']);
        $db_lang_6 = $this->repository->createLangWithoutCheck(['morph_id' => $db_morph_2->id, 'morph_type' => Device::class, 'code' => 'en_us', 'key' => 'name', 'value' => 'Bye']);

        // List array by name of enabled records
            // When
            $records = $this->repository->autoCompleteNameOfEnabled('en_us', 'H');
            // Then
            $this->assertCount(1, $records);

            // When
            $records = $this->repository->autoCompleteNameOfEnabled('zh_tw', 'H');
            // Then
            $this->assertCount(0, $records);

        // List array by serial of enabled records
            // When
            $records = $this->repository->autoCompleteSerialOfEnabled('en_us', 'A');
            // Then
            $this->assertCount(2, $records);
    }
}
