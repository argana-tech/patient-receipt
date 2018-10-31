<?php
namespace Fuel\Tasks;

use Fuel\Core\Cli;

class Linked
{
    public function run()
    {
        echo "位置情報 linked:locate\n";
        echo "既読処理 linked:read\n";
        echo "受付処理 linked:receipt\n";
        echo "受付済確認処理 linked:receipted\n";
    }

    private function get_system_directory()
    {
        $shared_dir = \Config::get('my.shared_directory.dir');
        return rtrim($shared_dir, '/') . '/';
    }

    private function check_create_dir($path) {
        if (!file_exists($path) && !is_dir($path)) {
            @mkdir($path, 0777, true);
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
     * 位置情報 出力
     */
    public function locate()
    {
        $sys_dir = $this->get_system_directory();
        $locate_path = $sys_dir . \Config::get('my.shared_system_directories.locate');
        $locate_index_path = $locate_path . '/' . \Config::get('my.shared_directory.index');
        $locate_data_path = $locate_path . '/' . \Config::get('my.shared_directory.data');
        $ok = \Config::get('my.shared_directory.ok');
        $ng = \Config::get('my.shared_directory.ng');
        $log = \Config::get('my.shared_directory.log');

        $this->check_create_dir($locate_path);
        $this->check_create_dir($locate_path . '/' . $log);
        $this->check_create_dir($locate_index_path);
        $this->check_create_dir($locate_index_path . '/' . $ok);
        $this->check_create_dir($locate_index_path . '/' . $ng);
        $this->check_create_dir($locate_data_path);
        $this->check_create_dir($locate_data_path . '/' . $ok);
        $this->check_create_dir($locate_data_path . '/' . $ng);

        $receipts = \Model_Receipt::find('all', array(
            'where' => array(
                'date' => date('Y-m-d'),
                array('status', '>', \Model_Receipt::STATUS_BEFORE_RECEIPT),
            ),
        ));
        $list_receipted_unique_ids = array();
        if ($receipts && count($receipts) > 0) {
            foreach($receipts as $receipt) {
                $list_receipted_unique_ids[] = $receipt->unique_id;
            }
        } else {
            \My_Log::debug('locate - nodata');
            return;
        }

        $beacon_locations = array();
        $geo_locations = array();
        $time = time();
        $is_create_file = false;

        foreach($list_receipted_unique_ids as $unique_id) {

            $options = array(
                'where' => array(
                    array('created_at', '>=', date('Y-m-d 00:00:00', $time)),
                    array('unique_id', '=', $unique_id),
                    array('send_flag', '=', false),
                ),
                'order_by' => array(
                    'created_at' => 'desc',
                ),
            );

            $beacon_location = \Model_Location_Beacon::find('first', $options);
            $geo_location = \Model_Location_Geo::find('first', $options);
            $location = null;

            if ($beacon_location && $geo_location) {
                if ($beacon_location->created_at > $geo_location->created_at) {
                    $location = $beacon_location;
                } else {
                    $location = $geo_location;
                }
            } elseif ($beacon_location) {
                $location = $beacon_location;
            } elseif ($geo_location) {
                $location = $geo_location;
            }

            if ($location) {
                // 集計したGPS分のフラグ
                $geo_update_query = \DB::update('geo_locations');
                $geo_update_query->set(array(
                    'send_flag' => true,
                ))
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', $time))
                    ->where('created_at', '<=', date('Y-m-d H:i:s'), $time)
                    ->where('unique_id', '=', $unique_id)
                    ->where('send_flag', '=', false)
                    ->execute();

                // 集計したビーコン分のフラグ
                $beacon_update_query = \DB::update('beacon_locations');
                $beacon_update_query->set(array(
                    'send_flag' => true,
                ))
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', $time))
                    ->where('created_at', '<=', date('Y-m-d H:i:s'), $time)
                    ->where('unique_id', '=', $unique_id)
                    ->where('send_flag', '=', false)
                    ->execute();

                \My_Log::debug(print_r($location, true));
                $data = sprintf("%s%s", str_pad($location->unique_id, 14, 0, STR_PAD_LEFT), str_pad(mb_convert_encoding($location->address, 'sjis-win', 'utf-8'), 1024));
                file_put_contents($locate_data_path . '/ICHI' . date('YmdHis', $time) . '.dat', $data . "\r\n", FILE_APPEND);
                $is_create_file = true;
            }
        }

        if ($is_create_file) {
            file_put_contents($locate_index_path . '/ICHI' . date('YmdHis', $time) . '.dat', '');
        }
    }

    /**
     * 既読 出力
     */
    public function read()
    {
        $sys_dir = $this->get_system_directory();
        $read_path = $sys_dir . \Config::get('my.shared_system_directories.read');
        $read_index_path = $read_path . '/' . \Config::get('my.shared_directory.index');
        $read_data_path = $read_path . '/' . \Config::get('my.shared_directory.data');
        $ok = \Config::get('my.shared_directory.ok');
        $ng = \Config::get('my.shared_directory.ng');
        $log = \Config::get('my.shared_directory.log');

        $this->check_create_dir($read_path);
        $this->check_create_dir($read_path . '/' . $log);
        $this->check_create_dir($read_index_path);
        $this->check_create_dir($read_index_path . '/' . $ok);
        $this->check_create_dir($read_index_path . '/' . $ng);
        $this->check_create_dir($read_data_path);
        $this->check_create_dir($read_data_path . '/' . $ok);
        $this->check_create_dir($read_data_path . '/' . $ng);

        $receipts = \Model_Receipt::find('all', array(
            'where' => array(
                'date' => date('Y-m-d'),
                array('status', '>', \Model_Receipt::STATUS_BEFORE_RECEIPT),
            ),
        ));
        $list_receipted_unique_ids = array();
        if ($receipts && count($receipts) > 0) {
            foreach($receipts as $receipt) {
                $list_receipted_unique_ids[] = $receipt->unique_id;
            }
        } else {
            \My_Log::debug('read - nodata');
            return;
        }

        $time = time();
        $is_create_file = false;

        foreach($list_receipted_unique_ids as $unique_id) {

            $options = array(
                'where' => array(
                    array('created_at', '>=', date('Y-m-d 00:00:00')),
                    array('unique_id', '=', $unique_id),
                'is_read' => true,
                ),
                'order_by' => array(
                    'created_at' => 'desc',
                ),
            );

            $notification = \Model_Notification::find('first', $options);
            $original_call = \Model_Originalcall::find('first', array(
                'where' => array(
                    array('id', $notification->original_call_id),
                ),
            ));

            if ($notification && $original_call) {
                $notification->use_read = false;
                $notification->save();

                \My_Log::debug(print_r($notification, true));
                $data = sprintf(
                    "%s%s%s%s%s%s%s%s",
                    str_pad($notification->unique_id, 14, 0, STR_PAD_LEFT),
                    str_pad($original_call->reserve_date, 8),
                    str_pad($original_call->reserve_type, 2),
                    str_pad($original_call->reserve_items, 3),
                    str_pad($original_call->room_number, 2),
                    str_pad($original_call->reserve_tantou, 8),
                    str_pad($original_call->reserve_start_time, 5),
                    str_pad($original_call->hojoka, 1)
                );
                file_put_contents($read_data_path . '/KIDOKU' . date('YmdHisn') . '.dat', $data . "\r\n", FILE_APPEND);
                $is_create_file = true;
            }
        }

        if ($is_create_file) {
            file_put_contents($read_index_path . '/KIDOKU' . date('YmdHisn', $time) . '.dat', '');
        }
    }

    /**
     * 受付 出力
     */
    public function receipt()
    {
        $sys_dir = $this->get_system_directory();
        $receipt_path = $sys_dir . \Config::get('my.shared_system_directories.receipt');
        $receipt_index_path = $receipt_path . '/' . \Config::get('my.shared_directory.index');
        $receipt_data_path = $receipt_path . '/' . \Config::get('my.shared_directory.data');
        $ok = \Config::get('my.shared_directory.ok');
        $ng = \Config::get('my.shared_directory.ng');
        $log = \Config::get('my.shared_directory.log');

        $this->check_create_dir($receipt_path);
        $this->check_create_dir($receipt_path . '/' . $log);
        $this->check_create_dir($receipt_index_path);
        $this->check_create_dir($receipt_index_path . '/' . $ok);
        $this->check_create_dir($receipt_index_path . '/' . $ng);
        $this->check_create_dir($receipt_data_path);
        $this->check_create_dir($receipt_data_path . '/' . $ok);
        $this->check_create_dir($receipt_data_path . '/' . $ng);

        $receipts = \Model_Receipt::find('all', array(
            'where' => array(
                'date' => date('Y-m-d'),
                array('status', '=', \Model_Receipt::STATUS_RECEIPTING),
            ),
        ));

        if ($receipts) {
            $time = time();
            foreach($receipts as $receipt) {
                $data = sprintf("%s", str_pad($receipt->unique_id, 14, 0, STR_PAD_LEFT));
                file_put_contents($receipt_data_path . '/SAIUKE' . date('YmdHis', $time) . '.dat', $data . "\r\n", FILE_APPEND);
            }
            file_put_contents($receipt_index_path . '/SAIUKE' . date('YmdHis', $time) . '.dat', '');
        } else {
            \My_Log::debug('receipt - nodata');
            return;
        }
    }

    /**
     * 受付済み処理
     */
    public function receipted()
    {
        $sys_dir = $this->get_system_directory();
        $receipt_path = $sys_dir . \Config::get('my.shared_system_directories.receipted');
        $receipt_index_path = $receipt_path . '/' . \Config::get('my.shared_directory.index');
        $receipt_data_path = $receipt_path . '/' . \Config::get('my.shared_directory.data');
        $ok = \Config::get('my.shared_directory.ok');
        $ng = \Config::get('my.shared_directory.ng');
        $log = \Config::get('my.shared_directory.log');

        $this->check_create_dir($receipt_path);
        $this->check_create_dir($receipt_path . '/' . $log);
        $this->check_create_dir($receipt_index_path);
        $this->check_create_dir($receipt_index_path . '/' . $ok);
        $this->check_create_dir($receipt_index_path . '/' . $ng);
        $this->check_create_dir($receipt_data_path);
        $this->check_create_dir($receipt_data_path . '/' . $ok);
        $this->check_create_dir($receipt_data_path . '/' . $ng);

        $files = scandir($receipt_index_path);
        $excepts = array(
            '.', '..', $ok, $ng,
        );

        foreach($files as $filename) {
            $status = true;
            $errors = array();
            if (in_array($filename, $excepts)) continue;

            $index_file = $receipt_index_path . '/' . $filename;
            $data_file = $receipt_data_path . '/' . $filename;

            if (!is_dir($index_file)) {
                if (!is_readable($index_file)) {
                    $errors[] = "index - ファイルが見つかりません({$index_file})。";
                    $status = false;
                }
            } else {
                $errors[] = "index - ディレクトリが指定されています({$index_file})。";
                $status = false;
            }

            $body = array();
            $receipt_kekka = array();
            if (!is_dir($data_file)) {
                if (!is_readable($data_file)) {
                    $errors[] = "data - ファイルが見つかりません({$index_file})。";
                    $status = false;
                } else {
                    $contents = file_get_contents($data_file);
                    $receipt_kekka['unique_id'] = substr($contents, \Config::get('my.denbun.sairai_uketuke_result.unique_id.start'), \Config::get('my.denbun.sairai_uketuke_result.unique_id.length'));
                    $receipt_kekka['hantei'] = substr($contents, \Config::get('my.denbun.sairai_uketuke_result.hantei.start'), \Config::get('my.denbun.sairai_uketuke_result.hantei.length'));
                    $receipt_kekka['result_code'] = substr($contents, \Config::get('my.denbun.sairai_uketuke_result.result_code.start'), \Config::get('my.denbun.sairai_uketuke_result.result_code.length'));
                    $receipt_kekka['receipt_number'] = substr($contents, \Config::get('my.denbun.sairai_uketuke_result.receipt_number.start'), \Config::get('my.denbun.sairai_uketuke_result.receipt_number.length'));
                    $receipt_kekka['guide'] = trim(
                        mb_convert_encoding(
                            substr($contents, \Config::get('my.denbun.sairai_uketuke_result.guide.start'), \Config::get('my.denbun.sairai_uketuke_result.guide.length')),
                            'utf-8',
                            'sjis-win'
                        )
                    );

                    if ($receipt_kekka['hantei'] == 1 && $receipt_kekka['result_code'] == 'YI000') {
                        $body['unique_id'] = $receipt_kekka['unique_id'];
                        $body['message'] = $receipt_kekka['guide'];
                    } elseif ($receipt_kekka['result_code']) {
                        $status = false;
                        $errors[] = 'リザルトコードエラー[' . $receipt_kekka['result_code'] . ']';
                    } else {
                        $status = false;
                        $errors[] = 'ファイルデータエラー';
                    }

                }
            } else {
                $errors[] = "data - ディレクトリが指定されています({$index_file})。";
                $status = false;
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

                        $receipt = \Model_Receipt::find('first', array(
                            'where' => array(
                                'unique_id' => $body['unique_id'],
                                'date' => date('Y-m-d'),
                                array('status', '=', \Model_Receipt::STATUS_RECEIPTING),
                            ),
                        ));
                        if ($receipt) {
                            $receipt->status = \Model_Receipt::STATUS_AFTER_RECEIPT;
                            if(!$receipt->save()) {
                                \My_Log::info("受付ステータス更新の失敗: " . $receipt->unique_id . " 受付中->受付済");
                                \DB::rollback_transaction();
                                continue;
                            }
                        }

                        $notification = new \Model_Notification(array(
                            'unique_id' => $body['unique_id'],
                            'device_token' => $terminal->device_token,
                            'platform' => $terminal->platform,
                            'application_type' => $terminal->application_type,
                            'body' => $body['message'],
                            'filename' => $index_file,
                            'use_read' => false,
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
                    $status = false;
                }
            }

            if ($skip_file_handle) {
                // $this->output_log('すでに取り込まれたファイルが見つかりました。[' . $index_file . ']', $receipt_path, $filename, false);
                // continue;
                $status = false;
                $errors = array('すでに取り込まれたファイルが見つかりました。');
            }

            if ($status) {
                $index_ok_dir = $receipt_index_path . '/' . \Config::get('my.shared_directory.ok') . '/';
                $data_ok_dir = $receipt_data_path . '/' . \Config::get('my.shared_directory.ok') . '/';

                chmod($index_file, 0777);
                chmod($index_ok_dir, 0777);
                chmod($data_file, 0777);
                chmod($data_ok_dir, 0777);
                @rename($index_file, $index_ok_dir . $filename);
                @rename($data_file, $data_ok_dir . $filename);
                $this->output_log("正常完了", $receipt_path, $filename, true);
            } else {
                $index_ng_dir = $receipt_index_path . '/' . \Config::get('my.shared_directory.ng') . '/';
                $data_ng_dir = $receipt_data_path . '/' . \Config::get('my.shared_directory.ng') . '/';

                chmod($index_file, 0777);
                chmod($index_ng_dir, 0777);
                chmod($data_file, 0777);
                chmod($data_ng_dir, 0777);
                @rename($index_file, $index_ng_dir . $filename);
                @rename($data_file, $data_ng_dir . $filename);
                $this->output_log(implode(",", $errors), $receipt_path, $filename, false);
            }
        }
    }
}