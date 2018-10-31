<?php

/**
 * 患者受付アプリ
 */
class Controller_Api_Receipt extends Controller_Base
{

    /**
     * 端末登録
     */
    public function action_register()
    {
        $this->format = 'json';

        try {

            $validate = \Validation::forge();
            $validate->add('unique_id', 'unique_id')
                ->add_rule('required')
                ->add_rule('exact_length', 14)
            ;
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
                return $this->response(array(
                    'result' => true,
                    'message' => \Config::get('my.messages.receipt.register.error'),
                ));
            }

            if ($validate->run()) {
                $unique_id = \Input::post('unique_id');
                $device_token = \Input::post('device_token');

                \DB::start_transaction();

                $terminal = Model_Terminal::find('first', array(
                    'where' => array(
                        'unique_id' => $unique_id,
                    ),
                ));

                if (!$terminal) {
                    $terminal = new Model_Terminal(array(
                        'unique_id' => $unique_id,
                        'device_token' => $device_token,
                        'platform' => $platform,
                        'application_type' => Model_Terminal::APP_TYPE_PATIENT_RECEIPT,
                    ));
                } else {
                    $terminal->device_token = $device_token;
                }

                if ($terminal->save()) {
                    $result['result'] = true;
                    $result['message'] = \Config::get('my.messages.receipt.register.success');
                    $result['created_at'] = sprintf(
                        "%s%s",
                        "前回登録日: ",
                        date('Y/m/d', strtotime($terminal->created_at))
                    );
                    \DB::commit_transaction();
                } else {
                    $result['result'] = false;
                    $result['message'] = \Config::get('my.messages.receipt.register.error');
                    \DB::rollback_transaction();
                }
            } else {
                $result = array(
                    'result' => true,
                    'message' => \Config::get('my.messages.receipt.register.error'),
                );
            }

        } catch (\Exception $e) {
            \Log::error('receipt register ' . $e->getMessage());
            $result = array(
                'result' => false,
                'message' => \Config::get('my.messages.receipt.register.error'),
            );
        }

        return $this->response($result);
    }

    public function action_entry()
    {
        $this->format = 'json';

        try {

            $validate = \Validation::forge();
            $validate->add('unique_id', 'unique_id')
                ->add_rule('required')
                ->add_rule('exact_length', 14)
            ;

            $result = array(
                'result' => false,
                'check_flag' => true,
            );

            if ($validate->run()) {
                $unique_id = \Input::post('unique_id');

                if (!\My_Validreceipt::is_release()) {
                    $result['check_flag'] = false;
                    $result['message'] = sprintf(
                        \Config::get('my.messages.check.receipt.before_release'),
                        str_replace('-', '/', \Config::get('my.release'))
                    );
                } elseif (!\My_Validreceipt::is_receipt_time()) {
                    $result['check_flag'] = false;
                    $result['message'] = \Config::get('my.messages.check.receipt.out_time');
                } elseif (\My_Validreceipt::is_receipted($unique_id, null, $message)) {
                    $result['check_flag'] = false;
                    $result['message'] = "受付済みです。";
                    if ($message) {
                        $result['message'] = $message;
                    }
                } elseif (!\My_Validreceipt::is_distance($unique_id)) {
                    $result['check_flag'] = false;
                    $result['message'] = \Config::get('my.messages.check.receipt.out_area');
                } else {

                    \DB::start_transaction();

                    $terminal = Model_Terminal::find('first', array(
                        'where' => array(
                            'unique_id' => $unique_id,
                        ),
                        'order_by' => array(
                            'updated_at' => 'desc',
                            'created_at' => 'desc',
                        ),
                    ));

                    $date = date('Y-m-d');
                    $receipt = Model_Receipt::find('first', array(
                        'where' => array(
                            'unique_id' => $unique_id,
                            'date' => $date,
                        ),
                    ));
                    if (!$receipt) {
                        $receipt = new Model_Receipt(array(
                            'unique_id' => $unique_id,
                            'date' => $date,
                            'status' => Model_Receipt::STATUS_RECEIPTING,
                        ));
                    }

                    if ($receipt->save()) {
                        $result['check_flag'] = false;
                        $result['message'] = \Config::get('my.messages.receipt.entry.success');
                        \DB::commit_transaction();
                    } else {
                        $result['result'] = false;
                        $result['check_flag'] = true;
                        $result['message'] = \Config::get('my.messages.receipt.entry.error');
                        \DB::rollback_transaction();
                    }

                }
            } else {
                $result = array(
                    'result' => true,
                    'check_flag' => true,
                    'message' => \Config::get('my.messages.receipt.entry.error'),
                );
            }

        } catch (\Exception $e) {
            \Log::error('receipt entry ' . $e->getMessage());
            $result = array(
                'result' => false,
                'check_flag' => true,
                'message' => \Config::get('my.messages.receipt.entry.error'),
            );
        }

        return $this->response($result);
    }
}
