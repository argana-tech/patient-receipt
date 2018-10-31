<?php
namespace Fuel\Tasks;

use Fuel\Core\Cli;

/*
 * php oil refine monitor
 */
class Monitor
{
    public function run()
    {
        if (!\Mounter::check_mount()) {
            \Log::info('[monitor] mount.');
            \Mounter::mount();
        }
    }

    public function check_mount()
    {
        $result = \Mounter::check_mount();
        return "check mount result: " . $result;
    }

    public function mount()
    {
        $result = \Mounter::mount();
        return "mount result: " . $result;
    }

    public function unmount()
    {
        $result = \Mounter::unmount();
        return "umount result: " . $result;
    }
}