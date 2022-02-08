<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DeviceSensor: Device Register
    |--------------------------------------------------------------------------
    |
    */

    'name'        => '名稱',
    'description' => '描述',
    'device_id'   => '讀卡機 ID',
    'serial'      => '編號',
    'identifier'  => '識別符',
    'mean'        => '名稱',
    'data_type'   => '資料型別',
    'is_enabled'  => '是否啟用',

    'list'   => '暫存器清單',
    'create' => '新增暫存器',
    'edit'   => '暫存器修改',

    'form' => [
        'information' => '暫存器資訊',
            'basicInfo'   => '基本資訊'
    ],

    'delete' => [
        'header' => '刪除暫存器',
        'body'   => '確定要刪除這個暫存器嗎？'
    ]
];
