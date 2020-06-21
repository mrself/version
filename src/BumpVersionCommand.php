<?php declare(strict_types=1);

namespace Mrself\Version;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BumpVersionCommand extends Command
{
    protected static $defaultName = 'app:bump_version';

    /**
     * @var string
     */
    private $workingDir;

    public function __construct(string $workingDir)
    {
        $this->workingDir = $workingDir;
        parent::__construct(null);
    }

    protected function configure()
    {
        parent::configure();
        $this->addArgument(
            'version',
            InputArgument::REQUIRED,
            NewVersion::getHelpMessage()
        );

        $this->addOption(
            'directory',
            null,
            InputOption::VALUE_REQUIRED,
            'The repository directory'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getArgument('version');
        $directory = $input->getOption('directory') ?: $this->workingDir;
        try {
            (new NewVersion($directory, $version))->new();
        } catch (\RuntimeException $e) {
            $output->writeln("<error>#{$e->getMessage()}</error>");
        }

        $output->writeln('<info>Done</info>');
        return 0;
    }

}