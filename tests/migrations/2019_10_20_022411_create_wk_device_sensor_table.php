<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkDeviceSensorTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.device-sensor.devices'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->unsignedBigInteger('order')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->unsignedSmallInteger('slave_id');
            $table->string('ip');
            $table->unsignedSmallInteger('port');
            $table->unsignedSmallInteger('scan_interval')->default(500);
            $table->char('sync_at', 6)->nullable();
            $table->boolean('is_multiplex')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
            $table->index('slave_id');
            $table->index(['ip', 'port']);
        });
        if (!config('wk-device-sensor.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.device-sensor.devices_lang'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('morph');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->text('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }

        Schema::create(config('wk-core.table.device-sensor.devices_registers'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id');
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->string('mean');
            $table->string('data_type');
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('device_id')->references('id')
                  ->on(config('wk-core.table.device-sensor.devices'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
        });
        if (!config('wk-device-sensor.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.device-sensor.devices_registers_lang'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('morph');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->text('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }

        Schema::create(config('wk-core.table.device-sensor.devices_states'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id');
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->string('mean');
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('device_id')->references('id')
                  ->on(config('wk-core.table.device-sensor.devices'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
        });
        if (!config('wk-device-sensor.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.device-sensor.devices_states_lang'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('morph');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->text('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }


        Schema::create(config('wk-core.table.device-sensor.data'), function (Blueprint $table) {
            $table->uuid('id');
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('register_id');
            $table->string('value');
            $table->timestamp('trigger_at');

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('device_id')->references('id')
                  ->on(config('wk-core.table.device-sensor.devices'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('register_id')->references('id')
                  ->on(config('wk-core.table.device-sensor.devices_registers'))
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->primary('id');
        });
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.device-sensor.data'));

        Schema::dropIfExists(config('wk-core.table.device-sensor.cards_lang'));
        Schema::dropIfExists(config('wk-core.table.device-sensor.cards'));

        Schema::dropIfExists(config('wk-core.table.device-sensor.devices_states_lang'));
        Schema::dropIfExists(config('wk-core.table.device-sensor.devices_states'));
        Schema::dropIfExists(config('wk-core.table.device-sensor.devices_registers_lang'));
        Schema::dropIfExists(config('wk-core.table.device-sensor.devices_registers'));
        Schema::dropIfExists(config('wk-core.table.device-sensor.devices_lang'));
        Schema::dropIfExists(config('wk-core.table.device-sensor.devices'));
    }
}
