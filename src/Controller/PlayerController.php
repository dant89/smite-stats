<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\PlayerSearch;
use App\Service\Smite;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class PlayerController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, Smite $smite)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->smite = $smite;
    }

    /**
     * @Route("/player/", name="players")
     * @param int id
     * @return Response
     */
    public function index(): Response
    {
        $playerRepo = $this->entityManager->getRepository(Player::class);
        /** @var Player $player */
        $newestPlayers = $playerRepo->findNewestPlayerNameNotNull(20);

        return $this->render('player/index.html.twig', [
            'newest_players' => $newestPlayers
        ]);
    }

    /**
     * @Route("/player/{gamertag}-{id}", name="player_view", requirements={"gamertag": "[-\w]+"})
     * @param string $gamertag
     * @param int id
     * @return Response
     * @throws InvalidArgumentException
     */
    public function player(string $gamertag, int $id): Response
    {
        // Check to see if we have this player stored in the database
        $playerRepo = $this->entityManager->getRepository(Player::class);
        /** @var Player $player */
        $player = $playerRepo->findOneBy(['smitePlayerId' => $id]);

        // See if we can find this player via the smite api
        if (is_null($player) || ($player->getCrawled() === 0)) {
            $playerDetails = $this->smite->getPlayerDetailsByPortalId($id);
            if (empty($playerDetails)) {
                throw new NotFoundHttpException();
            }

            // TODO move to a crawler function to ensure less duplicate code
            // Store this player ID in the player database
            $player = new Player();
            $player->setSmitePlayerId($id);
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
            $player->setDateCreated(new \DateTime());
            $player->setDateUpdated(new \DateTime());
            $player->setWins($playerDetails['Wins'] ?? 0);
            $player->setCrawled(1);
            $this->entityManager->persist($player);
            $this->entityManager->flush();
        }

        $playerNameSlug = preg_replace('/\[.*?\]/is', '', $player->getName());
        $playerNameSlug = preg_replace('/([^a-z0-9-]+)/is', '', $playerNameSlug);
        if (empty($playerNameSlug)) {
            // TODO fix issues with Japanese characters being converted to an empty string バイオレット
            throw new NotFoundHttpException('An error occurred fetching a player name.');
        }

        $gods = $this->smite->getGodsByNameKey();

        $achievements = $this->smite->getPlayerAchievements($player->getSmitePlayerId()) ?? [];
        $matches = $this->smite->getPlayerMatches($player->getSmitePlayerId()) ?? [];

        $matchIds = [];
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $matchIds[] = $match['Match'];
            }
        }

        $matchIds = array_slice($matchIds, 0, 5, true);
        $matchDetails = $this->smite->getMatchDetailsBatch($matchIds);

        // TODO use ActivePlayerId
        $formattedMatches = [];
        $test = [];
        $teams = [];
        if (!empty($matchDetails)) {
            foreach ($matchDetails as $matchDetail) {

                $teams[$matchDetail['Match']][$matchDetail['TaskForce']][$matchDetail['ActivePlayerId']] = $matchDetail;

                $formattedMatches[$matchDetail['Match']] = [
                    'Entry_Datetime' => $matchDetail['Entry_Datetime'],
                    'Map_Game' => $matchDetail['Map_Game'],
                    'Match' => $matchDetail['Match'],
                    'Minutes' => $matchDetail['Minutes'],
                    'Region' => $matchDetail['Region'],
                    'Winning_TaskForce' => $matchDetail['Winning_TaskForce'],
                    'Teams' => $teams[$matchDetail['Match']]
                ];

                if ($matchDetail['Match'] === 982362009) {
                    $test[] = $matchDetail;
                }
            }
        }

        // FIXME seems to be a bug if team 1 wins but team 2 is ordered first
        if (!empty($matchDetails)) {
            foreach ($matchDetails as $matchDetail) {
                $win = (array_key_exists($player->getSmitePlayerId(), $teams[$matchDetail['Match']][$matchDetail['Winning_TaskForce']]));
                $formattedMatches[$matchDetail['Match']]['Win'] = $win;
            }
        }

        $playerGods = $this->smite->getPlayerGodDetails($player->getSmitePlayerId()) ?? [];

        $playerStats = [
            'Kills' => 0,
            'Assists' => 0,
            'Deaths' => 0,
        ];

        if (!empty($playerGods)) {
            foreach ($playerGods as $playerGod) {
                $playerStats['Kills'] += $playerGod['Kills'];
                $playerStats['Assists'] += $playerGod['Assists'];
                $playerStats['Deaths'] += $playerGod['Deaths'];
            }
        }

        $playerGodInfo = array_slice($playerGods, 0, 4, true);

        return $this->render('player/player.html.twig', [
            'achievements' => $achievements,
            'player' => $player,
            'player_god_info' => $playerGodInfo,
            'player_stats' => $playerStats,
            'player_name_slug' => $playerNameSlug,
            'gods' => $gods,
            'matches' => $formattedMatches
        ]);
    }

    /**
     * @Route("/player_search", name="player_search")
     * @param Request $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function playerSearch(Request $request): Response
    {
        $playerName = $request->get('player_name');

        $players = [];
        if (!is_null($playerName) && !empty($playerName)) {

            $playerSearch = new PlayerSearch();
            $playerSearch->setTerm($playerName);
            $playerSearch->setDateSearched(new \DateTime());
            $this->entityManager->persist($playerSearch);
            $this->entityManager->flush();

            $players = $this->smite->searchPlayerByName($playerName);

            $playerRepo = $this->entityManager->getRepository(Player::class);
            /** @var Player $player */
            foreach ($players as $player) {
                $existingPlayer = $playerRepo->findOneBy(['smitePlayerId' => $player['player_id']]);
                if (is_null($existingPlayer)) {
                    $newPlayer = new Player();
                    $newPlayer->setSmitePlayerId($player['player_id']);
                    $newPlayer->setDateCreated(new \DateTime());
                    $newPlayer->setDateUpdated(new \DateTime());
                    $this->entityManager->persist($newPlayer);
                }
                $this->entityManager->flush();
            }
        }

        return $this->render('player/search.html.twig', [
            'players' => $players,
            'search_term' => $playerName
        ]);
    }
}
