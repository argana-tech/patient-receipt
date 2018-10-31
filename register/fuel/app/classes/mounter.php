<?php
class Mounter
{
    protected static $instance = null;

    protected $remote_full_path = null;
    protected $remote_host = null;
    protected $remote_path = null;
    protected $remote_user = null;
    protected $remote_password = null;
    protected $file_type = null;
    protected $mount_check_file = null;

    protected $local_path = null;
    protected $uid = null;
    protected $gid = null;
    protected $file_mode = null;
    protected $dir_mode = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$instance->initialize();
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this->remote_host = \Config::get('my.shared_directory.remote.host', false);
        $this->remote_path = \Config::get('my.shared_directory.remote.path', false);
        $this->remote_user = \Config::get('my.shared_directory.remote.user', false);
        $this->remote_password = \Config::get('my.shared_directory.remote.password', false);
        $this->file_type = \Config::get('my.shared_directory.remote.file_type', false);

        $this->local_path = \Config::get('my.shared_directory.dir');
        $this->uid = \Config::get('my.shared_directory.local.uid', false);
        $this->gid = \Config::get('my.shared_directory.local.gid', false);
        $this->file_mode = \Config::get('my.shared_directory.local.file_mode', false);
        $this->dir_mode = \Config::get('my.shared_directory.local.dir_mode', false);

        $this->mount_check_file = rtrim($this->local_path, '/') . '/' . \Config::get('my.shared_directory.remote.check_file', 'check_file.txt');
    }

    private function check_remote_config()
    {
        if (
            $this->remote_host === false
            || $this->remote_path === false
            // || $this->remote_user === false
            // || $this->remote_password === false
            || $this->file_type === false
        ) {
            return false;
        }
        return true;
    }

    /**
     * マウントされているかを判定
     * @return bool
     */
    private function is_mount()
    {
        if (!$this->check_remote_config()) {
            \Log::info('config "my"が設定されていません');
            return true;
        }

        $command_mount = 'mount';
        exec($command_mount, $result_mount, $status_mount);

        if ($status_mount != 0) {
            \Log::info('mount check error - command:"' . $command_mount . '" status:' . $status_mount);
            return false;
        }

        $result_mount_str = var_export($result_mount, true);

        $remote_path = '//' . rtrim($this->remote_host, '/') . '/' . rtrim($this->remote_path, '/');
        $remote = preg_quote($remote_path, '/');
        $local = preg_quote(rtrim($this->local_path, '/'), '/');
        $pattern = "/{$remote} on {$local}/";
        $result = preg_match($pattern, $result_mount_str, $matches);

        if ($result) {
            return true;
        }

        \Log::info('マウントされたディレクトリが見つかりません。[' . $pattern . ']');
        return false;
    }

    private function check_mount_file()
    {
        if (!file_exists($this->mount_check_file)) {
            \Log::info('マウントチェックファイルが見つかりません');
            return false;
        }
        return true;
    }

    /**
     * マウント実行
     * @return bool
     */
    private function run_mount()
    {
        if (!$this->check_remote_config()) {
            \Log::info('config "my"が設定されていません');
            return true;
        }

        if ($this->is_mount()) {
            \Log::info('すでにマウントされています');
            return true;
        }

        \Log::info('run mount');
        $remote_path = '//' . rtrim($this->remote_host, '/') . '/' . rtrim($this->remote_path, '/');
        $local_path = rtrim($this->local_path, '/');
        $command_mount = "mount -t {$this->file_type} {$remote_path} {$local_path}";

        $option = array();
        if ($this->remote_user) {
            $option[] = "user={$this->remote_user}";
        }
        if ($this->remote_password) {
            $option[] = "password={$this->remote_password}";
        }
        if ($this->uid) {
            $option[] = "uid={$this->uid}";
        }
        if ($this->gid) {
            $option[] = "gid={$this->gid}";
        }
        if ($this->file_mode) {
            $option[] = "file_mode={$this->file_mode}";
        }
        if ($this->dir_mode) {
            $option[] = "dir_mode={$this->dir_mode}";
        }
        $option_string = '';
        if (count($option) > 0) {
            $option_string = " -o " . implode(',', $option);
        }

        $command_mount .= $option_string;

        exec($command_mount, $result_mount, $status_mount);

        $result = 'OK';
        if ($status_mount != 0) {
            $result = 'NG';
        }

        \Log::info("{$result} command:\"" . $command_mount . '" status:' . $status_mount);

        if ($status_mount == 0) {
            return true;
        }
        return false;
    }

    /**
     * アンマウント実行
     * @return bool
     */
    private function run_unmount()
    {
        if (!$this->check_remote_config()) {
            \Log::info('config "my"が設定されていません');
            return true;
        }

        $local_path = rtrim($this->local_path);
        $command_unmount = "umount -l {$local_path}";
        exec($command_unmount, $result_unmount, $status_unmount);

        $result = 'OK';
        if ($status_unmount != 0){
            $result = 'NG';
        }

        \Log::info("{$result} command:\"" . $command_unmount . '" status:' . $status_unmount);

        if ($status_unmount == 0) {
            return true;
        }
        return false;
    }

    public static function check_mount()
    {
        if (self::instance()->is_mount()) {
            if (self::instance()->check_mount_file()) {
                return true;
            }
            self::instance()->run_unmount();
        }
        return false;
    }

    public static function mount()
    {
        return self::instance()->run_mount();
    }

    public static function unmount()
    {
        return self::instance()->run_unmount();
    }
}