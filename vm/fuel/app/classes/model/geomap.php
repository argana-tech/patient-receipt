<?php

class Model_Geomap extends Model_Base
{
    protected static $_table_name = 'geo_maps';

    protected static $_properties = array(
        'id',
        'pref_code' => array(
            'data_type' => 'int',
        ),
        'pref' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'city' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'address' => array(
            'data_type' => 'varchar',
            'default' => '',
        ),
        'lat' => array(
            'data_type' => 'double',
            'default' => 0.0,
        ),
        'lon' => array(
            'data_type' => 'double',
            'default' => 0.0,
        ),
        'geo_hash' => array(
            'data_type' => 'varchar',
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

    /**
     * 住所
     */
    public function get_address($data = 'short')
    {
        switch ($data) {
            case 'full':
                $params = array(
                    'pref',
                    'city',
                    'address',
                );
                break;
            case 'long':
                $params = array(
                    'pref',
                    'city',
                    'address',
                );
                break;
            case 'pref_city':
                $params = array(
                    'prefecture',
                    'city',
                );
                break;
            default:
                $params = array(
                    'city',
                    'address',
                );
                break;
        }

        $address = '';
        foreach($params as $param) {
            $address .= $this->$param;
        }
        if (!$address) {
            $address = 'エリア外';
        }
        return $address;
    }
}
