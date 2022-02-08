<?php

namespace WalkerChiu\DeviceSensor\Models\Entities;

use WalkerChiu\Core\Models\Entities\Lang;

class DeviceRegisterLang extends Lang
{
    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.device-sensor.devices_registers_lang');

        parent::__construct($attributes);
    }
}
