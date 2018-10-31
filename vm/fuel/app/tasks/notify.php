<?php
namespace Fuel\Tasks;

use Fuel\Core\Cli;

class Notify
{
    public function run()
    {
        \My_Log::debug('Task notify start');
        // ファイル確認
        $dir = \Config::get('my.shared_directory.dir');
        $categories = \Config::get('my.shared_categories');
        foreach ($categories as $category) {
            $this->check_shared_directory($dir . $category . '/');
        }

        // 通知処理
        $this->send_notify();
        return 0;
    }

    /**
     * 共有ディレクトリチェック
     */
    private function check_shared_directory($dir)
    {
        chmod($dir, 0777);
        $index_dir = $dir . \Config::get('my.shared_directory.index') . '/';
        $data_dir = $dir . \Config::get('my.shared_directory.data') . '/';

        // if (!is_dir($index_dir)) {
        //     $this->output_log($index_dir . " ディレクトリが見つかりません", $dir, false);
        //     return ;
        // }

        // if (!is_dir($data_dir)) {
        //     $this->output_log($data_dir . " ディレクトリが見つかりません", $dir, false);
        //     return ;
        // }

        $shared_files = array();

        $files = scandir($index_dir);
        $excepts = array(
            '.', '..',
            \Config::get('my.shared_directory.ok'),
            \Config::get('my.shared_directory.ng'),
        );
        foreach ($files as $file) {
            $status = true;
            $errors = array();
            if (in_array($file, $excepts)) continue;

            $index_file = $index_dir . $file;
            $data_file = $data_dir . $file;

            if (!is_dir($index_file)) {
                if (!is_readable($index_file)) {
                    $errors[] = "index - ファイルが見つかりません({$index_file})。";
                    $status &= false;
                }
            } else {
                $errors[] = "index - ディレクトリが指定されています({$index_file})。";
                $status &= false;
            }

            $body = array();
            if (!is_dir($data_file)) {
                if (!is_readable($data_file)) {
                    $errors[] = "data - ファイルが見つかりません({$index_file})。";
                    $status &= false;
                } else {
                    $contents = file_get_contents($data_file);
                    $body['unique_id'] = substr($contents, \Config::get('my.denbun.call.unique_id.start'), \Config::get('my.denbun.call.unique_id.length'));
                    $body['message'] = trim(
                        mb_convert_encoding(
                            substr($contents, \Config::get('my.denbun.call.message.start'), \Config::get('my.denbun.call.message.length')),
                            'utf-8',
                            'sjis-win'
                        )
                    );
                    $body['reserve_date'] = substr($contents, \Config::get('my.denbun.call.reserve_date.start'), \Config::get('my.denbun.call.reserve_date.length'));
                    $body['reserve_type'] = substr($contents, \Config::get('my.denbun.call.reserve_type.start'), \Config::get('my.denbun.call.reserve_type.length'));
                    $body['reserve_items'] = substr($contents, \Config::get('my.denbun.call.reserve_items.start'), \Config::get('my.denbun.call.reserve_items.length'));
                    $body['room_number'] = substr($contents, \Config::get('my.denbun.call.room_number.start'), \Config::get('my.denbun.call.room_number.length'));
                    $body['reserve_tantou'] = substr($contents, \Config::get('my.denbun.call.reserve_tantou.start'), \Config::get('my.denbun.call.reserve_tantou.length'));
                    $body['reserve_start_time'] = substr($contents, \Config::get('my.denbun.call.reserve_start_time.start'), \Config::get('my.denbun.call.reserve_start_time.length'));
                    $body['hojoka'] = substr($contents, \Config::get('my.denbun.call.hojoka.start'), \Config::get('my.denbun.call.hojoka.length'));
                }
            } else {
                $errors[] = "data - ディレクトリが指定されています({$index_file})。";
                $status &= false;
            }

            $skip_file_handle = false;
            if ($status) {
                $terminal = \Model_Terminal::find('first', array(
                    'where' => array(
                        'unique_id' => $body['unique_id'],
                    ),
                ));

                if ($terminal) {
                    try {
                        \DB::start_transaction();
                        $original_call = new \Model_Originalcall($body);
                        $original_call->save();

                        $notification = new \Model_Notification(array(
                            'unique_id' => $body['unique_id'],
                            'original_call_id' => $original_call->id,
                            'device_token' => $terminal->device_token,
                            'platform' => $terminal->platform,
                            'application_type' => $terminal->application_type,
                            'body' => str_replace('//', "\n", $body['message']),
                            'filename' => $index_file,
                        ));
                        $notification->save();

                        $query = \Model_Notification::query()
                            ->where(array(
                                'unique_id' => $body['unique_id'],
                                'filename' => $index_file,
                            ))
                        ;

                        if ($query->count() > 1) {
                            $skip_file_handle = true;
                            \DB::rollback_transaction();
                        } else {
                            \DB::commit_transaction();
                        }
                    } catch (Exception $e) {
                        $skip_file_handle = true;
                        \DB::rollback_transaction();
                        \My_Log::info($e);
                    }
                } else {
                    $errors[] = "該当する端末情報が見つかりませんでした。";
                    $status &= false;
                }
            }

            if ($skip_file_handle) {
                // $this->output_log('すでに取り込まれたファイルが見つかりました。[' . $index_file . ']', $dir, $file, false);
                // continue;
                $status = false;
                $errors = array('すでに取り込まれたファイルが見つかりました。');
            }

            if ($status) {
                $index_ok_dir = $index_dir . \Config::get('my.shared_directory.ok') . '/';
                $data_ok_dir = $data_dir . \Config::get('my.shared_directory.ok') . '/';
                if (!file_exists($index_ok_dir) || !is_dir($index_ok_dir)) {
                    @mkdir($index_ok_dir, 0777, true);
                }
                if (!file_exists($data_ok_dir) || !is_dir($data_ok_dir)) {
                    @mkdir($data_ok_dir, 0777, true);
                }

                chmod($index_file, 0777);
                chmod($index_ok_dir, 0777);
                chmod($data_file, 0777);
                chmod($data_ok_dir, 0777);
                @rename($index_file, $index_ok_dir . $file);
                @rename($data_file, $data_ok_dir . $file);
                $this->output_log("正常完了", $dir, $file, true);
            } else {
                $index_ng_dir = $index_dir . \Config::get('my.shared_directory.ng') . '/';
                $data_ng_dir = $data_dir . \Config::get('my.shared_directory.ng') . '/';
                if (!file_exists($index_ng_dir) || !is_dir($index_ng_dir)) {
                    @mkdir($index_ng_dir, 0777, true);
                }
                if (!file_exists($data_ng_dir) || !is_dir($data_ng_dir)) {
                    @mkdir($data_ng_dir, 0777, true);
                }

                chmod($index_file, 0777);
                chmod($index_ng_dir, 0777);
                chmod($data_file, 0777);
                chmod($data_ng_dir, 0777);
                @rename($index_file, $index_ng_dir . $file);
                @rename($data_file, $data_ng_dir . $file);
                $this->output_log(implode(",", $errors), $dir, $file, false);
            }
        }

    }

