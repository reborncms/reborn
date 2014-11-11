<?php

namespace Reborn\Console;

use Config;
use Reborn\Util\Str;
use Reborn\Filesystem\File;
use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Authentication key generate Command class for Reborn
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class AuthKeyGenerateCommand extends SfCommand
{

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('auth:key-generate')
            ->setDescription('Generate key for Authentication Session');

        $this->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'Key value');
    }

    /**
     * Executes the current command.
     *
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = $input->getOption('key');

        if (is_null($key)) {
            $key = Str::random();
        }

        $original = Config::get('app.sentry_keyname');

        $content = File::getContent(APP.'config'.DS.'app.php');

        $content = str_replace($original, $key, $content);

        File::put(APP.'config'.DS.'app.php', $content);

        $output->writeln("<info>New Key is $key</info>");
    }

} // END class Console
