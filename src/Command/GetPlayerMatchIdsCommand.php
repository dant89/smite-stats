<?php

namespace App\Command;

use App\Entity\Clan;
use App\Entity\Player;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPlayerMatchesCommand extends Command
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
        $this->setDescription('Store clan IDs that aren\'t currently in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO improve onscreen output for this command

        $clanRepo = $this->entityManager->getRepository(Clan::class);
        $playerRepo = $this->entityManager->getRepository(Player::class);

        $players = $playerRepo->findByTeamIdNotNull();
        $clanIds = [];
        if (!empty($players)) {
            /** @var Player $player */
            foreach ($players as $player) {
                $clan = $clanRepo->findOneBy(['smiteClanId' => $player->getTeamId()]);
                if (is_null($clan) && !in_array($player->getTeamId(), $clanIds)) {
                    $clanIds[] = $player->getTeamId();
                }
            }
        }

        $storedClansCount = 0;
        if (!empty($clanIds)) {
            foreach ($clanIds as $clanId) {
                $newClan = new Clan();
                $newClan->setSmiteClanId($clanId);
                $newClan->setDateCreated(new \DateTime());
                $newClan->setDateUpdated(new \DateTime());
                $this->entityManager->persist($newClan);
                $storedClansCount++;

                $clanDetails = $this->smite->getTeamDetails($clanId);
                if (!empty($clanDetails) && $clanDetails['TeamId'] !== 0) {
                    $newClan->setName($clanDetails['Name']);
                    $newClan->setFounder($clanDetails['Founder']);
                    $newClan->setTag($clanDetails['Tag']);
                    $newClan->setPlayers($clanDetails['Players']);
                    $newClan->setWins($clanDetails['Wins']);
                    $newClan->setLosses($clanDetails['Losses']);
                    $newClan->setRating($clanDetails['Rating']);
                    $newClan->setCrawled(1);
                    $this->entityManager->persist($newClan);
                }
            }
            $this->entityManager->flush();
        }

        $playersCount = count($players);
        $newClanIdsCount = count($clanIds);

        $output->writeln("{$playersCount} players to grab clan IDs from...");
        $output->writeln("{$newClanIdsCount} new clan IDs!");
        $output->writeln("{$storedClansCount} new clans added!");

        return 0;
    }
}
