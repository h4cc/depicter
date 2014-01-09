<?php


namespace Silpion\Depicter\Cli\Command;

use Silpion\Depicter\Report\Checker;
use Silpion\Depicter\Report\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CheckLimitsCommand extends Command
{
    protected function configure()
    {
        $this
          ->setName('check:limits')
          ->setDescription('Checking if the given limits are violated by the srutinizer.json report.')
          ->addArgument('limit', InputArgument::OPTIONAL, 'Limiting number of Warnings/Errors found', 100)
          ->
          addArgument('file', InputArgument::OPTIONAL, 'Path to the scrutinizer.json file.', 'scrutinizer.json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = File::createFromFile($input->getArgument('file'));
        $limit = $input->getArgument('limit');

        $checker = new Checker();

        $violations = $checker->findViolations($file);

        $rows = array_map(
            function ($key, $value) {
                return array($key, $value);
            },
            array_keys($violations),
            array_values($violations)
        );

        /** @var \Symfony\Component\Console\Helper\TableHelper $table */
        $table = $this->getApplication()->getHelperSet()->get('table');
        $table
          ->setHeaders(array('Violation', 'Count'))
          ->setRows($rows);
        $table->render($output);

        $sum = array_sum($violations);

        if ($sum >= $limit) {
            $output->writeln('<error>Limit reached. Too much violations found</error>');
            $output->writeln('<error>Found '.$sum.' violations for a limit of '.$limit.'</error>');

            return 1;
        }else{
            $output->writeln('<info>Limit not reached.</info>');
        }
    }
}