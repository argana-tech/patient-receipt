<?php

return array(
    'shared_directory' => array(
        'remote' => array(			// マウントするリモート情報
            'host' => '192.168.1.2',
            'path' => 'shared',
            'user' => 'User',
            'password' => 'password',
            'file_type' => 'cifs',
            'check_file' => 'check_file.txt',
        ),
        'local' => array(
             'uid' => 'apache',
             'gid' => 'apache',
             'file_mode' => '0777',
             'dir_mode' => '0777',
        ),
        'dir' => '/vagrant/mnt/',	// マウントするローカルディレクトリ
        'register_dir' => 'RIYOTOROKU',
        'register_result_dir' => 'RIYOTOROKU_KEKKA',
        'index' => 'INDEX',
        'data' => 'DATA',
        'ok' => 'OK',
        'ng' => 'ERR',
        'log' => 'LOG',
        'log_name_timestamp' => 'Ymd',
    ),

    /**
     * アプリケーションログ
     */
    'logs' => APPPATH . 'mylogs' . DS,
    'debug' => true,

    /**
     * トップページへのリダイレクト ミリ秒
     */
    'redirect_top_time' => 20000,
);
