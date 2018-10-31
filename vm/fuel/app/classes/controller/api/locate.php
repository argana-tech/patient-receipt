<?php

/**
 * 患者受付アプリ
 */
class Controller_Api_Locate extends Controller_Base
{

    /**
     * 位置情報集計
     */
    public function action_add_beacon()
    {
        $this->format = 'json';

        try {

            $result = true;

            $validate = \Validation::forge();
            $validate->add('unique_id', 'unique_id')
                ->add_rule('required')
                ->add_rule('exact_length', 14)
            ;

            // \Log::debug(print_r(\Input::post(), true));

            if ($validate->run()) {
                $unique_id = \Input::post('unique_id');
                $beacon_locations = \Input::post('beacon_locations');

                $terminal = Model_Terminal::find('first', array(
                    'where' => array(
                        'unique_id' => $unique_id,
                    ),
                    'order_by' => array(
                        'updated_at' => 'desc',
                        'created_at' => 'desc',
                    ),
                ));

                if ($terminal) {
                    $locations_id = array();
                    $beacon_location = array();
                    if (is_array($beacon_locations) && count($beacon_locations) > 0) {
                        $beacon_location = Model_Location_Beacon::aggregate($beacon_locations);

                        foreach($beacon_locations as $value) {
                            $location = new Model_Location_Beacon(array(
                                'unique_id' => $unique_id,
                                'beacon_name' => $value['name'],
                                'radio_strength' => $value['level'],
                                'time' => $value['time'],
                                // TODO: beacon_name と院内市のmap処理
                                'address' => ($beacon_location && $beacon_location['beacon_name']) ? $beacon_location['beacon_name'] : null,
                            ));
                            $location->save();
                        }
                    } else {
                        \Log::info('Add beacon locations - no data');
                    }

                } else {
                    \Log::debug('Add beacon terminal not found');
                }
            } else {
                \Log::debug('Add beacon validation error');
            }

        } catch (\Exception $e) {
            \Log::error('Add beacon ' . $e->getMessage());
            $result = false;
        }

        return $this->response(array(
            'result' => $result,
        ));
    }

    public function action_add_geo()
    {
        $this->format = 'json';
        $result = true;

        try {

            $validate = \Validation::forge();
            $validate->add('unique_id', 'unique_id')
                ->add_rule('required')
                ->add_rule('exact_length', 14)
            ;

            if ($validate->run()) {
                $unique_id = \Input::post('unique_id');
                $geo_locations = \Input::post('geo_locations');

                $terminal = Model_Terminal::find('first', array(
                    'where' => array(
                        'unique_id' => $unique_id,
                    ),
                    'order_by' => array(
                        'updated_at' => 'desc',
                        'created_at' => 'desc',
                    ),
                ));

                if ($terminal) {
                    $locations_id = array();
                    $geo_location = array();
                    if (is_array($geo_locations) && isset($geo_locations['lat'], $geo_locations['lon'])) {
                        $hash = \My_Geohash::geo_hash_encode($geo_locations['lat'], $geo_locations['lon']);
                        $geo_map_query = Model_Geomap::query()
                            ->and_where_open()
                                ->where('geo_hash', 'like', "{$hash}%")
                                ->or_where_open()
                                    ->where('geo_hash', 'not like', "{$hash}%")
                                    ->where('geo_hash', 'like', substr($hash, 0, strlen($hash) - 1) . '%')
                                ->or_where_close()
                                ->or_where_open()
                                    ->where('geo_hash', 'not like', "{$hash}%")
                                    ->where('geo_hash', 'not like', substr($hash, 0, strlen($hash) - 1) . '%')
                                    ->where('geo_hash', 'like', substr($hash, 0, strlen($hash) - 2) . '%')
                                ->or_where_close()
                            ->and_where_close()
                            ->order_by(\DB::expr('CHAR_LENGTH(geo_hash)', 'hash_length'), 'desc')
                            ;

                        $geo_map = $geo_map_query->get_one();

                        // \Log::debug(\DB::last_query());
                        $address = 'エリア外';
                        if ($geo_map) {
                            $address = $geo_map->get_address();
                        }

                        $location = new Model_Location_Geo(array(
                            'unique_id' => $unique_id,
                            'latitude' => $geo_locations['lat'],
                            'longitude' => $geo_locations['lon'],
                            'time' => date('Y-m-d H:i:s'),
                            'address' => $address,
                        ));
                        if (!$location->save()) {
                            \Log::error('Fail save geo data [' . $unique_id . ']');
                            $result = false;
                        }

                    } else {
                        \Log::info('Add geo locations - no data [' . $unique_id . ']');
                    }

                } else {
                    \Log::debug('Add geo terminal not found');
                }
            } else {
                \Log::debug('Add geo validation error');
            }

        } catch (\Exception $e) {
            \Log::error('Add geo ' . $e->getMessage());
            $result = false;
        }

        return $this->response(array(
            'result' => $result,
        ));
    }
}
