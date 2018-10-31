<?php
namespace Fuel\Tasks;

use Fuel\Core\Cli;

class Exec_Notify
{
    public function run()
    {
        require_once 'System/Daemon.php';
        \My_Log::debug('Task exec_notify start');

        $options = array(
            'appName' => 'pushnotification',
            'appDir' => dirname(__FILE__),
        );

        \System_Daemon::setOptions($options);

        \System_Daemon::start();


        $file = \Finder::search('tasks', strtolower('Notify'));
        require_once $file;

        $start_time = time();
        $end_time = strtotime(date('Y-m-d H:i:00', $start_time) . '+1 minute');
        $count = 0;
        // while (true) {
        while (!\System_Daemon::isDying()) {

            if (!\My_Mount::check_mount()) {
                if (!\My_Mount::mount()) {
                    // sleep(10);
                    \System_Daemon::iterate(10);
                    continue;
                }
            }

            try {
                $notify = new Notify();
                $notify->run();
                // Notify::run();
            } catch (Exception $e) {
                \My_Log::debug($e);
            }
            // sleep(10);
            \System_Daemon::iterate(10);
        }
        // return 0;
        System_Daemon::stop();
    }
}