<?php

namespace App\Command;

use App\Entity\MatchPlayer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MatchStatsCommand extends Command
{
    protected static $defaultName = 'smite:match:stats';

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
        $this->setDescription('Get stats on currently stored matches');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(MatchPlayer::class);
        $matchCount = $repository->count([]);

        $output->writeln("{$matchCount} matches stored");

        $newMatches = $repository->findBy(['crawled' => 0]);
        $newMatchesCount = count($newMatches);

        $output->writeln("{$newMatchesCount} matches ready to crawl");

        return 0;
    }
}
