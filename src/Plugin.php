<?php

namespace PatternLab\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new UnifiedAssetInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);

        $plugin = new PackageEventListener('packages');
        $composer->getEventDispatcher()->addSubscriber($plugin);

        $plugin = new DefaultConfigListener('packages');
        $composer->getEventDispatcher()->addSubscriber($plugin);
    }
}
