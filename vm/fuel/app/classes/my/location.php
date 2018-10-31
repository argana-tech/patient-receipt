<?php

class My_Location
{
    /**
     * 位置情報用集計設定情報
     */
    public static function get_aggregate_info()
    {
        $location_config = \Config::get('my.location');
        $beacon_send_span = \Arr::get($location_config, 'send.span', 60);
        $beacon_collect_span = \Arr::get($location_config, 'collect.span', 5);
        $active_send_time = \Arr::get($location_config, 'use_send_time');
        $geo_send_span = \Arr::get($location_config, 'geo_send_span', 60);

        if (!is_int($beacon_send_span)) {
            $beacon_send_span = 60;
        } elseif ($beacon_send_span < 30) {
            $beacon_send_span = 30;
        }
        if (!is_int($geo_send_span)) {
            $geo_send_span = 60;
        } elseif ($geo_send_span < 30) {
            $geo_send_span = 30;
        }

        if (!is_int($beacon_collect_span)) {
            $beacon_collect_span = 5;
        } elseif ($beacon_collect_span < 1) {
            $beacon_collect_span = 1;
        }

        if (!is_array($active_send_time)) {
            $active_send_time = array(
                'start' => '8:00',
                'end' => '18:00',
            );
        } else {
            if (empty($active_send_time['start'])) {
                $active_send_time['start'] = '8:00';
            }
            if (empty($active_send_time['end'])) {
                $active_send_time['end'] = '18:00';
            }
        }

        return array(
            'beacon_send_span' => $beacon_send_span,
            'beacon_collect_span' => $beacon_collect_span,
            'active_send_time' => $active_send_time,
            'geo_send_span' => $geo_send_span,
        );
    }

    public static function get_beacon_uuid_list()
    {
        $beacon = \Config::get("my.beacon_info");

        return array_keys($beacon);
    }
}
