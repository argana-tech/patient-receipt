<?php

class Model_Location_Beacon extends \Orm\Model
{
    protected static $_table_name = 'beacon_locations';

    protected static $_properties = array(
        'id',
        'unique_id' => array(
            'data_type' => 'varchar',
        ),
        'address' => array(
            'data_type' => 'varchar',
            'default' => null,
        ),
        'beacon_name' => array(
            'data_type' => 'varchar',
            'default' => null,
        ),
        'radio_strength' => array(
            'data_type' => 'double',
            'default' => 0.0,
        ),
        'time' => array(
            'data_type' => 'datetime',
            'default' => null,
        ),
        'send_flag' => array(
            'data_type' => 'bool',
            'default' => false,
        ),
        'created_at' => array(
            'data_type' => 'datetime',
        ),
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
        ),
    );

    public static function aggregate($beacon_locations = array())
    {
        $locations = array();
        $collection = null;
        $beacon_info = \Config::get('my.beacon_info', array());

        if (is_array($beacon_locations) && count($beacon_locations) > 0) {
            foreach($beacon_locations as $key => $value) {
                if (!isset($locations[$value['name']])) {
                    $locations[$value['name']] = array(
                        'beacon_name' => \Arr::get($beacon_info, $value['name'], '登録なし'),
                        'radio_strength' => 0,
                    );
                }
                $locations[$value['name']]['radio_strength'] += $value['level'];
            }

            $collection = \Arr::multisort($locations, array(
                'radio_strength' => SORT_DESC,
            ));
        }

        // Log::info(print_r($collection, true));

        $location = array();
        if ($collection) {
            $location = array_shift($collection);
        }

        \Log::info(print_r($location, true));

        return $location;
    }
}
