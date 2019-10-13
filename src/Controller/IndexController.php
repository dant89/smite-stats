<?php

namespace App\Controller;

use App\Service\SmiteApiClient\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @var Client
     */
    protected $smiteClient;

    /**
     * IndexController constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->smiteClient = $client;
    }

    /**
     * TODO cache API responses to help lower rate usage
     *
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $portalId = [
            'xbox' => 10
        ];

        $clanId = 700241809;

        $timestamp = date('omdHis');

        $authClient = $this->smiteClient->getHttpClient('auth');
        $response = $authClient->authenticate($timestamp);

        $clan = null;
        $clanPlayers = [];

        if ($response->getStatus() === 200) {

            $sessionId = $response->getContent()['session_id'];
            $playerClient = $this->smiteClient->getHttpClient('player');
            $teamClient = $this->smiteClient->getHttpClient('team');

            $clan = $teamClient->teamDetails($clanId, $sessionId, $timestamp)->getContent();
            $clanPlayers = $teamClient->teamPlayers($clanId, $sessionId, $timestamp)->getContent();

            foreach ($clanPlayers as &$clanPlayer) {
                $player = $playerClient->playerIdsByGamertag($clanPlayer['Name'], $portalId['xbox'], $sessionId, $timestamp)->getContent();
                $playerId = $player[0]['player_id'];
                $clanPlayer['Player_info'] = $playerClient->player($playerId, $sessionId, $timestamp)->getContent()[0];
            }
        }

        return $this->render('index/index.html.twig', [
            'clan' => $clan[0],
            'clan_players' => $clanPlayers,
        ]);
    }

}
