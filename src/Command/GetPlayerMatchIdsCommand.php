<?php

namespace App\Command;

use App\Entity\Player;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPlayerMatchIdsCommand extends Command
{
    protected static $defaultName = 'smite:player:store-match-ids';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager, Smite $smite)
    {
        $this->entityManager = $entityManager;
        $this->smite = $smite;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Store match IDs that aren\'t currently in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $players = $playerRepo->findBy(['crawled' => 1]);

        if (!empty($players)) {
            foreach ($players as $player) {
                $playerMatches = $this->smite->getPlayerMatches($player->getSmitePlayerId()) ?? [];
                if (!empty($playerMatches)) {
                    foreach ($playerMatches as $playerMatch) {
                        $playerMatchDetail = $this->smite->getMatchDetailsBatch([$playerMatch['Match']]);

                        // TODO figure out how best to store matches and any match details required
                        return 0;
                    }
                }
            }
        }
        
        return 0;
    }
}
