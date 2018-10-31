<?php

class Model_Location_Geo extends \Orm\Model
{
    protected static $_table_name = 'geo_locations';

    protected static $_properties = array(
        'id',
        'unique_id' => array(
            'data_type' => 'varchar',
        ),
        'address' => array(
            'data_type' => 'varchar',
            'default' => null,
        ),
        'latitude' => array(
            'data_type' => 'double',
            'default' => 0.0,
        ),
        'longitude' => array(
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

    public static function calc_distance($point) {
        $base = \Config::get('my.base_point');
        $y1 = $base['latitude'];
        $x1 = $base['longitude'];
        $y2 = $point['lat'];
        $x2 = $point['lon'];

        $r = 6378137.0;

        // 緯度経度をラジアンに変換
        $radLat1 = deg2rad($y1); // 緯度１
        $radLon1 = deg2rad($x1); // 経度１
        $radLat2 = deg2rad($y2); // 緯度２
        $radLon2 = deg2rad($x2); // 経度２

        $averageLat = ($radLat1 - $radLat2) / 2;
        $averageLon = ($radLon1 - $radLon2) / 2;
        $dist = abs($r * 2 * asin(sqrt(pow(sin($averageLat), 2) + cos($radLat1) * cos($radLat2) * pow(sin($averageLon), 2))));

        Log::info(sprintf("distance: %f m", $dist));
        return $dist;
    }
}
