<?php
class My_Log
{
    protected static $instance = null;

    protected $path = null;
    protected $filename = null;

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
        $path = \Config::get('my.logs', APPPATH.'mylogs'.DS);

        if ( ! is_dir($path) or ! is_writable($path))
        {
            \Config::set('log_threshold', \Fuel::L_NONE);
            throw new \FuelException('Unable to create the log file. The configured log path "'.$path.'" does not exist.');
        }

        $this->path = $path;
    }

    public function get_filename()
    {
        $filename = (date('Ymd') . '.log');
        if ($filename === $this->filename) {
            return $this->filename;
        }

        if (!\File::exists($this->path.$filename)) {
            \File::create($this->path, $filename);
        }

        $this->filename = $filename;

        return $this->filename;
    }

    public function write($msg)
    {
        $filename = $this->get_filename();
        $fp = fopen($this->path . $filename, "a");

        fwrite($fp, "[" . date('Y-m-d H:i:s') . "]" . $msg . PHP_EOL);
        chmod($this->path . $filename, 0666);

        fclose($fp);
    }

    public static function info($msg)
    {
        self::instance()->write($msg);
    }

    public static function debug($msg)
    {
        if (\Config::get('my.debug', false)) {
            self::instance()->write('[DEBUG]' . $msg);
        }
    }
}
