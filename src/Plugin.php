<?php

namespace PatternLab\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use PatternLab\InstallerUtil;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new UnifiedAssetInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);

        $plugin = new Installer();
        $composer->getEventDispatcher()->addSubscriber($plugin);
    }

    public static function getSubscribedEvents()
    {
        return [
            PluginEvents::COMMAND => ['onCommand']
        ];
    }

    public function onCommand(CommandEvent $event)
    {
        if (class_exists("\PatternLab\Config")) {
            InstallerUtil::init($event->getInput(), $event->getOutput());
        }
    }
}