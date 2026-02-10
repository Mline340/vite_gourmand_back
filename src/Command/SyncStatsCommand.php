<?php
namespace App\Command;

use App\Service\CommandeStatsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync-stats')]
class SyncStatsCommand extends Command
{
    public function __construct(private CommandeStatsService $statsService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Synchronisation des stats...');
        
        $this->statsService->synchroniserStats();
        
        $output->writeln('Terminé !');
        return Command::SUCCESS;
    }
}