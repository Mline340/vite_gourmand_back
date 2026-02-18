<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\CommandeStatsService;

class CommandesStatsSyncProcessor implements ProcessorInterface
{
    public function __construct(private CommandeStatsService $statsService) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $this->statsService->synchroniserStats();
        return ['message' => 'Stats synchronisées'];
    }
     public function synchronize(): void
    {
        $this->statsService->synchroniserStats();
    }
}