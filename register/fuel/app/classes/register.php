<?php
class Register
{
    const TIME_OUT = 30;
    const GET_SPAN_SEC = 2;
    const RESULE_OK_CODE = 'YI000';

    /*
     * レスポンスデータ TODO 例外処理（エラー時にエラー画面をださない）
     * @return array(
     *     'is_success' => boolean,
     *     'patient_id' => integer,
     *     'unique_id' => integer,
     *     'name' => string,
     *     'birth_at' => string
     * )
     */
    public $response = array();

    /*
     * 電子カルテと連携し、患者情報を取得
     *
     * @param integer unique_id
     * @param integer patient_id
     * @return array
     */
    public static function getPatient($unique_id, $patient_id)
    {
        // mount チェック
        // mountされていない場合mount

        $register = new self();

        // ファイル送信
        $register->put($unique_id, $patient_id);

        $startTime = time();
        $nowTime = time();
        while(($nowTime - $startTime) < self::TIME_OUT) {
            sleep(self::GET_SPAN_SEC);
            $nowTime = time();

            if ($register->get($unique_id)) {
                return $register->response;
            }
        }

        \Log::info('利用開始登録結果：タイムアウトしました id.' . $unique_id);

        $response = array(
            'is_success' => 0,
            'unique_id' => $unique_id,
            'name' => '',
            'birth_at' => '',
            'error_message' => '患者情報の取得でタイムアウトしました',
        );

        return $response;
    }

    /*
     * 電子カルテと連携し、患者情報を取得
     *
     * @param integer unique_id
     * @param integer patient_id
     */
    public function put($unique_id, $patient_id)
    {
        $dir = sprintf(
            '%s%s',
            \Config::get('my.shared_directory.dir', false),
            \Config::get('my.shared_directory.register_dir', false)
        );

        if (!is_dir($dir)) {
            \Log::info('register_dir に接続できません');
            return ;
        }

        $filename = sprintf(
            '%s%s',
            'TOROKU' . date('Ymd'),
            substr($unique_id, -4) . '.dat'
        );

        $indexFilePath = $dir . '/' . \Config::get('my.shared_directory.index', false) . '/' . $filename;
        $dataFilePath = $dir . '/' . \Config::get('my.shared_directory.data', false) . '/' . $filename;


        // dataファイル格納
        $data = $unique_id . $patient_id;
        file_put_contents($dataFilePath, $data, FILE_APPEND);

        // indexファイル格納
        file_put_contents($indexFilePath, "", FILE_APPEND);
    }

    /*
     * 電子カルテと連携し、患者情報を取得
     *
     * @param integer unique_id
     * @return boolean 接続が返ってきた場合はエラーであってもtrueを返す
     */
    public function get($unique_id)
    {
        $dir = sprintf(
            '%s%s',
            \Config::get('my.shared_directory.dir', false),
            \Config::get('my.shared_directory.register_result_dir', false)
        );

        if (!is_dir($dir)) {
            \Log::info('register_result_dir に接続できません');
            return ;
        }

        $index_dir = $dir . '/' . \Config::get('my.shared_directory.index', false) . '/';
        $data_dir = $dir . '/' . \Config::get('my.shared_directory.data', false) . '/';
        $log_dir = $dir . '/' . \Config::get('my.shared_directory.log', false) . '/';
        $log_file = date('Ymd') . '.dat';

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
            if (strpos($file, '.') === 0) continue;
            $index_file = $index_dir . $file;
            $data_file = $data_dir . $file;

            if (!is_readable($data_file)) {
                continue;
            }

            // index, dataファイルが見つかった場合処理
            $contents = file_get_contents($data_file);
            $response = array(
                'unique_id' => substr($contents, 0, 14),
                'result' => substr($contents, 14, 1),
                'result_code' => substr($contents, 15, 5),
                'name' => trim(
                    mb_convert_encoding(
                        substr($contents, 20, 20),
                        'utf-8',
                        'sjis-win'
                    )
                ),
                'birth_at' => substr($contents, 40, 8),
            );

            // 対象のデータでない場合
            if ($response['unique_id'] != $unique_id) {
                continue;
            }

            // 取得処理
            if ($response['result_code'] !== self::RESULE_OK_CODE) {
                // NG処理

                $this->move_file($index_dir, $file, false);  // indexファイルをNGに移動
                $this->move_file($data_dir, $file, false);   // dataファイルをNGに移動

                $this->output_log('結果コードが正常ではありませんでした', $log_dir, $log_file, false);	// logファイル書き込み


                $this->response = array(
                    'is_success' => 0,
                    'unique_id' => $unique_id,
                    'name' => $response['name'],
                    'birth_at' => $response['birth_at'],
                    'error_message' => '患者情報が取得できませんでした。(エラーコード:' . $response['result_code'] . ')' ,
                );
                return true;
            }

            // OK処理
            $this->move_file($index_dir, $file);  // indexファイルをOKに移動
            $this->move_file($data_dir, $file);   // dataファイルをOKに移動

            $this->output_log('正常完了', $log_dir, $log_file);	// logファイル書き込み

            $this->response = array(
                'is_success' => 1,
                'unique_id' => $unique_id,
                'name' => $response['name'],
                'birth_at' => $this->strtodate($response['birth_at']),
                'error_message' => '正常',
            );
            return true;

        }

        return false;
    }

    /**
     * 共有ディレクトリ OK, NGディレクトリに移動
     */
    private function move_file($parentdir, $filename, $isOk = true)
    {
        $from_filepath = sprintf(
            '%s%s',
            $parentdir,
            $filename
        );

        $to_filedir = sprintf(
            '%s%s/',
            $parentdir,
            ($isOk)? \Config::get('my.shared_directory.ok', false) : \Config::get('my.shared_directory.ng', false)
        );

        if (!file_exists($to_filedir) || !is_dir($to_filedir)) {
            @mkdir($to_filedir, 0777, true);
        }

        $to_filepath = sprintf(
            '%s%s',
            $to_filedir,
            $filename
        );

        return rename($from_filepath, $to_filepath);
    }

    /**
     * 共有ディレクトリ ログ出力
     */
    private function output_log($msg, $parentdir, $filename, $isOk = true)
    {
        $file_dir = sprintf(
            '%s%s/',
            $parentdir,
            ($isOk)? \Config::get('my.shared_directory.ok', false) : \Config::get('my.shared_directory.ng', false)
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

        file_put_contents($filepath, "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n", FILE_APPEND);
    }

    private function strtodate($str)
    {
        if (!$str) {
            return '';
        }

        $year = substr($str, 0, 4);
        $month = substr($str, 4, 2);
        $day = substr($str, 6, 2);

        return sprintf(
            "%s年%s月%s日",
            $year, $month, $day
        );
    }
}