<?php

/**
 * 患者受付アプリ
 */
class Controller_Api_Notification extends Controller_Base
{

    /**
     * 既読処理
     */
    public function action_read()
    {
        $this->format = 'json';

        try {

            $validate = \Validation::forge();
            $validate->add('unique_id', 'unique_id')
                ->add_rule('required')
                ->add_rule('exact_length', 14)
            ;

            $result = true;

            if ($validate->run()) {
                $unique_id = \Input::post('unique_id');

                \DB::start_transaction();

                $notification = Model_Notification::find('first', array(
                    'where' => array(
                        'unique_id' => $unique_id,
                        'send_status' => true,
                        'is_read' => false,
                    ),
                ));

                if ($notification) {
                    $notification->is_read = true;

                    if ($notification->save()) {
                        // $result['result'] = true;
                        // $result['message'] = '登録しました';
                        \Log::error('notification read saved.');
                        \DB::commit_transaction();
                    } else {
                        $result = false;
                        \Log::error('notification read 既読処理が行なえませんでした。');
                        \DB::rollback_transaction();
                    }
                } else {
                    \Log::info('notification read 未読通知データがありませんでした。');
                    \DB::rollback_transaction();
                }
            } else {
                \Log::info('notification read validation error');
            }

        } catch (\Exception $e) {
            \Log::error('notification read ' . $e->getMessage());
            $result = false;
        }

        return $this->response(array(
            'result' => $result,
        ));
    }
}
