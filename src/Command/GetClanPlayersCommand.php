<?php

namespace App\Command;

use App\Entity\Clan;
use App\Entity\Player;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetClanPlayersCommand extends Command
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
        $clans = $clanRepo->findBy(['crawled' => 1]);

        $clanCount = count($clans);
        $output->writeln("{$clanCount} existing clans...");

        $newPlayerIds = [];

        /** @var Clan $clan */
        foreach ($clans as $clan) {
            if ($clan->getSmiteClanId()) {
                $clanPlayers = $this->smite->getTeamPlayers($clan->getSmiteClanId());
                foreach ($clanPlayers as $clanPlayer) {

                    $clanPlayerName = $clanPlayer['Name'] ?? null;
                    $output->writeln($clanPlayer['Name']);
                    if (!is_null($clanPlayerName)) {
                        $player = $this->smite->getPlayerIdByName($clanPlayerName);
                        $playerId = $player['player_id'] ?? null;
                        if (!is_null($playerId)) {
                            $existingPlayer = $playerRepo->findOneBy(['smitePlayerId' => $playerId]);
                            if (is_null($existingPlayer)) {
                                $newPlayerIds[] = $playerId;
                            }
                        }
                    }
                }
            }
        }

        $playersAddedCount = 0;

        if (!empty($newPlayerIds)) {
            foreach ($newPlayerIds as $newPlayerId) {
                $newPlayer = new Player();
                $newPlayer->setSmitePlayerId($newPlayerId);
                $newPlayer->setDateCreated(new \DateTime());
                $newPlayer->setDateUpdated(new \DateTime());
                $this->entityManager->persist($newPlayer);
                $playersAddedCount++;
            }
            $this->entityManager->flush();
        }

        $newPlayerIdsCount = count($newPlayerIds);

        $output->writeln("{$newPlayerIdsCount} new player IDs...");
        $output->writeln("{$playersAddedCount} new players added!");

        return 0;
    }
}
