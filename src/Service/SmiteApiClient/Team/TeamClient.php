<?php

namespace App\Service\SmiteApiClient\Team;

use App\Service\SmiteApiClient\AbstractHttpClient;
use App\Service\SmiteApiClient\Response;

class TeamClient extends AbstractHttpClient
{
    /**
     * @param string $searchTerm
     * @param string $sessionId
     * @param string $timestamp
     * @return Response
     */
    public function searchTeams(string $searchTerm, string $sessionId, string $timestamp): Response
    {
        $signature = $this->generateSignature('searchteams', $timestamp);
        $uri = "/searchteamsJson/{$this->client->getDevId()}/{$signature}/{$sessionId}/{$timestamp}/{$searchTerm}";

        return $this->get($uri);
    }

    /**
     * @param string $clanId
     * @param string $sessionId
     * @param string $timestamp
     * @return Response
     */
    public function teamDetails(string $clanId, string $sessionId, string $timestamp): Response
    {
        $signature = $this->generateSignature('getteamdetails', $timestamp);
        $uri = "/getteamdetailsJson/{$this->client->getDevId()}/{$signature}/{$sessionId}/{$timestamp}/{$clanId}";

        return $this->get($uri);
    }

    /**
     * @param string $clanId
     * @param string $sessionId
     * @param string $timestamp
     * @return Response
     */
    public function teamPlayers(string $clanId, string $sessionId, string $timestamp): Response
    {
        $signature = $this->generateSignature('getteamplayers', $timestamp);
        $uri = "/getteamplayersJson/{$this->client->getDevId()}/{$signature}/{$sessionId}/{$timestamp}/{$clanId}";

        return $this->get($uri);
    }
}
