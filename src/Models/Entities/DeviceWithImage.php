<?php

namespace WalkerChiu\DeviceSensor\Models\Entities;

use WalkerChiu\DeviceSensor\Models\Entities\Device;
use WalkerChiu\MorphImage\Models\Entities\ImageTrait;

class DeviceWithImage extends Device
{
    use ImageTrait;
}
