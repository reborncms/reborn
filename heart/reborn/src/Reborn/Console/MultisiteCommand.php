<?php

namespace Reborn\Console;

use Reborn\Util\Str;
use Reborn\Config\Writer;
use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;
use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Multisite Create Configure Command class for Reborn
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class MultisiteCommand extends SfCommand
{
	/**
     * Configures the current command.
     */
	protected function configure()
    {
        $this->setName('multisite:create')
            ->setDescription('Multisite folder and config create from console');
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
		$data = $this->collectData($output);

		$output->writeln("<info>Multisite create......</info>");

		$this->create($data);

        $output->writeln("<info>Site ".$data['path']." is created</info>");
    }

    /**
     * Create the module process
     *
     * @param array $data
     * @return void
     */
    protected function create($data)
    {
    	$this->contentFolderCheck($data['path']);

    	$site_path = BASE_CONTENT.$data['path'];

    	Dir::make($site_path);

    	$folders = array('modules', 'themes', 'uploads', 'widgets');

    	foreach ($folders as $folder) {
    		$this->makeFolder($site_path.DS.$folder);
    	}

    	$this->writeSiteConfigFile($data);
    }

    /**
     * Write Site Configuration File
     *
     * @param array $data
     * @return void
     **/
    protected function writeSiteConfigFile($data)
    {
    	$orginal = require BASE_CONTENT.'sites.php';

    	$config = array(
    		'content_path'	=> array($data['domain'] => $data['path']),
    		'prefix'		=> array($data['domain'] => $data['prefix'])
    	);

        $config = array_merge_recursive($orginal, $config);

        $content_path = str_replace(
                            array('  ', 'array (', ')'),
                            array("\t\t", 'array(', "\t)"),
                            var_export($config['content_path'], true)
                    ).",\n";
        $prefix = str_replace(
                        array('  ', 'array (', ')'),
                        array("\t\t", 'array(', "\t)"),
                        var_export($config['prefix'], true)
                    ).",\n";

        $dummy = File::getContent(__DIR__.DS.'Resources'.DS.'sites.txt');

        $sites = str_replace(
                    array('{{content_path}}', '{{prefix}}'),
                    array($content_path, $prefix),
                    $dummy
                );

        File::write(BASE_CONTENT, 'sites.php', $sites);
    }

    /**
     * Make site content folder and .gitignore file
     *
     * @param string $folder_path
     * @return void
     **/
    protected function makeFolder($folder_path)
    {
    	if (Dir::make($folder_path)) {
    		$ignore = <<<EOT
*
!.gitignore
EOT;
    		File::write($folder_path.DS, '.gitignore', $ignore);
    	}
    }

    /**
     * Check Content Folder is Exists or not
     *
     * @param string $path
     * @return void
     **/
    protected function contentFolderCheck($path)
    {
        if (in_array($path, array('main', 'shared', 'commands', 'vendor'))) {
            throw new \InvalidArgumentException("$path name is not allowed name!");
        }

    	if (is_dir(BASE_CONTENT.$path)) {
    		throw new \InvalidArgumentException("$path is already exists!");
    	}
    }

    /**
     * Collect site data from the user
     *
     * @param OutputInterface $output
     * @return array
     */
    protected function collectData($output)
    {
    	$dialog = $this->getHelperSet()->get('dialog');

        $domain = $dialog->ask($output, "<question>Site Domain : </question>", null);

        $path = $dialog->ask($output, "<question>Content Folder Name : </question>", null);

        $prefix = $dialog->ask($output, "<question>Prefix Name for Database : </question>", null);

		$data = array(
				'domain' => $domain,
				'path' => str_replace(' ', '_', $path),
                'prefix' => rtrim($prefix, '_')
			);

		return $data;
    }
}
