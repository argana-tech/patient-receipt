<?php

class Model_Receipt extends Model_Base
{
    protected static $_table_name = 'receipts';

    protected static $_properties = array(
        'id',
        'unique_id' => array(
            'data_type' => 'varchar',
        ),
        'status' => array(
            'data_type' => 'int',
            'default' => 0,
        ),
        'date' => array(
            'data_type' => 'date',
        ),
        'receipt_number' => array(
            'data_type' => 'varchar',
            'default' => null,
        ),
        'message' => array(
            'data_type' => 'varchar',
            'default' => null,
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

    const STATUS_BEFORE_RECEIPT = 0;
    const STATUS_RECEIPTING = 1;
    const STATUS_AFTER_RECEIPT = 2;
}
