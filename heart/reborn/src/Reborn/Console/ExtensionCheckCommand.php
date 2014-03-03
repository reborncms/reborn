<?php

namespace Reborn\Console;

use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Authentication key generate Command class for Reborn
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class ExtensionCheckCommand extends SfCommand
{

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('check:extension')
            ->setDescription('Check require extension are already have or not.');
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
        $lists = array();

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $lists['PHP'] = '<info>PHP Version OK</info>';
        } else {
            $lists['PHP'] = '<error>Need to update PHP Version</error>';
        }

        if (function_exists('mysql_connect')) {
            $lists['MySQL'] = '<info>MySQL extension OK</info>';
        } else {
            $lists['MySQL'] = '<error>MySQL Extension require</error>';
        }

        if (function_exists('curl_init')) {
            $lists['CURL'] = '<info>CURL extension OK</info>';
        } else {
            $lists['CURL'] = '<error>CURL Extension require</error>';
        }

        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd_version = preg_replace('/[^0-9\.]/', '', $gd['GD Version']);

            if (version_compare($gd_version, '2.0', '>=')) {
                $lists['GD'] = '<info>GD extension OK</info>';
            } else {
                $lists['GD'] = '<error>Need to update GD Version</error>';
            }
        } else {
            $lists['GD'] = '<error>GD Extension require</error>';
        }

        foreach ($lists as $ext => $msg) {
            $output->writeln($ext.' - '.$msg);
        }
    }

} // END class Console
