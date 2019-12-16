<?php

namespace App\Command;

use App\Entity\Clan;
use App\Entity\Player;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetPlayerClanIdsCommand extends Command
{
    protected static $defaultName = 'smite:player:store-clan-ids';

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
        $clanRepo = $this->entityManager->getRepository(Clan::class);
        $playerRepo = $this->entityManager->getRepository(Player::class);

        $playersCount = $playerRepo->count([]);
        $perPage = '500';
        $pageCount = ceil($playersCount / $perPage);

        $output->writeln("{$playersCount} players to grab clan IDs from.");
        $output->writeln("Players per batch {$perPage}");

        $query = $playerRepo->findNewestPlayerNameNotNullQuery();
        $paginator = new Paginator($query);

        $i = 0;
        $totalStoredClansCount = 0;

        for ($p=1; $p<=$pageCount; $p++) {
            $output->writeln("Player batch {$p} of {$pageCount}...");

            $offset = $perPage * ($p-1);
            $paginator
                ->getQuery()
                ->setFirstResult($offset)
                ->setMaxResults($perPage);

            $clanIds = [];
            /** @var Player $player */
            foreach ($paginator as $player) {
                $clan = $clanRepo->findOneBy(['smiteClanId' => $player->getTeamId()]);
                if (is_null($clan) && !in_array($player->getTeamId(), $clanIds) && !is_null($player->getTeamId())) {
                    $clanIds[] = $player->getTeamId();
                }
                $i++;
            }
            $newClanIdsCount = count($clanIds);

            $storedClansCount = 0;
            if (!empty($clanIds)) {
                $output->writeln("{$newClanIdsCount} new clan IDs!");

                foreach ($clanIds as $clanId) {
                    $newClan = new Clan();
                    $newClan->setSmiteClanId($clanId);
                    $newClan->setDateCreated(new \DateTime());
                    $newClan->setDateUpdated(new \DateTime());
                    $this->entityManager->persist($newClan);
                    $storedClansCount++;
                    $totalStoredClansCount++;

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
                $output->writeln("{$storedClansCount} new clans added!");
            }
        }
        return 0;
    }
}
