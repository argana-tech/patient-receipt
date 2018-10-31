<?php

class Controller_Api_Check extends Controller_Base
{
    public function action_register()
    {
        $this->format = 'json';

        try {

            $config_messages = \Config::get('my.messages.check.register');
            $result = array();

            $validate = \Validation::forge();
            $validate->add('unique_id', 'unique_id')
                ->add_rule('required')
                ->add_rule('exact_length', 14)
            ;

            if ($validate->run()) {
                $unique_id = \Input::post('unique_id');

                $terminal = Model_Terminal::find('first', array(
                    'where' => array(
                        'unique_id' => $unique_id,
                    ),
                ));

                $errors = array();
                if ($terminal) {
                    if (empty($terminal->device_token)) {
                        $errors[] = 'デバイストークンが登録されていません。';
                    }
                } else {
                    $errors[] = '端末が登録されていません。';
                }

                if (count($errors) > 0) {
                    Log::info("check/register error:" . serialize($errors));
                    $result = array(
                        'result' => true,
                        'message' => \Arr::get($config_messages, 'before'),
                        'check_flag' => true,
                        'button' => '登録',
                    );
                } else {
                    $result = array(
                        'result' => true,
                        'message' => sprintf(
                            "%s前回の登録日:%s",
                            \Arr::get($config_messages, 'after', ''),
                            date('Y/m/d', strtotime($terminal->created_at))
                        ),
                        'check_flag' => true,
                        'button' => '再登録',
                    );
                }
            } else {
                \Log::info('check/register validation error: ' . serialize(\Input::post()));
                $result = array(
                    'result' => true,
                    'message' => \Arr::get($config_messages, 'before'),
                    'check_flag' => true,
                    'button' => '登録',
                );
            }

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $result = array(
                'result' => false,
                'check_flag' => true,
                'button' => '登録',
                'message' => \Config::get('my.messages.check.register.before')
            );
        }

        return $this->response($result);
    }

    public function action_time()
    {
        $this->format = 'json';

        try {

            $now = time();

            // メッセージ初期設定
            $message = '';
            $button = '受付';

            $validate = \Validation::forge();
            $validate->add('unique_id', 'unique_id')
                ->add_rule('required')
                ->add_rule('exact_length', 14)
            ;

            if (!$validate->run()) {
                $message = \Config::get('my.messages.check.receipt.before');
                if (!\My_Validreceipt::is_release($now)) {
                    $message = sprintf(
                        \Config::get('my.messages.check.receipt.before_release'),
                        str_replace('-', '/', \Config::get('my.release'))
                    );
                }
                $check_flag = false;
            } else {

                $unique_id = \Input::post('unique_id');

                $tmp_result = array();

                // 距離
                // $tmp_result['distance'] = \My_Validreceipt::is_distance($unique_id, $now);
                if ($unique_id && \My_Validreceipt::is_distance($unique_id, $now)) {
                    $tmp_result['distance'] = true;
                } else {
                    $tmp_result['distance'] = false;
                    $message = \Config::get('my.messages.check.receipt.out_area');
                }

                // 受付時間
                // $tmp_result['time'] = \My_Validreceipt::is_receipt_time($now);
                if (\My_Validreceipt::is_receipt_time($now)) {
                    $tmp_result['time'] = true;
                } else {
                    $tmp_result['time'] = false;
                    $message = \Config::get('my.messages.check.receipt.out_time');
                }

                // release
                // $tmp_result['release'] = \My_Validreceipt::is_release($now);
                if (\My_Validreceipt::is_release($now)) {
                    $tmp_result['release'] = true;
                } else {
                    $tmp_result['release'] = false;
                    $message = sprintf(
                        \Config::get('my.messages.check.receipt.before_release'),
                        str_replace('-', '/', \Config::get('my.release'))
                    );
                }

                // 受付クリック
                // $tmp_result['receipted'] = \My_Validreceipt::is_receipted($unique_id, $now, $message);
                $receipt = Model_Receipt::find('first', array(
                    'where' => array(
                        'unique_id' => $unique_id,
                        'date' => date('Y-m-d', $now),
                    ),
                    'order_by' => array(
                        'created_at' => 'desc',
                    ),
                ));
                if ($unique_id && $receipt && $receipt->status == 1) {
                    $message = \Config::get('my.messages.check.receipt.now');
                    $button = '受付中';
                    $tmp_result['receipted'] = true;
                } elseif ($unique_id && $receipt && $receipt->status == 2) {
                    $message = sprintf(
                        "%s\n受付番号:%s",
                        \Config::get('my.messages.check.receipt.after'),
                        $receipt->receipt_number
                    );
                    $button = '受付済';
                    $tmp_result['receipted'] = true;
                } else {
                    $tmp_result['receipted'] = false;
                }

                $check_flag = false;
                // $result = false;
                if ($tmp_result) {
                    // $result = true;
                    $check_flag = true;
                    foreach($tmp_result as $key => $value) {
                        if ($key == 'receipted') {
                            if ($value) {
                                $check_flag = false;
                            }
                        } elseif (!$value) {
                            $check_flag = false;
                        }
                    }
                }
            }
            if (!$message) {
                $message = \Config::get('my.messages.check.receipt.before');
            }
            \Log::debug('受付チェックフラグ: ' . $check_flag);

            $result = array(
                'result' => true,
                'message' => $message,
                'button' => $button,
                'check_flag' => $check_flag,
            );

        } catch (\Exception $e) {
            \Log::error('check time ' . $e->getMessage());
            $result = array(
                'result' => false,
                'message' => \Config::get('my.messages.check.receipt.before'),
                'button' => '受付',
                'check_flag' => true,
            );
        }
        return $this->response($result);
    }
}
