<?php

namespace App\Service;

use Dant89\SmiteApiClient\Client;
use Dant89\SmiteApiClient\God\GodClient;
use Dant89\SmiteApiClient\Player\PlayerClient;
use Dant89\SmiteApiClient\Player\PlayerInfoClient;
use Dant89\SmiteApiClient\Team\TeamClient;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Smite
{
    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var Client
     */
    protected $smiteClient;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $timestamp;

    /**
     * Smite constructor.
     * @param Client $client
     * @param AdapterInterface $cache
     * @throws InvalidArgumentException
     */
    public function __construct(Client $client, AdapterInterface $cache)
    {
        $this->smiteClient = $client;
        $this->cache = $cache;
        $this->timestamp = date('omdHis');
        $this->sessionId = $this->authenticate();
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function authenticate(): string
    {
        $session = $this->cache->getItem('smite_session_id');
        if ($session->isHit()) {
            return $session->get();
        }

        $authClient = $this->smiteClient->getHttpClient('auth');
        $response = $authClient->createSession($this->timestamp);

        if ($response->getStatus() === 200) {
            $sessionId = $response->getContent()['session_id'];
            $session->set($sessionId);
            $session->expiresAfter(600); // 10 minutes
            $this->cache->save($session);
            return $sessionId;
        }

        throw new \RuntimeException('Could not authenticate.');
    }

    /**
     * @param string $gamertag
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPlayerDetailsByGamertag(string $gamertag): array
    {
        $portalId = [
            'xbox' => 10
        ];

        $gamertagLower = strtolower($gamertag);
        $cache = $this->cache->getItem("smite_player_{$gamertagLower}");
        if ($cache->isHit()) {
            return $cache->get();
        }

        /** @var PlayerClient $playerClient */
        $playerClient = $this->smiteClient->getHttpClient('player');

        $playerIdResponse = $playerClient->getPlayerIdsByGamertag($portalId['xbox'], $gamertag, $this->sessionId, $this->timestamp);
        $playerId = $playerIdResponse->getContent()[0]['player_id'];

        $playerResponse = $playerClient->getPlayer($playerId, $this->sessionId, $this->timestamp);

        if ($playerResponse->getStatus() === 200) {
            $data = $playerResponse->getContent()[0];
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data;
        }
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getGods(): array
    {
        $cache = $this->cache->getItem("smite_gods");
        if ($cache->isHit()) {
            return $cache->get();
        }

        /** @var GodClient $godClient */
        $godClient = $this->smiteClient->getHttpClient('god');
        $response = $godClient->getGods($this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
            return $data;
        }
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getGodsFormatted(): array
    {
        $gods = $this->getGods();
        $formattedGods = [];

        foreach ($gods as $god) {
            $formattedGods[$god['Name']] = $god;
        }

        return $formattedGods;
    }

    /**
     * @param string $playerId
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPlayerGodDetails(string $playerId): array
    {
        $cache = $this->cache->getItem("smite_player_gods_{$playerId}");
        if ($cache->isHit()) {
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->getGodRanks($playerId, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data;
        }
    }

    /**
     * @param string $id
     * @return array
     * @throws InvalidArgumentException
     */
    public function getTeamDetails(string $id): array
    {
        $cache = $this->cache->getItem("smite_team_{$id}");
        if ($cache->isHit()) {
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->getTeamDetails($id, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent()[0];
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
            return $data;
        }
    }

    /**
     * @param string $term
     * @return array
     * @throws InvalidArgumentException
     */
    public function searchTeams(string $term): array
    {
        $cache = $this->cache->getItem("smite_team_search_{$term}");
        if ($cache->isHit()) {
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->searchTeams($term, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent()[0];
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
            return $data;
        }
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getTeamPlayers(): array
    {
        $teamId = 700241809;

        $cache = $this->cache->getItem("smite_team_players_{$teamId}");
        if ($cache->isHit()) {
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->getTeamPlayers($teamId, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
            return $data;
        }
    }
}
