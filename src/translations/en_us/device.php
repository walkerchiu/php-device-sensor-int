<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DeviceSensor: Device
    |--------------------------------------------------------------------------
    |
    */

    'name'        => 'Name',
    'description' => 'Description',
    'location'    => 'Location',
    'serial'      => 'Serial',
    'identifier'  => 'Identifier',
    'order'       => 'Order',
    'is_enabled'  => 'Is Enabled',

    'slave_id'      => 'Slave ID',
    'ip'            => 'IP Address',
    'port'          => 'Port',
    'scan_interval' => 'Scan Interval',
    'sync_at'       => 'Synchronize At',
    'is_multiplex'  => 'Is Multiplex',

    'list'   => 'Device List',
    'create' => 'Create Device',
    'edit'   => 'Edit Device',

    'form' => [
        'information' => 'Information',
            'basicInfo'   => 'Basic info'
    ],

    'delete' => [
        'header' => 'Delete Device',
        'body'   => 'Are you sure you want to delete this device?'
    ]
];
