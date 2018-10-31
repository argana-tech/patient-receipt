<?php

class My_Geohash
{
    private static $base_32 = '0123456789bcdefghjkmnpqrstuvwxyz';

    /**
     * @param float $lat latitude
     * @param float $lon longitude
     */
    public static function geo_hash_encode($lat, $lon, $length = 8) {
        $arr_lat_bin = str_split(self::lat_encode($lat));
        $arr_lon_bin = str_split(self::lon_encode($lon));

        $conv_bin = '';

        for($i = 0; $i < 20; $i++) {
            $conv_bin .= isset($arr_lon_bin[$i]) ? $arr_lon_bin[$i] : 0;
            $conv_bin .= isset($arr_lat_bin[$i]) ? $arr_lat_bin[$i] : 0;
        }

        $base_32 = self::$base_32;
        $hash = '';
        for($i = 0; $i < $length; $i++) {
            $len = 5;
            $s = $i * $len;

            $sub = substr($conv_bin, $s, $len);
            $hash .= substr($base_32, base_convert($sub, 2, 10), 1);
        }

        return $hash;
    }

    /**
     * @param float $hash GeoHash
     */
    public static function geo_hash_decode($hash)
    {
        $base_32 = self::$base_32;

        $str_bin = '';
        $max_len = strlen($hash);
        for($i = 0; $i < $max_len; $i++) {
            $len = 1;
            $s = $i * $len;

            $sub = substr($hash, $s, $len);

            $str_bin .= sprintf('%05s', base_convert(strpos($base_32, $sub), 10, 2));
        }

        $lat_bin = '';
        $lon_bin = '';
        $max_len = floor(strlen($str_bin) / 2);

        for ($i = 0; $i < $max_len; $i++) {
            if (isset($str_bin[$i * 2]))
                $lon_bin .= $str_bin[$i * 2];
            if (isset($str_bin[$i * 2 + 1]))
                $lat_bin .= $str_bin[$i * 2 + 1];
        }

        return array(
            'latitude' => self::lat_decode($lat_bin),
            'longitude' => self::lon_decode($lon_bin),
        );
    }

    private static function lat_encode($lat)
    {
        $min = -90;
        $max = 90;
        $mid = 0;
        $str = '';

        for ($i = 0; $i < 20; $i++) {
            if ($lat >= $mid) {
                $str .= '1';
                $min = $mid;
                $mid = (($max - $mid) / 2) + $mid;
            } else {
                $str .= '0';
                $max = $mid;
                $mid = $mid - (($mid - $min) / 2);
            }
        }

        return $str;
    }
    private static function lon_encode($lon)
    {
        $min = -180;
        $max = 180;
        $mid = 0;
        $str = '';

        for ($i = 0; $i < 20; $i++) {
            if ($lon >= $mid) {
                $str .= '1';
                $min = $mid;
                $mid = (($max - $mid) / 2) + $mid;
            } else {
                $str .= '0';
                $max = $mid;
                $mid = $mid - (($mid - $min) / 2);
            }
        }

        return $str;
    }

    private static function lat_decode($lat_bin)
    {
        $max = 90;
        $min = -90;
        $mid = 0;

        $max_len = strlen($lat_bin);
        for($i = 0; $i < $max_len; $i++) {
            if ($lat_bin[$i] == 1) {
                $min = $mid;
                $mid = (($max - $mid) / 2) + $mid;
            } else {
                $max = $mid;
                $mid = $mid - (($mid - $min) / 2);
            }
        }
        return $mid;
    }

    private static function lon_decode($lon_bin)
    {
        $max = 180;
        $min = -180;
        $mid = 0;

        $max_len = strlen($lon_bin);
        for($i = 0; $i < $max_len; $i++) {
            if ($lon_bin[$i] == 1) {
                $min = $mid;
                $mid = (($max - $mid) / 2) + $mid;
            } else {
                $max = $mid;
                $mid = $mid - (($mid - $min) / 2);
            }
        }
        return $mid;
    }
}
