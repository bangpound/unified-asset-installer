<?php

/*!
 * Installer Class
 *
 * Copyright (c) 2014 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 *
 * References the InstallerUtil class that is included in pattern-lab/core
 *
 */

namespace PatternLab\Composer;

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use PatternLab\InstallerUtil;

class Installer implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
          ScriptEvents::POST_CREATE_PROJECT_CMD => ['postCreateProjectCmd'],
          PackageEvents::POST_PACKAGE_INSTALL => ['postPackageInstall'],
          PackageEvents::POST_PACKAGE_UPDATE => ['postPackageUpdate'],
          ScriptEvents::PRE_INSTALL_CMD => ['preInstallCmd'],
          PackageEvents::PRE_PACKAGE_UNINSTALL => ['prePackageUninstall'],
        );
    }

    /**
     * Run the PL tasks when a package is installed.
     *
     * @param Event $event a script event object from composer
     */
    public static function postCreateProjectCmd(Event $event)
    {

        // make sure pattern lab has been loaded
        if (class_exists("\PatternLab\Config")) {
            InstallerUtil::postCreateProjectCmd($event);
        }
    }

    /**
     * Run the PL tasks when a package is installed.
     *
     * @param PackageEvent $event a script event object from composer
     */
    public static function postPackageInstall(PackageEvent $event)
    {

        // make sure pattern lab has been loaded
        if (class_exists("\PatternLab\Config")) {
            InstallerUtil::postPackageInstall($event);
        }
    }

    /**
     * Run the PL tasks when a package is updated.
     *
     * @param PackageEvent $event a script event object from composer
     */
    public static function postPackageUpdate(PackageEvent $event)
    {

        // make sure pattern lab has been loaded
        if (class_exists("\PatternLab\Config")) {
            InstallerUtil::postPackageUpdate($event);
        }
    }

    /**
     * Make sure certain things are set-up before running composer's install.
     *
     * @param Event $event a script event object from composer
     */
    public static function preInstallCmd(Event $event)
    {

        // make sure pattern lab has been loaded
        if (class_exists("\PatternLab\Config")) {
            InstallerUtil::preInstallCmd($event);
        }
    }

    /**
     * Run the PL tasks when a package is removed.
     *
     * @param PackageEvent $event a script event object from composer
     */
    public static function prePackageUninstall(PackageEvent $event)
    {

        // make sure pattern lab has been loaded
        if (class_exists("\PatternLab\Config")) {
            InstallerUtil::prePackageUninstallCmd($event);
        }
    }
}
