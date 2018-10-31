<?php
namespace Fuel\Tasks;

use Fuel\Core\Cli;

class Monitor
{
    public function run()
    {
        exec('ps aux | grep "exec_notify" | grep -v "grep"', $result, $status);
        if ($status == 0 && count($result) > 0) {
            // \My_Log::debug(count($result) . ' ' . var_export($result, true));
        } else {
            // \My_Log::debug(count($result) . ' ' . var_export($result, true));
            \My_Log::info('[monitor] restart exec_notify.');
            exec('FUEL_ENV=production /usr/bin/php /var/www/patient-recept/tuh_patient_recept/oil r exec_notify > /dev/null 2>&1 &');
        }

        if (!\My_Mount::check_mount()) {
            \My_Log::info('[monitor] mount.');
            \My_Mount::mount();
        }
    }

    public function check_mount()
    {
        $result = \My_Mount::check_mount();
        return "check mount result: " . $result;
    }

    public function mount()
    {
        $result = \My_Mount::mount();
        return "mount result: " . $result;
    }

    public function unmount()
    {
        $result = \My_Mount::unmount();
        return "umount result: " . $result;
    }
}