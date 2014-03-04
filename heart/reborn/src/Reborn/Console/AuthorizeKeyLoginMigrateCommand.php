<?php

namespace Reborn\Console;

use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Authorize Key Login Migration Command class for Reborn
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class AuthorizeKeyLoginMigrateCommand extends SfCommand
{

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('migrate:key_table')
            ->setDescription('Create Authorize Key Table');

        $this->addOption('table', 't', InputOption::VALUE_OPTIONAL, 'Key table name');
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
        $tablename = $input->getOption('table');

        if (is_null($tablename)) {
            $tablename = 'authorize_keys';
        }

        try {
            \Schema::table($tablename, function($table)
            {
                $table->create();
                $table->increments('id');
                $table->string('client_key'); // Client key for Application
                $table->string('client_secret'); // Client secret for Application
                $table->integer('usre_id');
                $table->string('authorization_code'); // Key for user login
                $table->integer('access_times'); // Total access time for user
                $table->tinyInteger('activated')->default(1);
                $table->timestamps();
            });

            $output->writeln("<info>Authorize table [" . $tablename . "] is success created.</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
        }
    }

}
