<?php

class Controller_Api_Settings extends Controller_Base
{
    public function action_property()
    {
        $this->format = 'json';
        try {

            $result = \My_Location::get_aggregate_info();
            $result['active_receipt_time'] = \Config::get('my.receipt_time');
            $result['menu_check_span'] = \Config::get('my.menu_check_span', 60);
            $result['beacon_uuid_list'] = \My_Location::get_beacon_uuid_list();
            $result['result'] = true;

        } catch (\Exception $e) {
            \Log::error('settings property ' . $e->getMessage());
            $result = array(
                'result' => false,
            );
        }
        return $this->response($result);
    }
}
