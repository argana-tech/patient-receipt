<?php

class Model_Originalcall extends Model_Base
{
    protected static $_table_name = 'original_calls';

    protected static $_properties = array(
        'id',
        'unique_id' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'receipt_number' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'shinryouka_code' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'shinryouka_name' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'reserve_name' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'reserve_date' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'reserve_type' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'reserve_items' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'room_number' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'reserve_tantou' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'reserve_start_time' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'hojoka' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'message' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'created_at' => array(
            'data_type' => 'datetime',
        ),
        'updated_at' => array(
            'data_type' => 'datetime',
        ),
        'deleted_at' => array(
            'data_type' => 'datetime',
        ),
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
        ),
    );
}
