<?php
namespace Fuel\Tasks;

use Fuel\Core\Cli;

class Geo_Map
{
    private $path;
    private $col_map = array(
        'pref_code',
        'pref_name',
        'city_code',
        'city',
        'address_code',
        'address',
        'lat',
        'lon',
        'src_code',
        'address_sep_code',
    );

    public function __construct() {
        $this->path = APPPATH . 'vendor/map_data/';
    }

    public function run()
    {
        $path = $this->path;

        $count = 0;
        foreach(glob($path . '*.csv') as $file) {
            $count++;
            echo $file . "\n";
        }
        echo sprintf("%d 件のファイルが見つかりました。", $count) . "\n";
    }

    public function geo_test($len = 8)
    {
        echo $test = \My_Geohash::geo_hash_encode(35.4279936, 133.360454, $len) . "\n";
        echo \My_Geohash::geo_hash_encode("35.4279936", "133.360454", $len) . "\n";
        echo print_r(\My_Geohash::geo_hash_decode($test), true);
    }

    public function import()
    {
        echo "取込開始\n";
        $path = $this->path;

        foreach(glob($path . '*.csv') as $origin_file) {
            if (is_file($origin_file)) {
                \My_Log::info("ファイル {$origin_file} が見つかりました。");
                $tmp_path = $path . 'tmp/';
                if (!file_exists($tmp_path) && !is_dir($tmp_path))
                {
                    mkdir($tmp_path);
                }
                $tmp_content = file_get_contents($origin_file);
                $tmp_content = mb_convert_encoding($tmp_content, 'UTF-8', 'sjis-win');
                $tmp_content = preg_replace(array("/\r\n/", "/\r/"), "\n", $tmp_content);
                $file = $tmp_path . basename($origin_file);
                file_put_contents($file, $tmp_content);

                $result = array(
                    'success' => 0,
                    'error' => 0,
                    'message' => '',
                );
                if (($fp = fopen($file, "r")) !== false) {
                    $count = 0;
                    while (($line = fgetcsv($fp, 1000, ",")) !== false) {
                        $count++;
                        if ($count == 1) {
                            continue;
                        }

                        $data = $this->populate($line);

                        $geo_map = \Model_Geomap::find('first', array(
                            'where' => array(
                                'lat' => round($data['lat'], 6),
                                'lon' => round($data['lon'], 6),
                            ),
                        ));
                        if (!$geo_map) {
                            $geo_map = new \Model_Geomap();
                        }

                        $geo_map->pref_code = $data['pref_code'];
                        $geo_map->pref = $data['pref_name'];
                        $geo_map->city = $data['city'];
                        $geo_map->address = $data['address'];
                        $geo_map->lat = $data['lat'];
                        $geo_map->lon = $data['lon'];
                        $geo_map->geo_hash = $data['geo_hash'];

                        if ($geo_map->save()) {
                            $result['success'] += 1;
                        } else {
                            $result['error'] += 1;
                            $result['message'][] = sprintf("Import error: %s", $data['address_code']);

                        }
                    }
                    fclose($fp);
                    @unlink($file);
                    $res = sprintf("ファイル: %s [count: %d, success: %s, error: %s]", basename($file), $count, $result['success'], $result['error']);
                    \My_Log::info($res);
                    if ($result['message']) {
                        \My_Log::info(print_r($result['message']));
                    }
                    echo $res . "\n";
                }
            }
        }
        echo "取込終了\n";
    }

    private function populate($line)
    {
        $data = array();

        foreach ($this->col_map as $key => $value) {
            $data[$value] = $line[$key];
        }

        $data['geo_hash'] = \My_Geohash::geo_hash_encode($data['lat'], $data['lon'], 8);

        return $data;
    }
}