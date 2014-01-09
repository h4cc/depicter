<?php


namespace Silpion\Depicter\Cli;

use Silpion\Depicter\Cli\Command\CheckLimitsCommand;
use Silpion\Depicter\Cli\Command\ReportGenerateCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('silpion/depicter', 'alpha');
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new ReportGenerateCommand();
        $commands[] = new CheckLimitsCommand();

        return $commands;
    }
}