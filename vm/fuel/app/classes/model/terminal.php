<?php

class Model_Terminal extends Model_Base
{
    protected static $_table_name = 'terminals';

    protected static $_properties = array(
        'id',
        'unique_id' => array(
            'data_type' => 'varchar',
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

    const APP_TYPE_PATIENT_CALL = 0;
    const APP_TYPE_PATIENT_RECEIPT = 1;

    public static function get_application_type_name($app_type)
    {
        $app_type_names = array(
            0 => 'patient_calls',
            1 => 'patient_receiption',
        );
        return isset($app_type_names[$app_type])
            ? $app_type_names[$app_type]
            : $app_type_names[0];
    }

    public static function is_usable_platform($platform) {
        switch (strtolower($platform)) {
            case 'ios':
            case 'android':
                return true;
                break;

            default:
                return false;
                break;
        }
    }
}
