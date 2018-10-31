<?php

class Model_Notification extends Model_Base
{
    protected static $_table_name = 'notifications';

    protected static $_properties = array(
        'id',
        'unique_id' => array(
            'data_type' => 'varchar',
        ),
        'original_call_id' => array(
            'data_type' => 'int',
            'default' => 0,
        ),
        'device_token' => array(
            'data_type' => 'varchar',
        ),
        'platform' => array(
            'data_type' => 'varchar',
        ),
        'application_type' => array(
            'data_type' => 'int',
            'default' => 0,
        ),
        'body' => array(
            'data_type' => 'varchar',
        ),
        'send_at' => array(
            'data_type' => 'datetime',
            'default' => null,
        ),
        'send_status' => array(
            'data_type' => 'bool',
            'default' => false,
        ),
        'status' => array(
            'data_type' => 'int',
            'default' => 0,
        ),
        'filename' => array(
            'data_type' => 'varchar',
        ),
        'try_numbers' => array(
            'data_type' => 'int',
            'default' => 0,
        ),
        'is_read' => array(
            'data_type' => 'bool',
            'default' => false,
        ),
        'use_read' => array(
            'data_type' => 'bool',
            'default' => true,
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

    const STATUS_NEW = 0;
    const STATUS_SENDING = 1;
    const STATUS_WAIT_RETRY = 2;
    const STATUS_SENDED = 3;

    public function get_status_string()
    {
        $status_string = array(
            'new',
            'sending',
            'wait retry',
            'sended',
        );

        if (is_null($this->status)) {
            return 'unknown';
        }
        return \Arr::get($status_string, $this->status, 'unknown');
    }

    public function get_platform()
    {
        return strtolower($this->platform);
    }
}