    /**
     * 共有ディレクトリ用 ログ出力
     */
    private function output_log($msg, $dir, $filename, $isOk = true)
    {
        $file_dir = sprintf(
            '%s/%s/%s/',
            rtrim($dir, '/'),
            \Config::get('my.shared_directory.log'),
            ($isOk) ? \Config::get('my.shared_directory.ok', false) : \Config::get('my.shared_directory.ng', false)
        );
        if (!file_exists($file_dir) || !is_dir($file_dir)) {
            @mkdir($file_dir, 0777, true);
        }
        $filepath = sprintf(
            '%s%s',
            $file_dir,
            $filename
        );
        $msg = mb_convert_encoding(
            $msg,
            'sjis-win',
            'utf-8'
        );
        file_put_contents($filepath, "[" . date('Y-m-d H:i:s') . "] " . $msg . "\r\n", FILE_APPEND);
    }

    /**
     * プッシュ通知
     */
    private function send_notify()
    {
        // \My_Log::info("send_notify");
        $notifications = \Model_Notification::find('all', array(
            'where' => array(
                'send_at' => null,
                'send_status' => false,
                'status' => \Model_Notification::STATUS_NEW,
            ),
        ));

        foreach ($notifications as $notification) {
            $notification->status = \Model_Notification::STATUS_SENDING;
            $notification->save();
        }

        $this->run_notify($notifications);
    }

