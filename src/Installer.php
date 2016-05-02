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
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use PatternLab\Config;
use PatternLab\Console;
use PatternLab\Fetch;
use PatternLab\Generator;

class Installer implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
          ScriptEvents::POST_CREATE_PROJECT_CMD => ['postCreateProjectCmd'],
          ScriptEvents::PRE_INSTALL_CMD => ['preInstallCmd'],
        );
    }

    /**
     * Ask questions after the create package is done.
     *
     * @param Event $event a script event object from composer
     */
    public static function postCreateProjectCmd(Event $event)
    {
        // see if there is an extra component
        $extra = $event->getComposer()->getPackage()->getExtra();

        if (isset($extra['patternlab'])) {
            Console::writeLine('');

            // see if we have any starterkits to suggest
            if (isset($extra['patternlab']['starterKitSuggestions']) && is_array($extra['patternlab']['starterKitSuggestions'])) {
                $suggestions = $extra['patternlab']['starterKitSuggestions'];

                // suggest starterkits
                Console::writeInfo('suggested starterkits that work with this edition:', false, true);
                foreach ($suggestions as $i => $suggestion) {

                    // write each suggestion
                    $num = $i + 1;
                    Console::writeLine($num.': '.$suggestion, true);
                }

                // prompt for input on the suggestions
                Console::writeLine('');
                $prompt = 'choose an option or hit return to skip:';
                $options = '(ex. 1)';
                $input = Console::promptInput($prompt, $options);
                $result = (int) $input - 1;

                if (isset($suggestions[$result])) {
                    Console::writeLine('');
                    $f = new Fetch();
                    $result = $f->fetchStarterKit($suggestions[$result]);

                    if ($result) {
                        Console::writeLine('');
                        $g = new Generator();
                        $g->generate(array('foo' => 'bar'));

                        Console::writeLine('');
                        Console::writeInfo('type <desc>php core/console --server</desc> to start the built-in server and see Pattern Lab...', false, true);
                    }
                } else {
                    Console::writeWarning('you will need to install a StarterKit before using Pattern Lab...');
                }
            }
        }
    }

    /**
     * Make sure certain things are set-up before running composer's install.
     *
     * @param Event $event a script event object from composer
     */
    public static function preInstallCmd(Event $event)
    {
        // default vars
        $sourceDir = Config::getOption('sourceDir');
        $packagesDir = Config::getOption('packagesDir');

        // check directories
        if (!is_dir($sourceDir)) {
            mkdir($sourceDir);
        }

        if (!is_dir($packagesDir)) {
            mkdir($packagesDir);
        }
    }
}
