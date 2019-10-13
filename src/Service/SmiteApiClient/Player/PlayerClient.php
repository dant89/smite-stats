<?php

namespace App\Service\SmiteApiClient\Player;

use App\Service\SmiteApiClient\AbstractHttpClient;
use App\Service\SmiteApiClient\Response;

class PlayerClient extends AbstractHttpClient
{
    /**
     * @param string $playerId
     * @param string $sessionId
     * @param string $timestamp
     * @return Response
     */
    public function player(string $playerId, string $sessionId, string $timestamp): Response
    {
        $signature = $this->generateSignature('getplayer', $timestamp);
        $uri = "/getplayerJson/{$this->client->getDevId()}/{$signature}/{$sessionId}/{$timestamp}/{$playerId}";

        return $this->get($uri);
    }

    /**
     * @param string $playerName
     * @param string $sessionId
     * @param string $timestamp
     * @return Response
     */
    public function playerIdByName(string $playerName, string $sessionId, string $timestamp): Response
    {
        $signature = $this->generateSignature('getplayeridbyname', $timestamp);
        $uri = "/getplayeridbynameJson/{$this->client->getDevId()}/{$signature}/{$sessionId}/{$timestamp}/{$playerName}";

        return $this->get($uri);
    }
    /**
     * @param string $gamertag
     * @param string $sessionId
     * @param string $timestamp
     * @return Response
     */
    public function playerIdsByGamertag(string $gamertag, string $portalId, string $sessionId, string $timestamp): Response
    {
        $signature = $this->generateSignature('getplayeridsbygamertag', $timestamp);
        $uri = "/getplayeridsbygamertagJson/{$this->client->getDevId()}/{$signature}/{$sessionId}/{$timestamp}/{$portalId}/{$gamertag}";

        return $this->get($uri);
    }
}
