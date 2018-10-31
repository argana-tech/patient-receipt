<?php
class My_Validreceipt
{
    public static function is_release($now = null)
    {
        if (!$now) {
            $now = time();
        }
        $release = \Config::get('my.release');
        $start = \Config::get('my.receipt_time.start', false);

        return (strtotime($release . $start . ':00') >= $now) ? false : true;
    }

    public static function is_distance($unique_id, $now = null)
    {
        if (!$now) {
            $now = time();
        }
        $start = \Config::get('my.receipt_time.start', false);
        $end = \Config::get('my.receipt_time.end', false);
        $limit_distance = \Config::get('my.location.distance');

        $location_geo = Model_Location_Geo::find('first', array(
            'where' => array(
                'unique_id' => $unique_id,
                array('created_at', '>=', date('Y-m-d ' . $start . ':00', $now)),
                array('created_at', '<=', date('Y-m-d ' . $end . ':00', $now)),
            ),
            'order_by' => array(
                'created_at' => 'desc',
            ),
        ));
        $location_beacon = Model_Location_Beacon::find('first', array(
            'where' => array(
                'unique_id' => $unique_id,
                array('created_at', '>=', date('Y-m-d ' . $start . ':00', $now)),
                array('created_at', '<=', date('Y-m-d ' . $end . ':00', $now)),
            ),
            'order_by' => array(
                'created_at' => 'desc',
            ),
        ));

        $location_allow = '';
        if ($location_geo && $location_beacon) {
            if (strtotime($location_beacon->created_at) > strtotime($location_geo->created_at)) {
                $location_allow = 'beacon';
            } else {
                $location_allow = 'geo';
            }
        } elseif ($location_geo) {
            $location_allow = 'geo';
        } elseif ($location_beacon) {
            $location_allow = 'beacon';
        } else {
            // 位置情報の登録のないユニークIDに対してはtrueを返します。
            return true;
        }

        if ($location_allow == 'geo' && $location_geo->latitude && $location_geo->longitude) {
            $distance = Model_Location_Geo::calc_distance(array('lat' => $location_geo->latitude, 'lon' => $location_geo->longitude));
            \Log::debug("unique_id: $unique_id, distance: $distance, limit_distance: $limit_distance");
            if ($distance <= $limit_distance) {
                return true;
            } else {
                return false;
            }
        } elseif ($location_allow == 'beacon' && $location_beacon->beacon_name && $location_beacon->radio_strength) {
            \Log::debug("unique_id: $unique_id, beacon_address:" . $location_beacon->address);
            return true;
        } else {
            \Log::debug("unique_id: $unique_id, エリア外");
            return false;
        }
    }

    public static function is_receipt_time($now = null)
    {
        if (!$now) {
            $now = time();
        }
        $start = \Config::get('my.receipt_time.start', false);
        $end = \Config::get('my.receipt_time.end', false);

        if ($start && $end) {
            $start_timestamp = strtotime(date('Y-m-d', $now) . ' ' . $start . ':00');
            $end_timestamp = strtotime(date('Y-m-d', $now) . ' ' . $end . ':00');
            if ($start_timestamp <= $now && $now <= $end_timestamp) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function is_receipted($unique_id, $now = null, &$message = null)
    {
        if (!$now) {
            $now = time();
        }

        $receipt = Model_Receipt::find('first', array(
            'where' => array(
                'unique_id' => $unique_id,
                'date' => date('Y-m-d', $now),
            ),
            'order_by' => array(
                'created_at' => 'desc',
            ),
        ));
        if ($receipt && ($receipt->status == 1 || $receipt->status == 2)) {
            if ($receipt->status == 1) {
                $message = \Config::get('my.messages.check.receipt.now');
            }
            return true;
        } else {
            return false;
        }
    }
}
