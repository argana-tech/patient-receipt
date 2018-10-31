<?php

class Controller_Api_App extends Controller_Base
{
    private $unique_id_prefix_format = 'ymd';
    private $unique_id_number_format = "%08d";
    private $unique_id_number_length = 8;

    public function action_register()
    {
        $this->format = 'json';

        $validate = \Validation::forge();
        // $validate->add('unique_id', 'unique_id')
        //     ->add_rule('required')
        //     ->add_rule('exact_length', 14)
        // ;
        $validate->add('device_token', 'device_token')
            ->add_rule('required')
        ;
        $validate->add('platform', 'platform')
            ->add_rule('required')
        ;

        $result = array(
            'result' => false,
        );
        $platform = \Input::post('platform', false);

        if (!Model_Terminal::is_usable_platform($platform) || \Input::method() !== 'POST') {
            return Response::forge(View::forge('error/404'), 404);
        }

        if ($validate->run()) {
            // $unique_id = \Input::post('unique_id');
            $device_token = \Input::post('device_token');

            $unique_id_prefix = date($this->unique_id_prefix_format);

            \DB::start_transaction();
            $exist_terminal = Model_Terminal::find('first', array(
                'where' => array(
                    array('unique_id', 'like', $unique_id_prefix.'%'),
                ),
                'order_by' => array(
                    'unique_id' => 'desc',
                ),
            ));
            if ($exist_terminal) {
                $unique_id_number = sprintf($this->unique_id_number_format, intval(substr($exist_terminal->unique_id, strlen($unique_id_prefix), $this->unique_id_number_length)) + 1);
            } else {
                $unique_id_number = sprintf($this->unique_id_number_format, 1);
            }
            $unique_id = $unique_id_prefix . $unique_id_number;

            $terminal = new Model_Terminal(array(
                'unique_id' => $unique_id,
                'device_token' => $device_token,
                'platform' => $platform,
            ));

            if ($terminal->save()) {
                $result['result'] = true;
                $result['message'] = \Config::get('my.messages.check.register.success');
                $result['unique_id'] = $terminal->unique_id;
                \DB::commit_transaction();
            } else {
                $result['result'] = false;
                $result['message'] = \Config::get('my.messages.check.register.error');
                $result['unique_id'] = '';
                \DB::rollback_transaction();
            }
        } else {
            $result = array(
                'result' => false,
                'error' => \Config::get('my.messages.check.register.error'),
                'unique_id' => '',
            );
        }

        return $this->response($result);
    }
}
