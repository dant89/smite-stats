<?php

namespace App\Service;

use App\Entity\ApiCall;
use Dant89\SmiteApiClient\Client;
use Dant89\SmiteApiClient\God\GodClient;
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

class Smite
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
    public function __construct(Client $client, AdapterInterface $cache, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->smiteClient = $client;
        $this->cache = $cache;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
        $cache = $this->cache->getItem('smite_session_id');
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        $authClient = $this->smiteClient->getHttpClient('auth');
        $response = $authClient->createSession($this->timestamp);

        $this->logApiCall($cache->getKey(), false, $response->getStatus());

        if ($response->getStatus() === 200) {
            $sessionId = $response->getContent()['session_id'];
            $cache->set($sessionId);
            $cache->expiresAfter(600); // 12 minutes
            $this->cache->save($cache);
            return $sessionId;
        }

        $this->logger->critical('Failed to authenticate.', [
            'response' => $response
        ]);

        throw new \RuntimeException('Could not authenticate.');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function ping()
    {
        $cache = $this->cache->getItem("smite_gods");
        if ($cache->isHit()) {
            $this->logApiCall($cache->getKey(), true);
            return $cache->get();
        }

        /** @var ToolClient $toolClient */
        $toolClient = $this->smiteClient->getHttpClient('tool');
        $response = $toolClient->ping();

        return $response;
    }

    /**
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getGods(): ?array
    {
        $cache = $this->cache->getItem("smite_gods");
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
                'item' => 'smite_gods',
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
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
     * @param string $queue
     * @param string $tier
     * @param string $round
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getLeagueLeaderboard(string $queue, string $tier, string $round): ?array
    {
        $cache = $this->cache->getItem("smite_leaderboards_{$queue}_{$tier}_{$round}");
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
                'item' => "smite_leaderboards_{$queue}_{$tier}_{$round}",
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
        $uniqueKey = md5(implode('_', $matchIds));

        $cache = $this->cache->getItem("smite_team_match_details_{$uniqueKey}");
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
                'item' => "smite_team_match_details_{$uniqueKey}",
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
        $cache = $this->cache->getItem("smite_player_achievements_{$playerId}");
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
                'item' => "smite_player_achievements_{$playerId}",
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
        $cache = $this->cache->getItem("smite_player_{$portalId}");
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
                'item' => "smite_player_{$portalId}",
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
        $cache = $this->cache->getItem("smite_player_{$name}");
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
                'item' => "smite_player_{$name}",
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
        $cache = $this->cache->getItem("smite_player_gods_{$playerId}");
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
                'item' => "smite_player_gods_{$playerId}",
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
        $cache = $this->cache->getItem("smite_player_matches_{$playerId}");
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
                'item' => "smite_player_matches_{$playerId}",
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
        $cache = $this->cache->getItem("smite_team_{$id}");
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
                'item' => "smite_team_{$id}",
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
        $cache = $this->cache->getItem("smite_team_search_{$term}");
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
                'item' => "smite_team_search_{$term}",
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
        $cache = $this->cache->getItem("smite_team_players_{$teamId}");
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
                'item' => "smite_team_players_{$teamId}",
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
        $cache = $this->cache->getItem("smite_top_matches");
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
                'item' => "smite_top_matches",
                'response' => $response
            ]);
        }

        $this->logApiCall($cache->getKey(), false, $response->getStatus());
        return $data;
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
