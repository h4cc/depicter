<?php


namespace Silpion\Depicter\Cli\Command;

use Silpion\Depicter\Report\File;
use Silpion\Depicter\Report\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ReportGenerateCommand extends Command
{
    protected function configure()
    {
        $this
          ->setName('report:generate')
          ->setDescription('Will generate a report from a given scrutinizer.json file.')
          ->addArgument('target', InputArgument::OPTIONAL, 'Target path where report should be stored.', 'report/')
          ->addArgument('source', InputArgument::OPTIONAL, 'Path to the analyzed source files.', 'src/')
          ->addArgument('file', InputArgument::OPTIONAL, 'Path to the scrutinizer.json file.', 'scrutinizer.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = File::createFromFile($input->getArgument('file'));
        $source = $input->getArgument('source');

        $target = $input->getArgument('target');
        if (!is_dir($target)) {
            // Generate target directory
            $fs = new Filesystem();
            $fs->mkdir($target, 0770);
        }

        $generator = new Generator();
        $generator->generateReport($file, $target, $source);

        $output->writeln("Depicter report generated from '$source' to '$target'");
    }
}