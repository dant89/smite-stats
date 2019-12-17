<?php

namespace App\Command;

use App\Entity\Player;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayerUpdateCommand extends Command
{
    protected static $defaultName = 'smite:player:update';

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
        $this->setDescription('Update the data we have on a player');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(Player::class);

        $players = $repository->findBy(['crawled' => 0]);
        $playerCount = count($players);

        $output->writeln("{$playerCount} players to update...");

        $updatedPlayers = 0;
        if (!empty($players)) {
            $playersBatch = array_chunk($players, 5);
            $batchCount = count($playersBatch);
            $batchProcessedCount = 1;
            foreach ($playersBatch as $players) {
                $output->writeln("Processing batch {$batchProcessedCount} of {$batchCount}...");
                /** @var Player $player */
                foreach ($players as $player) {
                    $playerId = $player->getSmitePlayerId();
                    $playerDetails = $this->smite->getPlayerDetailsByPortalId($playerId);

                    $player->setAvatarUrl($playerDetails['Avatar_URL']);
                    $player->setDateRegistered(new \DateTime($playerDetails['Created_Datetime']));
                    $player->setDateLastLogin(new \DateTime($playerDetails['Last_Login_Datetime']));
                    $player->setHoursPlayed($playerDetails['HoursPlayed'] ?? 0);
                    $player->setLeaves($playerDetails['Leaves'] ?? 0);
                    $player->setLevel($playerDetails['Level'] ?? 0);
                    $player->setLosses($playerDetails['Losses'] ?? 0);
                    $player->setMasteryLevel($playerDetails['MasteryLevel'] ?? 0);
                    $player->setName($playerDetails['Name']);
                    $player->setPersonalStatusMessage($playerDetails['Personal_Status_Message']);
                    $player->setRankStatConquest($playerDetails['Rank_Stat_Conquest'] ?? 0);
                    $player->setRankStatDuel($playerDetails['Rank_Stat_Duel'] ?? 0);
                    $player->setRankStatJoust($playerDetails['Rank_Stat_Joust'] ?? 0);
                    $player->setRegion($playerDetails['Region']);
                    $player->setTeamId($playerDetails['TeamId']);
                    $player->setTeamName($playerDetails['Team_Name']);
                    $player->setTierConquest($playerDetails['Tier_Conquest'] ?? 0);
                    $player->setTierDuel($playerDetails['Tier_Duel'] ?? 0);
                    $player->setTierJoust($playerDetails['Tier_Joust'] ?? 0);
                    $player->setTotalAchievements($playerDetails['Total_Achievements'] ?? 0);
                    $player->setTotalWorshippers($playerDetails['Total_Worshippers'] ?? 0);
                    $player->setWins($playerDetails['Wins'] ?? 0);
                    $player->setCrawled(1);
                    $this->entityManager->persist($player);
                    $updatedPlayers++;
                    $output->writeln("Updated {$updatedPlayers} of {$playerCount} players");
                }
                $output->writeln("Saving batch {$batchProcessedCount} of {$batchCount}...");
                $batchProcessedCount++;
                $this->entityManager->flush();
            }
        }

        $output->writeln("{$updatedPlayers} players updated!");

        return 0;
    }
}
