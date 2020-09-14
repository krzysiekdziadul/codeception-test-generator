<?php


namespace CodeceptionTestsGenerator;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class PostCmd extends Init
{
    public static function postInstall(Event $event)
    {
        $composer = $event->getComposer();
    }

    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        $run      = new Init();
        $run->initial();
    }

    public static function postAutoloadDump(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        require $vendorDir . '/autoload.php';

        some_function_from_an_autoloaded_file();
    }

    public static function postPackageInstall(PackageEvent $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
    }

    public static function warmCache(Event $event)
    {
    }
}