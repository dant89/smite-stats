<?php

namespace App\Command;

use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayerStatsCommand extends Command
{
    protected static $defaultName = 'smite:player:stats';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Get stats on currently stored players');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(Player::class);
        $playersCount = $repository->count([]);

        $output->writeln("{$playersCount} players stored");

        $newPlayers = $repository->findBy(['crawled' => 0]);
        $newPlayersCount = count($newPlayers);

        $output->writeln("{$newPlayersCount} players ready to crawl");

        return 0;
    }
}