    /**
     * プッシュ通知リトライ
     */
    public function resend_notify()
    {
        // \My_Log::info("resend_notify");
        $notifications = \Model_Notification::find('all', array(
            'where' => array(
                array('send_at', '!=', null),
                'send_status' => false,
                'status' => \Model_Notification::STATUS_WAIT_RETRY,
            ),
        ));

        foreach ($notifications as $notification) {
            $notification->status = \Model_Notification::STATUS_SENDING;
            $notification->save();
        }

        $this->run_notify($notifications, 'resend_notify');
    }

    private function run_notify($notifications, $referer = 'send_notify')
    {
        // \My_Log::info("run notify");
        if ($notifications) {
            \My_Log::info("count notifications({$referer}): " . count($notifications));

            foreach ($notifications as $notification) {
                if (empty($notification->device_token)) {
                    \My_Log::info("Not found device_token.[id:{$notification->id}]");
                    continue;
                }

                $body = str_replace("\n", " ", trim($notification->body));

                switch ($notification->get_platform()) {
                    case 'ios':
                        $data = array(
                            "priority" => "high",
                            'to' => $notification->device_token,
                            'notification' => array(
                                "content_available" => "true",
                                'sound' => 'default',
                                'title' => '呼出のお知らせ',
                                'body' => "{$body}",
                            ),
                            'data' => array(
                                'title' => '呼出のお知らせ',
                                'text' => "{$body}",
                            ),
                        );
                        break;

                    case 'android':
                        $data = array(
                            "priority" => "high",
                            'to' => $notification->device_token,
                            'data' => array(
                                "id" => $notification->id,
                                'title' => '呼出のお知らせ',
                                'text' => "{$body}",
                                "notification_id" => $notification->id,
                            ),
                        );
                        break;
                    default:
                        $data = array(
                            "priority" => "high",
                            'to' => $notification->device_token,
                            'notification' => array(
                                'title' => '呼出のお知らせ',
                                'body' => "{$body}",
                            ),
                        );
                        break;
                }

                if (\Config::get('request_test', false)) {
                    $data['dry_run'] = true; // request test
                }

                if (!$this->curl_request($data, $notification->application_type, $error)) {
                    $notification->send_status = false;
                    $notification->status = \Model_Notification::STATUS_WAIT_RETRY;
                    \My_Log::info("$error [id:{$notification->id}]");
                } else {
                    \My_Log::debug("[id:{$notification->id}]");
                    $notification->send_status = true;
                    $notification->status = \Model_Notification::STATUS_SENDED;
                }
                $notification->try_numbers = $notification->try_numbers + 1;
                $notification->send_at = date('Y-m-d H:i:s');
                $notification->save();
            }
        }

    }

    /**
     * firebaseへのリクエスト
     */
    private function curl_request($data, $app_type = 0, &$error = '')
    {
        \My_Log::debug(json_encode($data));
        $ch = curl_init(\Config::get('my.notifier.url'));
        $app_type_name = \Model_Terminal::get_application_type_name($app_type);
        $api_key = \Config::get('my.notifier.api_key.' . $app_type_name);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER ,array(
            'Content-Type:application/json',
            'Authorization:key=' . $api_key,
        ));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        if ($curl_errno > 0) {
            $error = $curl_error;
            $status = false;
        } else {
            $info = curl_getinfo($ch);
            $json = null;
            if ($this->is_json($response)) {
                $json = json_decode($response);
            }
            switch ($info = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case '200':
                    $status = true;
                    \My_Log::debug('response ' . $response);
                    break;

                default:
                    $status = false;
                    if (
                        $json
                        && isset($json->error)
                        && isset($json->error->code)
                        && isset($json->error->message)
                    ) {
                        $error = "response error - [code: {$json->error->code}] {$json->error->message}";
                    } else {
                        $error = "response error - [code: {$info}] {$response}";
                    }
                    break;
            }
        }

        curl_close($ch);
        return $status;
    }

    public function is_json($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}