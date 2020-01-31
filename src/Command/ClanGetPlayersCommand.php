<?php

namespace App\Command;

use App\Entity\Clan;
use App\Entity\Player;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClanGetPlayersCommand extends Command
{
    protected static $defaultName = 'smite:clan:store-player-ids';

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
        $this->setDescription('Store clan player IDs that aren\'t currently in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clanRepo = $this->entityManager->getRepository(Clan::class);
        $playerRepo = $this->entityManager->getRepository(Player::class);
        $clans = $clanRepo->findBy(['crawled' => 1, 'dateLastPlayerCrawl' => null]);

        $clanCount = count($clans);
        $output->writeln("{$clanCount} clans to crawl...");

        $newPlayerIds = [];

        $clanIndex = 1;
        $playersAddedCount = 0;

        /** @var Clan $clan */
        foreach ($clans as $clan) {
            if ($clan->getSmiteClanId()) {
                $clanPlayers = $this->smite->getTeamPlayers($clan->getSmiteClanId());
                if (!empty($clanPlayers)) {
                    $clanPlayersCount = count($clanPlayers);
                    $output->writeln("{$clanPlayersCount} players in clan {$clanIndex}...");
                    foreach ($clanPlayers as $clanPlayer) {
                        $clanPlayerName = $clanPlayer['Name'] ?? null;
                        if (!is_null($clanPlayerName)) {
                            $player = $this->smite->getPlayerIdByName($clanPlayerName);
                            $playerId = (int) $player['player_id'] ?? null;
                            if (!is_null($playerId) && $playerId !== 0) {
                                $existingPlayer = $playerRepo->findOneBy(['smitePlayerId' => $playerId]);
                                if (is_null($existingPlayer) && !in_array($playerId, $newPlayerIds)) {
                                    $newPlayerIds[] = $playerId;
                                }
                            }
                        }
                    }
                }
            }

            $clan->setDateLastPlayerCrawl(new \DateTime());
            $this->entityManager->persist($clan);
            $this->entityManager->flush();

            $clanPlayersAdded = 0;
            if (!empty($newPlayerIds)) {
                $newPlayerIds = array_unique($newPlayerIds);
                foreach ($newPlayerIds as $newPlayerId) {
                    $newPlayer = new Player();
                    $newPlayer->setSmitePlayerId($newPlayerId);
                    $newPlayer->setDateCreated(new \DateTime());
                    $newPlayer->setDateUpdated(new \DateTime());
                    $this->entityManager->persist($newPlayer);
                    $clanPlayersAdded++;
                    $playersAddedCount++;
                }
                $this->entityManager->flush();
            }

            $newPlayerIds = [];
            $output->writeln("{$clanPlayersAdded} new players added from clan {$clanIndex}!");

            $clanIndex++;
        }

        $output->writeln("Total {$playersAddedCount} new players added!");

        return 0;
    }
}
