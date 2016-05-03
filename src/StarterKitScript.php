<?php

namespace PatternLab\Composer;

use Composer\Package\PackageInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Script\Event;
use PatternLab\Config;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class StarterKitScript
{
    public static function execute(Event $event)
    {
        $composer = $event->getComposer();
        $platformOverrides = $composer->getConfig()->get('platform') ?: [];
        $platformRepo = new PlatformRepository([], $platformOverrides);
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();
        $installedRepo = new CompositeRepository([$localRepo, $platformRepo]);
        $repos = new CompositeRepository(array_merge([$installedRepo], $composer->getRepositoryManager()->getRepositories()));


        // find the value given to the command
        $args = $event->getArguments();
        $definition = new InputDefinition([
          new InputOption('install', 'f', InputOption::VALUE_REQUIRED, 'Fetch a specific StarterKit from GitHub.'),
          new InputOption('init', 'i', InputOption::VALUE_NONE, 'Initialize with a blank StarterKit based on the active PatternEngine.'),
        ]);

        $input = new StringInput(implode(' ', $args));
        $input->bind($definition);

        $starterkit = $input->getOption('install');

        if ($input->getOption('init')) {
            // $patternEngine = Config::getOption("patternExtension");
            $patternEngine = 'twig';
            $starterkit    = "pattern-lab/starterkit-".$patternEngine."-base";
        }

        $matches = $repos->search($starterkit, RepositoryInterface::SEARCH_NAME);

        // double-checks options was properly set
        if (!$matches) {
            throw new \RuntimeException("please provide a path for the starterkit before trying to fetch it...");
        }

        /** @var PackageInterface $package */
        $package = $repos->findPackage($matches[0]['name'], '*');

        list($org, $repo) = explode("/",$package->getName());
        $tag = $package->getVersion();

        // set default attributes
        $sourceDir        = Config::getOption("sourceDir");
        $tempDir          = sys_get_temp_dir().DIRECTORY_SEPARATOR."pl-sk";
        //$tempDirSK        = $tempDir.DIRECTORY_SEPARATOR."pl-sk-archive";
        $tempDirDist      = $tempDir.DIRECTORY_SEPARATOR."dist";
        $tempComposerFile = $tempDir.DIRECTORY_SEPARATOR."composer.json";

        $event->getIO()->write("downloading the starterkit...");

        // try to download the given package
        $event->getComposer()->getDownloadManager()->download($package, $tempDir);

        // Create temp directory if doesn't exist
        $fs = new Filesystem();
        try {
            $fs->mkdir($tempDir, 0775);
        } catch (IOExceptionInterface $e) {
            throw new \RuntimeException("Error creating temporary directory at " . $e->getPath());
        }

        $event->getIO()->write("finished downloading the starterkit...");

        // extract, if the zip is supposed to be unpacked do that (e.g. stripdir)

        if (!is_dir($tempDirDist)) {
            // try without repo dir
            $tempDirDist  = $tempDir.DIRECTORY_SEPARATOR."dist";
        }
        // thrown an error if temp/dist/ doesn't exist
        if (!is_dir($tempDirDist)) {
            throw new \RuntimeException("the starterkit needs to contain a dist/ directory before it can be installed...");
        }

        // check for composer.json. if it exists use it for determining things. otherwise just mirror dist/ to source/
        if (file_exists($tempComposerFile)) {

            $tempComposerJSON = json_decode(file_get_contents($tempComposerFile), true);

            // see if it has a patternlab section that might define the files to move
            if (isset($tempComposerJSON["extra"]) && isset($tempComposerJSON["extra"]["patternlab"])) {
                $event->getIO()->write("installing the starterkit...");
                // InstallerUtil::parseComposerExtraList($tempComposerJSON["extra"]["patternlab"], $starterkit, $tempDirDist);
                $event->getIO()->write("installed the starterkit...");
            } else {
                //$this->mirrorDist($sourceDir, $tempDirDist);
            }

        } else {

            //$this->mirrorDist($sourceDir, $tempDirDist);

        }

        // remove the temp files
        $event->getIO()->write("cleaning up the temp files...");
        $fs = new Filesystem();
        $fs->remove($tempDir);

        $event->getIO()->write("the starterkit installation is complete...");

        return true;



//
//        if ($starterkit) {
//
//            // download the starterkit
//            $f = new Fetch();
//            $f->fetchStarterKit($starterkit);
//
//        }


    }
}
