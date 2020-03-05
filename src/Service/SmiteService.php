<?php

namespace App\Service;

use App\Entity\ApiCall;
use Dant89\SmiteApiClient\Authentication\AuthenticationClient;
use Dant89\SmiteApiClient\Client;
use Dant89\SmiteApiClient\God\GodClient;
use Dant89\SmiteApiClient\Item\ItemClient;
use Dant89\SmiteApiClient\League\LeagueClient;
use Dant89\SmiteApiClient\Match\MatchClient;
use Dant89\SmiteApiClient\Player\PlayerClient;
use Dant89\SmiteApiClient\Player\PlayerInfoClient;
use Dant89\SmiteApiClient\Team\TeamClient;
use Dant89\SmiteApiClient\Tool\ToolClient;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class SmiteService
{
    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @throws InvalidArgumentException
     */
    public function __construct(
        Client $client,
        AdapterInterface $cache,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->smiteClient = $client;
        $this->cache = $cache;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->timestamp = date('omdHis');
        $this->authenticate();
    }

    /**
     * @return bool
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function authenticate(): bool
    {
        $cacheKey = $this->generateCacheKey('smite_session_id');
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            $this->sessionId = $cache->get();
            return true;
        }

        $authClient = $this->smiteClient->getHttpClient('auth');
        $response = $authClient->createSession($this->timestamp);

        $this->logApiCall($cache->getKey(), false, $response->getStatus());

        if ($response->getStatus() === 200) {
            $sessionId = $response->getContent()['session_id'];
            $cache->set($sessionId);
            $cache->expiresAfter(600); // 12 minutes
            $this->cache->save($cache);
            $this->sessionId = $sessionId;
            return true;
        }

        $this->logger->critical('Failed to authenticate.', [
            'response' => $response
        ]);

        $this->sessionId = null;
        return false;
    }

    /**
     * @return array|null
     */
    public function ping(): ?array
    {
        /** @var ToolClient $toolClient */
        $toolClient = $this->smiteClient->getHttpClient('tool');
        $response = $toolClient->ping();

        return $response->getContent();
    }

    /**
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getGods(): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_gods');
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var GodClient $godClient */
        $godClient = $this->smiteClient->getHttpClient('god');
        $response = $godClient->getGods($this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param int $queue
     * @param string $godId
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getTopGods(int $queue, string $godId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_top_gods', $queue . $godId);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var GodClient $godClient */
        $godClient = $this->smiteClient->getHttpClient('god');
        $response = $godClient->getGodLeaderboard($godId, $queue, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $queue
     * @param string $tier
     * @param string $round
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getLeagueLeaderboard(string $queue, string $tier, string $round): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_leaderboards', implode('_', [$queue, $tier, $round]));
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var LeagueClient $godClient */
        $leagueClient = $this->smiteClient->getHttpClient('league');
        $response = $leagueClient->getLeagueLeaderboard($queue, $tier, $round, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $matchId
     * @return array
     * @throws InvalidArgumentException
     */
    public function getMatchDetails(string $matchId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey("smite_team_match_detail_{$matchId}");
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var MatchClient $teamClient */
        $matchClient = $this->smiteClient->getHttpClient('match');
        $response = $matchClient->getMatchDetails($matchId, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param array $matchIds
     * @return array
     * @throws InvalidArgumentException
     */
    public function getMatchDetailsBatch(array $matchIds): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_team_match_details', implode('_', $matchIds));
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var MatchClient $teamClient */
        $matchClient = $this->smiteClient->getHttpClient('match');
        $response = $matchClient->getMatchDetailsBatch($matchIds, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $matchId
     * @return array
     * @throws InvalidArgumentException
     */
    public function getMatchPlayerDetails(string $matchId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey("smite_team_match_player_details_{$matchId}");
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var MatchClient $teamClient */
        $matchClient = $this->smiteClient->getHttpClient('match');
        $response = $matchClient->getMatchPlayerDetails($matchId, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $playerId
     * @return array|null
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function getPlayerAchievements(string $playerId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_player_achievements', $playerId);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->getPlayerAchievements($playerId, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $playerName
     * @return array|null
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function searchPlayerByName(string $playerName): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_player_search', $playerName);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->searchPlayers($playerName, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $portalId
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getPlayerDetailsByPortalId(string $portalId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_player_id', $portalId);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var PlayerClient $playerClient */
        $playerClient = $this->smiteClient->getHttpClient('player');
        $response = $playerClient->getPlayer($portalId, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $data = $data[0];
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getItems(): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_items');
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var $itemClient ItemClient */
        $itemClient = $this->smiteClient->getHttpClient('item');
        $response = $itemClient->getItems($this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 24 hours
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $name
     * @return array|null
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function getPlayerIdByName(string $name): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_player_name', $name);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->searchPlayers($name, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $data = $data[0];
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $playerId
     * @return array|null
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function getPlayerGodDetails(string $playerId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_player_god_details', $playerId);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->getGodRanks($playerId, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $playerId
     * @return array|null
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function getPlayerMatches(string $playerId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_player_matches', $playerId);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->getMatchHistory($playerId, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $id
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getTeamDetails(string $id): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_team_details', $id);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->getTeamDetails($id, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $data = $data[0];
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $term
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function searchTeams(string $term): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey('smite_team_search', $term);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->searchTeams($term, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $data = $data[0];
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    /**
     * @param string $teamId
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getTeamPlayers(string $teamId): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey("smite_team_players", $teamId);
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->getTeamPlayers($teamId, $this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }
    /**
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getTopMatches(): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        $cacheKey = $this->generateCacheKey("smite_top_matches");
        $cache = $this->cache->getItem($cacheKey);
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var MatchClient $matchClient */
        $matchClient = $this->smiteClient->getHttpClient('match');
        $response = $matchClient->getTopMatches($this->sessionId, $this->timestamp);

        $data = null;

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
        } else {
            $this->logger->error('API call failed.', [
                'item' => $cacheKey,
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
    }

    public function testHirezSession(): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        /** @var AuthenticationClient $authenticationClient */
        $authenticationClient = $this->smiteClient->getHttpClient('auth');
        $response = $authenticationClient->testSession($this->timestamp, $this->sessionId);

        return $response->getContent();
    }

    public function getUsage(): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        /** @var ToolClient $toolClient */
        $toolClient = $this->smiteClient->getHttpClient('tool');
        $response = $toolClient->getDataUsed($this->timestamp, $this->sessionId);

        return $response->getContent();
    }

    public function getHirezServerStatus(): ?array
    {
        if (is_null($this->sessionId)) {
            return null;
        }

        /** @var ToolClient $toolClient */
        $toolClient = $this->smiteClient->getHttpClient('tool');
        $response = $toolClient->getHirezServerStatus($this->timestamp, $this->sessionId);

        return $response->getContent();
    }

    protected function generateCacheKey(string $prepend, ?string $value = null): string
    {
        return $prepend . (isset($value) ? '_' . md5($value) : null);
    }

    protected function logApiCall(string $name, bool $cached, int $responseStatus = null): void
    {
        $apiCall = new ApiCall();
        $apiCall->setName($name);
        $apiCall->setResponseStatus($responseStatus);
        $apiCall->setCached($cached ? 1 : 0);
        $apiCall->setDateCreated(new \DateTime());

        $this->entityManager->persist($apiCall);
        $this->entityManager->flush();
    }
}
