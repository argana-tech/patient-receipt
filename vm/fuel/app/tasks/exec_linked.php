<?php
namespace Fuel\Tasks;

use Fuel\Core\Cli;

class Exec_Linked
{
    public function run()
    {
        $sub_method = array('locate', 'read', 'receipt', 'received');
        foreach($sub_method as $method) {
            exec('ps aux | grep "exec_linked:' . $method . '" | grep -v "grep"', $result, $status);
            if ($status == 0 && count($result) > 0) {
                // \My_Log::debug(count($result) . ' ' . var_export($result, true));
            } else {
                // \My_Log::debug(count($result) . ' ' . var_export($result, true));
                \My_Log::info('[monitor] restart exec_linked:' . $method . '.');
                exec('FUEL_ENV=production /usr/bin/php /var/www/patient-recept/tuh_patient_recept/oil r exec_linked:' . $method . ' > /dev/null 2>&1 &');
            }
        }
    }

    public function locate()
    {
        require_once 'System/Daemon.php';
        \My_Log::debug('Task exec_linked:locate start');

        $options = array(
            'appName' => 'linkedlocate',
            'appDir' => dirname(__FILE__),
        );

        \System_Daemon::setOptions($options);

        \System_Daemon::start();


        $file = \Finder::search('tasks', strtolower('Linked'));
        require_once $file;

        $start_time = time();
        $end_time = strtotime(date('Y-m-d H:i:00', $start_time) . '+1 minute');
        $count = 0;
        // while (true) {
        while (!\System_Daemon::isDying()) {

            if (!\My_Mount::check_mount()) {
                if (!\My_Mount::mount()) {
                    // sleep(10);
                    \System_Daemon::iterate(60);
                    continue;
                }
            }

            try {
                $linked = new Linked();
                $linked->locate();
                // Notify::run();
            } catch (Exception $e) {
                \My_Log::debug($e);
            }
            // sleep(10);
            \System_Daemon::iterate(60);
        }
        // return 0;
        System_Daemon::stop();
    }

    public function read()
    {
        require_once 'System/Daemon.php';
        \My_Log::debug('Task exec_linked:read start');

        $options = array(
            'appName' => 'linkedread',
            'appDir' => dirname(__FILE__),
        );

        \System_Daemon::setOptions($options);

        \System_Daemon::start();


        $file = \Finder::search('tasks', strtolower('Linked'));
        require_once $file;

        $start_time = time();
        $end_time = strtotime(date('Y-m-d H:i:00', $start_time) . '+1 minute');
        $count = 0;
        // while (true) {
        while (!\System_Daemon::isDying()) {

            if (!\My_Mount::check_mount()) {
                if (!\My_Mount::mount()) {
                    // sleep(10);
                    \System_Daemon::iterate(60);
                    continue;
                }
            }

            try {
                $linked = new Linked();
                $linked->read();
                // Notify::run();
            } catch (Exception $e) {
                \My_Log::debug($e);
            }
            // sleep(10);
            \System_Daemon::iterate(60);
        }
        // return 0;
        System_Daemon::stop();
    }

    public function receipt()
    {
        require_once 'System/Daemon.php';
        \My_Log::debug('Task exec_linked:receipt start');

        $options = array(
            'appName' => 'linkedreceipt',
            'appDir' => dirname(__FILE__),
        );

        \System_Daemon::setOptions($options);

        \System_Daemon::start();


        $file = \Finder::search('tasks', strtolower('Linked'));
        require_once $file;

        $start_time = time();
        $end_time = strtotime(date('Y-m-d H:i:00', $start_time) . '+1 minute');
        $count = 0;
        // while (true) {
        while (!\System_Daemon::isDying()) {

            if (!\My_Mount::check_mount()) {
                if (!\My_Mount::mount()) {
                    // sleep(10);
                    \System_Daemon::iterate(60);
                    continue;
                }
            }

            try {
                $linked = new Linked();
                $linked->receipt();
            } catch (Exception $e) {
                \My_Log::debug($e);
            }
            // sleep(10);
            \System_Daemon::iterate(60);
        }
        // return 0;
        System_Daemon::stop();
    }

    public function received()
    {
        require_once 'System/Daemon.php';
        \My_Log::debug('Task exec_linked:received start');

        $options = array(
            'appName' => 'linkedreceived',
            'appDir' => dirname(__FILE__),
        );

        \System_Daemon::setOptions($options);

        \System_Daemon::start();


        $file = \Finder::search('tasks', strtolower('Linked'));
        require_once $file;

        $start_time = time();
        $end_time = strtotime(date('Y-m-d H:i:00', $start_time) . '+1 minute');
        $count = 0;
        // while (true) {
        while (!\System_Daemon::isDying()) {

            if (!\My_Mount::check_mount()) {
                if (!\My_Mount::mount()) {
                    // sleep(10);
                    \System_Daemon::iterate(60);
                    continue;
                }
            }

            try {
                $linked = new Linked();
                $linked->receipted();
            } catch (Exception $e) {
                \My_Log::debug($e);
            }
            // sleep(10);
            \System_Daemon::iterate(60);
        }
        // return 0;
        System_Daemon::stop();
    }
}