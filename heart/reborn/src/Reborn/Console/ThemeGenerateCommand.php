<?php

namespace Reborn\Console;

use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;
use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Theme Skeleton Generator Command class for Reborn
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class ThemeGenerateCommand extends SfCommand
{

    /**
     * Configures the current command.
     */
	protected function configure()
    {
        $this->setName('theme:generate')
            ->setDescription('Theme skeleton generate from console');
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

		$output->writeln("<info>Theme create......</info>");

		$this->create($data);

		$name = ucwords(str_replace('_', ' ', $data['name']));

        $output->writeln("<info>Theme $name created</info>");
    }

    /**
     * Create the module process
     *
     * @param array $data
     * @return void
     */
    protected function create($data)
    {
    	$path = ($data['share']) ? SHARED.'themes'.DS.$data['name'] : THEMES.$data['name'];

    	if (Dir::is($path)) {
    		throw new \Exception("Theme ".$data['name']." is already exists!");
    	}

        // make theme folder
    	Dir::make($path);

    	// make assets folder
    	Dir::make($path.DS.'assets');
    	Dir::make($path.DS.'assets'.DS.'css');
    	Dir::make($path.DS.'assets'.DS.'js');
    	Dir::make($path.DS.'assets'.DS.'img');

    	// make view folder
    	Dir::make($path.DS.'views');
    	Dir::make($path.DS.'views'.DS.'layout');
    	Dir::make($path.DS.'views'.DS.'partial');

    	$this->makeLayout($path.DS.'views'.DS.'layout'.DS);
    	$this->makePartials($path.DS.'views'.DS.'partial'.DS);
    	$this->makeNotFound($path.DS.'views'.DS);
    	$this->makeThemeInfo($data, $path);
    }

    /**
     * Make Theme Default Layout File
     *
     * @param string $path
     * @return void
     **/
    protected function makeLayout($path)
    {
    	$content = <<<EOT
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	{{ include:metadata }}
</head>
<body>

	{{ include:header }}

	<div id="content">
		{{ \$layoutBody }}
	</div>

	{{ include:footer }}

	{{ \$footerScript }}

	{{ \$footerScriptInline }}

</body>
</html>
EOT;

    	File::write($path, 'default.html', $content);
    }

    /**
     * Make Theme Partial Files
     *
     * @param string $path
     * @return void
     **/
    protected function makePartials($path)
    {
    	// Make metadata.html file
    	$metadata = $this->getMetadata();
    	File::write($path, 'metadata.html', $metadata);

    	// Make header.html file
    	$header = $this->getHeader();
    	File::write($path, 'header.html', $header);

    	// Make footer.html file
    	$footer = $this->getFooter();
    	File::write($path, 'footer.html', $footer);
    }

    /**
     * Make Theme Info File
     *
     * @param array $info
     * @param string $path
     * @return void
     **/
    protected function makeThemeInfo($info, $path)
    {
    	$name = ucwords(str_replace('_', ' ', $info['name']));

    	$content = <<<EOT
name = {$name}
description = Description for {$name} Theme.
author = {$info['author']}
website = {$info['website']}
version = {$info['version']}
min-require = 2.0
EOT;

    	File::write($path, 'theme.info', $content);
    }

    /**
     * Get metadata file's content
     *
     * @return string
     **/
    protected function getMetadata()
    {
    	$content = <<<EOT
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ \$layoutTitle }} | {{ setting('site_title') }}</title>
{{ Html::favicon() }}

{{ \$headerStyle }}
<!-- CSS file for theme -->
{{ css('style.css') }}

{{ \$headerScript }}

{{ \$headerScriptInline }}
EOT;
		return $content;
    }

    /**
     * Get header file's content
     *
     * @return string
     **/
    protected function getHeader()
    {
    	$content = <<<EOT
<div id="menubar" class="container clearfix">
	<div id="logo" class="clearfix">
		<h1>
			<a href="{{ url() }}">{{ setting('site_title') }}</a> | <span id="slogan">{{ setting('site_slogan') }}</span>
		</h1>
	</div>

	<nav>
		{{ nav:header }}
	</nav>

</div>
EOT;
		return $content;
    }

    /**
     * Get footer file's content
     *
     * @return string
     **/
    protected function getFooter()
    {
    	$content = <<<EOT
<div id="footer">
	<div class="container">
		<p class="footer-text">Proudly Powered by <a href="http://www.reborncms.com">RebornCMS</a></p>
	</div>
</div>
EOT;
		return $content;
    }

    /**
     * Make 404 page not found page
     *
     * @return
     **/
    protected function makeNotFound($path)
    {
    	$content = <<<EOT
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>404 | {{ setting('site_title') }}</title>
	{{ Html::favicon() }}

	{{ css('style.css') }}

</head>
<body>

	{{ include:header }}

	<div id="main">
		<div class="container clearfix">
			<div id="content">
				<h2>404 Page Not Found.</h2>
				<p>We cannot find what you like to know in this site. Please click <a href="{{ url() }}">Here</a> to go back to home page. </p>
			</div>
		</div>
	</div>

	{{ include:footer }}

</body>
</html>
EOT;
		File::write($path, '404.html', $content);
    }

    /**
     * Collect theme data from the user
     *
     * @param OutputInterface $output
     * @return array
     */
    protected function collectData($output)
    {
    	$dialog = $this->getHelperSet()->get('dialog');

        $name = $dialog->ask($output, "<question>Please enter the name of the theme [eg: bootstrapper] : </question>", null);

        $share = false;

        if ($dialog->askConfirmation(
        $output,
        '<question>Want to share for '.$name.' theme? [Y|n]</question>',
        true
    	)) {
    		$share = true;
		}

        $version = $dialog->ask($output, "<question>Please enter the version of the module [1.0] : </question>", '1.0');


		$author = $dialog->ask($output, "<question>Please enter the author of the theme : </question>", null);

		$website = $dialog->ask($output, "<question>Please enter the author Website URL of the theme : </question>", null);

		$data = array(
				'name' => str_replace(' ', '_', strtolower($name)),
				'version' => $version,
				'share' => $share,
				'author' => $author,
				'website' => $website
			);

		return $data;
    }

} // END class ThemeGenerateCommand
