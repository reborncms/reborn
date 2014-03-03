<?php

namespace Reborn\Console;

use Reborn\Util\Str;
use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;
use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputInterface;
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
     * Module uri
     *
     * @var string
     **/
    protected $uri;

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

        $output->writeln("<info>".$data['name']." Module created</info>");
    }

    /**
     * Create the module process
     *
     * @param  array $data
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

        if ($data['backend']) {
            $admin = $src.DS.'Controller'.DS.'Admin';
            Dir::make($admin);
            // Make Admin View Folder
            Dir::make($path.DS.'views'.DS.'admin');
        }

        $data['src_path'] = $src;

        $this->makeInfo($data, $path);
        $this->makeInstaller($data, $path);
        $this->makeBootstrap($data, $path);
        $this->makeController($data, $path);
        $this->makeModel($data);

        $this->makeRouteFile($data, $path);
    }

    /**
     * Generate the route file for Module
     *
     * @param  array  $data
     * @param  string $path
     * @return void
     **/
    protected function makeRouteFile($data, $path)
    {
        if ($data['backend']) {
            $uri = $this->uri;
            $route_name = 'admin.'.strtolower($data['classname']);
            $ctrl_name = $data['classname'].'\Admin\\'.$data['classname'];
            $route_data = <<<EOT
Route::group('@admin/$uri', function () {
    Route::get('{p:page}?', '$ctrl_name::index', '$route_name.index');
    Route::add('create', '$ctrl_name::create', '$route_name.create');
    Route::add('edit/{int:id}', '$ctrl_name::edit', '$route_name.edit');
    Route::get('delete/{int:id}?', '$ctrl_name::delete', '$route_name.delete');
});
EOT;
        } else {
            $route_data = '';
        }

        if ($data['frontend']) {
            $uri = $this->uri;
            $route_name = strtolower($data['classname']);
            $ctrl_name = $data['classname'].'\\'.$data['classname'];
            $public_route = <<<EOT
Route::get('$uri', '$ctrl_name::index', '$route_name.index');
Route::get('$uri/view/{int:id}', '$ctrl_name::view', '$route_name.view');
EOT;
        } else {
            $public_route = '';
        }


        $route = <<<EOT
<?php

// Route file for module {$data['module']}

$route_data

$public_route
EOT;

        File::write($path, 'routes.php', $route);
    }

    /**
     * Generate the Info File for Module
     *
     * @param  array $data
     * @return void
     */
    protected function makeInfo($data, $path)
    {
        $file = __DIR__.DS.'Resources'.DS.'Info.php';

        $fileData = File::getContent($file);

        if ($data['frontend']) {
            $data['frontend'] = 'true';
        } else {
            $data['frontend'] = 'false';
        }

        if ($data['backend']) {
            $data['backend'] = 'true';
        } else {
            $data['backend'] = 'false';
        }

        if ($data['allowDefaultModule']) {
            $data['allowDefaultModule'] = 'true';
        } else {
            $data['allowDefaultModule'] = 'false';
        }

        if ($data['allowToChangeUriPrefix']) {
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
     * @param  array  $data
     * @param  string $path
     * @return void
     */
    protected function makeInstaller($data, $path)
    {
        $file = __DIR__.DS.'Resources'.DS.'Installer.php';

        $fileData = File::getContent($file);

        $search = array('{module}', '{table}');
        $replace = array($data['classname'], $data['table']);

        $fileData = str_replace($search, $replace, $fileData);

        File::write($path, $data['classname'].'Installer.php', $fileData);
    }

    /**
     * Generate the Bootstrap File for Module
     *
     * @param  array $data
     * @return void
     */
    protected function makeBootstrap($data, $path)
    {
        $file = __DIR__.DS.'Resources'.DS.'Bootstrap.php';

        $fileData = File::getContent($file);

        $search = array('{module}', '{table}', '{uri}');
        $replace = array($data['classname'], $data['table'], $this->uri);

        $fileData = str_replace($search, $replace, $fileData);

        File::write($path, 'Bootstrap.php', $fileData);
    }

    /**
     * Generate the Controller File for Module
     *
     * @param  array  $data
     * @param  string $path
     * @return void
     */
    protected function makeController($data, $path)
    {
        if ($data['frontend']) {
            $file = __DIR__.DS.'Resources'.DS.'Controller.php';

            $fileData = File::getContent($file);

            $fileData = str_replace('{module}', $data['classname'], $fileData);

            $controller = $data['classname'].'Controller.php';

            $ctrl_path = $data['src_path'].DS.'Controller'.DS;

            File::write($ctrl_path, $controller, $fileData);

            $this->makeViews($path);
        }

        if ($data['backend']) {
            $admin = __DIR__.DS.'Resources'.DS.'AdminController.php';

            $adminData = File::getContent($admin);

            $adminData = str_replace('{module}', $data['classname'], $adminData);

            $adminCon = $data['classname'].'Controller.php';

            $ctrl_path = $data['src_path'].DS.'Controller'.DS.'Admin'.DS;

            File::write($ctrl_path, $adminCon, $adminData);

            // Make Extension Folder for Form, Table, etc..
            $extension = $data['src_path'].DS.'Extensions';
            Dir::make($extension);

            // Make Form Folder in Extensions
            Dir::make($extension.DS.'Form');

            $this->makeFormClass($extension.DS.'Form'.DS, $data['classname']);

            // Make Table Folder in Extensions
            Dir::make($extension.DS.'Table');

            $this->makeTableClass($extension.DS.'Table'.DS, $data['classname']);

            $this->makeViews($path, true);
        }
    }

    /**
     * Make Form Builder Class
     *
     * @param  string $path
     * @param  string $class
     * @return void
     **/
    protected function makeFormClass($path, $class)
    {
        $filename = $class.'Form.php';

        $form = __DIR__.DS.'Resources'.DS.'Form.php';

        $formData = str_replace('{module}', $class, File::getContent($form));

        File::write($path, $filename, $formData);
    }

    /**
     * Make Table Class
     *
     * @param  string $path
     * @param  string $class
     * @return void
     **/
    protected function makeTableClass($path, $class)
    {
        $filename = $class.'Table.php';

        $table = __DIR__.DS.'Resources'.DS.'Table.php';

        $search = array('{module}', '{uri}');
        $replace = array($class, $this->uri);

        $tableData = str_replace($search, $replace, File::getContent($table));

        File::write($path, $filename, $tableData);
    }

    /**
     * Make module views.
     *
     * @param  string  $path
     * @param  boolean $backend
     * @return void
     **/
    protected function makeViews($path, $backend = false)
    {
        $path = $path.DS.'views'.DS;

        $files = array('index', 'view');

        if ($backend) {
            $path = $path.'admin'.DS;
            $files = array_merge($files, array('form'));
        }

        foreach ($files as $file) {
            File::write($path, $file.'.html', $this->getViewContent($file, $backend));
        }

    }

    /**
     * Generate the Model File for Module
     *
     * @param  array $data
     * @return void
     */
    protected function makeModel($data)
    {
        $file = __DIR__.DS.'Resources'.DS.'Model.php';

        $fileData = File::getContent($file);

        $fileData = str_replace('{module}', $data['classname'], $fileData);

        $fileData = str_replace('{table}', $data['table'], $fileData);

        $model = $data['classname'].'.php';

        File::write($data['src_path'].DS.'Model'.DS, $model, $fileData);
    }

    /**
     * Get View Content for Module
     *
     * @param  string  $name
     * @param  boolean $backend
     * @return string
     **/
    protected function getViewContent($name, $backend)
    {
        $path = __DIR__.DS.'Resources'.DS.'views'.DS;
        $path = ($backend) ? $path.'admin'.DS : $path.'public'.DS;

        switch ($name) {
            case 'index':
                $file = $path.'index.html';

                return str_replace('{uri}', $this->uri, File::getContent($file));
                break;

            case 'form':
                return '{{ $form->build() }}';
                break;

            default:
                return '// View File';
                break;
        }
    }

    /**
     * Collect module data from the user
     *
     * @param  OutputInterface $output
     * @return array
     */
    protected function collectData($output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $name = $dialog->ask($output, "<question>Please enter the name of the module : </question>", null);

        $description = $dialog->ask($output, "<question>Please enter the description of the module : </question>", null);

        $author = $dialog->ask($output, "<question>Please enter the author of the module : </question>", null);

        $authorEmail = $dialog->ask($output, "<question>Please enter the author email of the module : </question>", null);

        $authorUrl = $dialog->ask($output, "<question>Please enter the author URL of the module : </question>", null);

        $backend = false;

        if ($dialog->askConfirmation(
        $output,
        '<question>'.$name.' Module is backend support? [Y|n]</question>',
        true
        )) {
            $backend = true;
        }

        $frontend = false;

        if ($dialog->askConfirmation(
        $output,
        '<question>'.$name.' Module is frontend support? [Y|n]</question>',
        true
        )) {
            $frontend = true;
        }

        // Ask for Allow Default Module if Frontend support
        if ($frontend) {
            $allowDefaultModule = false;

            if ($dialog->askConfirmation(
            $output,
            '<question>'.$name.' Module is allow to set default module? [Y|n]</question>',
            true
            )) {
                $allowDefaultModule = true;
            }
        }

        $prefix = $dialog->ask($output, "<question>Please enter the URI Prefix of the module : </question>", null);

        $allowToChangeUriPrefix = false;

        if ($dialog->askConfirmation(
        $output,
        '<question>'.$name.' Module is allow to change the Default URI Prefix? [y|N]</question>',
        false
        )) {
            $allowToChangeUriPrefix = true;
        }

        $this->uri = $prefix;

        $data = array(
                'module' => ucfirst($name),
                'name' => $name,
                'table' => str_replace(' ', '_', strtolower($name)),
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
