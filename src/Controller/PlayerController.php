<?php

namespace App\Controller;

use App\Service\Smite;
use Cocur\Slugify\SlugifyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Cache\InvalidArgumentException;

class PlayerController extends AbstractController
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SlugifyInterface
     */
    protected $slugify;

    /**
     * @var Smite
     */
    protected $smite;

    public function __construct(LoggerInterface $logger ,SlugifyInterface $slugify, Smite $smite)
    {
        $this->logger = $logger;
        $this->slugify = $slugify;
        $this->smite = $smite;
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
        $player = $this->smite->getPlayerDetailsByPortalId($id);
        if (empty($player)) {
            throw new NotFoundHttpException();
        }

        $playerNameSlug = preg_replace('/\[.*?\]/is', '', $player['Name']);
        $playerNameSlug = $this->slugify->slugify($playerNameSlug);

        $achievements = $this->smite->getPlayerAchievements($player['Id']);
        $gods = $this->smite->getGodsFormatted();
        $matches = $this->smite->getPlayerMatches($player['Id']);

        $matchIds = [];
        foreach ($matches as $match) {
            $matchIds[] = $match['Match'];
        }

        $matchIds = array_slice($matchIds, 0, 10, true);
        $matchDetails = $this->smite->getMatchDetailsBatch($matchIds);

        $formattedMatches = [];
        $teams = [];
        foreach ($matchDetails as $matchDetail) {

            $teams[$matchDetail['Match']][$matchDetail['TaskForce']][$matchDetail['playerId']] = $matchDetail;

            $formattedMatches[$matchDetail['Match']] = [
                'Entry_Datetime' => $matchDetail['Entry_Datetime'],
                'Map_Game' => $matchDetail['Map_Game'],
                'Match' => $matchDetail['Match'],
                'Minutes' => $matchDetail['Minutes'],
                'Region' => $matchDetail['Region'],
                'Winning_TaskForce' => $matchDetail['Winning_TaskForce'],
                'Win' => (array_key_exists($player['Id'], $teams[$matchDetail['Match']][$matchDetail['Winning_TaskForce']])),
                'Teams' => $teams[$matchDetail['Match']]
            ];
        }

        $playerGods = $this->smite->getPlayerGodDetails($player['Id']);

        $playerStats = [
            'Kills' => 0,
            'Assists' => 0,
            'Deaths' => 0,
        ];

        foreach($playerGods as $playerGod) {
            $playerStats['Kills'] += $playerGod['Kills'];
            $playerStats['Assists'] += $playerGod['Assists'];
            $playerStats['Deaths'] += $playerGod['Deaths'];
        }

        $player['God_info'] = array_slice($playerGods, 0, 4, true);
        $player['Stats_info'] = $playerStats;

        return $this->render('player/player.html.twig', [
            'achievements' => $achievements,
            'player' => $player,
            'player_name_slug' => $playerNameSlug,
            'gods' => $gods,
            'matches' => $formattedMatches
        ]);
    }
}
