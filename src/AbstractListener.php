<?php

namespace PatternLab\Composer;

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;

class AbstractListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $packageDir;

    /**
     * @var bool
     */
    protected $runPostAutoloadDump = true;

    public function __construct($packageDir)
    {
        $this->packageDir = $packageDir;
    }

    public static function getSubscribedEvents()
    {
        return [
          ScriptEvents::POST_AUTOLOAD_DUMP => ['postAutoloadDump'],
        ];
    }

    protected static function exportArray($array)
    {
        $string = '';
        foreach ($array as $k => $v) {
            $string .= var_export($k, true).' => '.$v.",\n";
        }

        return $string;
    }
}
