<?php

namespace Reborn\Console;

use Reborn\Util\Str;
use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;
use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Module Generator Command class for Reborn
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class ModuleCommand extends SfCommand
{

    /**
     * Configures the current command.
     */
	protected function configure()
    {
        $this->setName('module:generate')
            ->setDescription('Module generate from console');
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

		$output->writeln("<info>Module create......</info>");

		$this->create($data);

        $output->writeln("<info>Module created</info>");
    }

    /**
     * Create the module process
     *
     * @param array $data
     * @return void
     */
    protected function create($data)
    {
    	$path = MODULES.strtolower(str_replace(' ', '_', $data['name']));
        // make module folder path
    	Dir::make($path);

        $data['classname'] = Str::studly($data['module']);

        // make module src path
        Dir::make($path.DS.'src');
        // make module src/Namespace path
        Dir::make($path.DS.'src'.DS.$data['classname']);
        $src = $path.DS.'src'.DS.$data['classname'];

    	$dirs = array(
    			$path.DS.'lang',
                $path.DS.'lang'.DS.'en',
    			$path.DS.'config',
    			$path.DS.'assets',
    			$path.DS.'assets'.DS.'css',
    			$path.DS.'assets'.DS.'js',
    			$path.DS.'assets'.DS.'img',
    			$path.DS.'views',
    			$src.DS.'Controller',
    			$src.DS.'Model',
    		);

    	foreach ($dirs as $d) {
    		Dir::make($d);
    	}

        if (($data['backend'] == 'yes') or ($data['backend'] == 'Yes')) {
            $admin = $src.DS.'Controller'.DS.'Admin';
            Dir::make($admin);
        }

        $data['src_path'] = $src;

    	$this->setInfo($data, $path);
    	$this->setInstaller($data, $path);
    	$this->setBootstrap($data, $path);
    	$this->setController($data);
        $this->setModel($data);

    	$route = <<<EOT
<?php

// Route file for module {$data['module']}
EOT;

    	File::write($path, 'routes.php', $route);
    }

    /**
     * Generate the Info File for Module
     *
     * @param array $data
     * @return void
     */
    protected function setInfo($data, $path)
    {
    	$file = __DIR__.DS.'Resources'.DS.'Info.php';

    	$fileData = File::getContent($file);

    	if (($data['frontend'] == 'yes') or ($data['frontend'] == 'Yes')) {
    		$data['frontend'] = 'true';
    	} else {
    		$data['frontend'] = 'false';
    	}

    	if (($data['backend'] == 'yes') or ($data['backend'] == 'Yes')) {
    		$data['backend'] = 'true';
    	} else {
    		$data['backend'] = 'false';
    	}

        if (($data['allowDefaultModule'] == 'yes') or ($data['allowDefaultModule'] == 'Yes')) {
            $data['allowDefaultModule'] = 'true';
        } else {
            $data['allowDefaultModule'] = 'false';
        }

        if (($data['allowToChangeUriPrefix'] == 'yes') or ($data['allowToChangeUriPrefix'] == 'Yes')) {
            $data['allowToChangeUriPrefix'] = 'true';
        } else {
            $data['allowToChangeUriPrefix'] = 'false';
        }

    	foreach ($data as $k => $v) {
    		$fileData = str_replace('{'.$k.'}', $data[$k], $fileData);
    	}

    	File::write($path, $data['classname'].'Info.php', $fileData);
    }

    /**
     * Generate the Installer File for Module
     *
     * @param array $data
     * @return void
     */
    protected function setInstaller($data, $path)
    {
    	$file = __DIR__.DS.'Resources'.DS.'Installer.php';

    	$fileData = File::getContent($file);

    	$fileData = str_replace('{module}', $data['classname'], $fileData);

    	File::write($path, $data['classname'].'Installer.php', $fileData);
    }

    /**
     * Generate the Bootstrap File for Module
     *
     * @param array $data
     * @return void
     */
    protected function setBootstrap($data, $path)
    {
    	$file = __DIR__.DS.'Resources'.DS.'Bootstrap.php';

    	$fileData = File::getContent($file);

    	$fileData = str_replace('{module}', $data['classname'], $fileData);

    	File::write($path, 'Bootstrap.php', $fileData);
    }

    /**
     * Generate the Controller File for Module
     *
     * @param array $data
     * @return void
     */
    protected function setController($data)
    {
    	if (($data['frontend'] == 'yes') or ($data['frontend'] == 'Yes')) {
            $file = __DIR__.DS.'Resources'.DS.'Controller.php';

            $fileData = File::getContent($file);

            $fileData = str_replace('{module}', $data['classname'], $fileData);

            $controller = $data['classname'].'Controller.php';

            $path = $data['src_path'].DS.'Controller'.DS;

            File::write($path, $controller, $fileData);
        }

        if (($data['backend'] == 'yes') or ($data['backend'] == 'Yes')) {
            $admin = __DIR__.DS.'Resources'.DS.'AdminController.php';

            $adminData = File::getContent($admin);

            $adminData = str_replace('{module}', $data['classname'], $adminData);

            $adminCon = $data['classname'].'Controller.php';

            $path = $data['src_path'].DS.'Controller'.DS.'Admin'.DS;

            File::write($path, $adminCon, $adminData);
        }
    }

    /**
     * Generate the Model File for Module
     *
     * @param array $data
     * @return void
     */
    protected function setModel($data)
    {
        $file = __DIR__.DS.'Resources'.DS.'Model.php';

        $fileData = File::getContent($file);

        $fileData = str_replace('{module}', $data['classname'], $fileData);

        $fileData = str_replace('{table}', $data['table'], $fileData);

        $model = $data['classname'].'.php';

        File::write($data['src_path'].DS.'Model'.DS, $model, $fileData);
    }

    /**
     * Collect module data from the user
     *
     * @param OutputInterface $output
     * @return array
     */
    protected function collectData($output)
    {
    	$dialog = $this->getHelperSet()->get('dialog');

        $name = $dialog->ask($output, "<question>Please enter the name of the module : </question>", null);

        $version = $dialog->ask($output, "<question>Please enter the version of the module : </question>", null);

        $description = $dialog->ask($output, "<question>Please enter the description of the module : </question>", null);


		$author = $dialog->ask($output, "<question>Please enter the author of the module : </question>", null);

		$authorEmail = $dialog->ask($output, "<question>Please enter the author email of the module : </question>", null);

		$authorUrl = $dialog->ask($output, "<question>Please enter the author URL of the module : </question>", null);

		$backend = $dialog->ask($output, "<question>Module is backend support? (Yes, No) : </question>", null);

		$frontend = $dialog->ask($output, "<question>Module is frontend support? (Yes, No) : </question>", null);

        if (in_array($frontend, array('no', 'No'))) {
            $allowDefaultModule = 'no';
        } else {
            $allowDefaultModule = $dialog->ask($output, "<question>Module is allow to set default module? (Yes, No) : </question>", null);
        }

        $prefix = $dialog->ask($output, "<question>Please enter the URI Prefix of the module : </question>", null);

        $allowToChangeUriPrefix = $dialog->ask($output, "<question>Module is allow to change the Default URI Prefix? (Yes, No) : </question>", null);

		$data = array(
				'module' => ucfirst($name),
				'name' => $name,
                'table' => strtolower($name),
				'version' => $version,
				'description' => $description,
				'author' => $author,
				'authorEmail' => $authorEmail,
				'authorUrl' => $authorUrl,
				'backend' => $backend,
				'frontend' => $frontend,
                'allowDefaultModule' => $allowDefaultModule,
                'prefix' => "'".$prefix."'",
                'allowToChangeUriPrefix' => $allowToChangeUriPrefix
			);

		return $data;
    }

} // END class Console
