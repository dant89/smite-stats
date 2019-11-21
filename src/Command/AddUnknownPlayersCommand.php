<?php

namespace App\Command;

use App\Entity\Player;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUnknownPlayersCommand extends Command
{
    protected static $defaultName = 'smite:store-new-player-ids';

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
        $this->setDescription('Add newly found player IDs to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(Player::class);
        $players = $repository->findAll();

        $playerCount = count($players);
        $output->writeln("{$playerCount} existing players...");

        $existingPlayerIds = [];
        /** @var Player $player */
        foreach ($players as $player) {
            if (!in_array($player->getSmitePlayerId(), $existingPlayerIds)) {
                $existingPlayerIds[] = $player->getSmitePlayerId();
            }
        }

        $matches = $this->smite->getTopMatches();
        if (empty($matches)) {
            $output->writeln("Could not fetch matches");
            exit();
        }

        $matchIds = [];
        foreach ($matches as $match) {
            $matchIds[] = $match['Match'];
        }

        $matchIdsChunks = array_chunk($matchIds, 5);

        $matchCount = 0;
        $newPlayerCount = 0;
        $playerIds = [];

        if (!empty($matchIdsChunks)) {
            foreach ($matchIdsChunks as $matchIdsChunk) {
                $matchDetails = $this->smite->getMatchDetailsBatch($matchIdsChunk);
                if (!empty($matchDetails)) {
                    foreach ($matchDetails as $matchDetail) {
                        if (
                            !is_null($matchDetail['ActivePlayerId']) &&
                            $matchDetail['ActivePlayerId'] !== "0" &&
                            !in_array($matchDetail['ActivePlayerId'], $playerIds) &&
                            !in_array($matchDetail['ActivePlayerId'], $existingPlayerIds)
                        ) {
                            $playerIds[] = $matchDetail['ActivePlayerId'];
                            $newPlayer = new Player();
                            $newPlayer->setSmitePlayerId($matchDetail['ActivePlayerId']);
                            $newPlayer->setDateCreated(new \DateTime());
                            $newPlayer->setDateUpdated(new \DateTime());
                            $this->entityManager->persist($newPlayer);
                            $newPlayerCount++;
                        }
                        $matchCount++;
                    }
                } else {
                    $output->writeln("Could not get match details, skipping...");
                }
            }
        }

        $this->entityManager->flush();
        $output->writeln("{$matchCount} matches scanned...");
        $output->writeln("{$newPlayerCount} new players added!");
    }
}
