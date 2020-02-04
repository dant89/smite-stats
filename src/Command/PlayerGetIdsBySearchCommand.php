<?php

namespace App\Command;

use App\Entity\Player;
use App\Service\SmiteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayerGetIdsBySearchCommand extends Command
{
    protected static $defaultName = 'smite:player:search-player-ids';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SmiteService
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager, SmiteService $smite)
    {
        $this->entityManager = $entityManager;
        $this->smite = $smite;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Search for and store player IDs that aren\'t currently in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $players = $playerRepo->findBy(['crawled' => 1], ['id' => 'DESC'], 100);

        $searchTerms = 0;
        $newPlayers = 0;
        $currentPlayer = 1;
        $playerCount = count($players);

        if (!empty($players)) {
            /** @var Player $player */
            foreach ($players as $player) {
                $output->writeln("Searching player {$currentPlayer} of {$playerCount}...");

                $nameParts = explode(' ', $player->getName());
                foreach ($nameParts as $namePart) {
                    $searchPlayers = $this->smite->searchPlayerByName($namePart) ?? [];
                    $searchTerms++;

                    $newPlayerIds = [];
                    foreach ($searchPlayers as $searchPlayer) {
                        $playerId = (int) $searchPlayer['player_id'] ?? null;
                        if (!is_null($playerId) && $playerId !== 0) {
                            if (!in_array($playerId, $newPlayerIds)) {
                                $newPlayerIds[] = $playerId;
                                $existingPlayer = $playerRepo->findOneBy(['smitePlayerId' => $playerId]);
                                if (is_null($existingPlayer)) {
                                    $newPlayer = new Player();
                                    $newPlayer->setSmitePlayerId($searchPlayer['player_id']);
                                    $newPlayer->setDateCreated(new \DateTime());
                                    $newPlayer->setDateUpdated(new \DateTime());
                                    $this->entityManager->persist($newPlayer);
                                    $newPlayers++;
                                }
                            }
                        }
                    }
                    $newPlayersCount = count($newPlayerIds);
                    $output->writeln("Adding {$newPlayersCount} new players!");

                    $this->entityManager->flush();
                }
                $currentPlayer++;
            }
        }

        $output->writeln("{$searchTerms} terms searched...");
        $output->writeln("{$newPlayers} new players added!");

        return 0;
    }
}
