<?php

namespace Reborn\Console;

use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Compile Command for Memeory Reduce
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class CompileCommand extends SfCommand
{

    /**
     * Configures the current command.
     */
	protected function configure()
    {
        $this->setName('compile')
            ->setDescription('PHP Class File Compiler for Performance');
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
		$output->writeln("<info>--- Start Compiling ---</info>");

        $outputfile = STORAGES.'compile.php';

        if (file_exists($outputfile)) {
            @unlink($outputfile);
        }

		$files = \Reborn\Config\Config::get('compile_lists');

		if (!$handle = fopen($outputfile, 'w')) {
            throw new \RuntimeException(
                "Unable to open {$outputfile} for writing"
            );
        }

        fwrite($handle, "<?php\n");
        $output->writeln('Compiling classes');
        foreach ($files as $file) {
            $output->writeln("Written for {$file}");
            fwrite($handle, $this->getContent($file) . "\n");
        }
        fclose($handle);

        $output->writeln("<info>Finish compiled and written to {$outputfile}</info>");
    }

    /**
     * Get Class Content  from given file
     *
     * @param string $file
     * @return string
     **/
    protected function getContent($file)
    {
    	if (!is_readable($file)) {
    		throw new \RuntimeException("Cannot open {".$file."} for reading");
    	}

        // Get content without comment and whitespace
    	$content = php_strip_whitespace($file);

    	if (substr($content, 0, 5) == "<?php") {
            $content = substr($content, 5);
        }

        if (false === strpos($content, 'namespace ') and
            $content !== ''
            ) {
            $content = "namespace {\n" . $content . "\n}\n";
        }

        return $content;
    }

}
