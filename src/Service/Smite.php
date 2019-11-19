<?php

namespace App\Service;

use Dant89\SmiteApiClient\Client;
use Dant89\SmiteApiClient\God\GodClient;
use Dant89\SmiteApiClient\League\LeagueClient;
use Dant89\SmiteApiClient\Match\MatchClient;
use Dant89\SmiteApiClient\Player\PlayerClient;
use Dant89\SmiteApiClient\Player\PlayerInfoClient;
use Dant89\SmiteApiClient\Team\TeamClient;
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
     * @param LoggerInterface $logger
     * @throws InvalidArgumentException
     */
    public function __construct(Client $client, AdapterInterface $cache, LoggerInterface $logger)
    {
        $this->smiteClient = $client;
        $this->cache = $cache;
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

        $this->logger->critical('Failed to authenticate.', [
            'response' => $response
        ]);

        throw new \RuntimeException('Could not authenticate.');
    }

    /**
     * @return array|null
     * @throws InvalidArgumentException
     */
    public function getGods(): ?array
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
        } else {
            $this->logger->error('API call failed.', [
                'item' => 'smite_gods',
                'response' => $response
            ]);
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
            return $cache->get();
        }

        /** @var LeagueClient $godClient */
        $leagueClient = $this->smiteClient->getHttpClient('league');
        $response = $leagueClient->getLeagueLeaderboard($queue, $tier, $round, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data;
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_leaderboards_{$queue}_{$tier}_{$round}",
                'response' => $response
            ]);
        }
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
            return $cache->get();
        }

        /** @var MatchClient $teamClient */
        $matchClient = $this->smiteClient->getHttpClient('match');
        $response = $matchClient->getMatchDetailsBatch($matchIds, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data;
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_team_match_details_{$uniqueKey}",
                'response' => $response
            ]);
        }
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
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->getPlayerAchievements($playerId, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data;
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_player_achievements_{$playerId}",
                'response' => $response
            ]);
        }
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
            return $cache->get();
        }

        /** @var PlayerClient $playerClient */
        $playerClient = $this->smiteClient->getHttpClient('player');
        $response = $playerClient->getPlayer($portalId, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $cache->set($data[0]);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data[0];
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_player_{$portalId}",
                'response' => $response
            ]);
        }
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
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->searchPlayers($name, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $cache->set($data[0]);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data[0];
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_player_{$name}",
                'response' => $response
            ]);
        }
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
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_player_gods_{$playerId}",
                'response' => $response
            ]);
        }
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
            return $cache->get();
        }

        /** @var PlayerInfoClient $playerInfoClient */
        $playerInfoClient = $this->smiteClient->getHttpClient('player_info');
        $response = $playerInfoClient->getMatchHistory($playerId, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            $cache->set($data);
            $cache->expiresAfter(3600 / 2); // 30 minutes
            $this->cache->save($cache);
            return $data;
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_player_matches_{$playerId}",
                'response' => $response
            ]);
        }
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
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->getTeamDetails($id, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $cache->set($data[0]);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
            return $data[0];
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_team_{$id}",
                'response' => $response
            ]);
        }
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
            return $cache->get();
        }

        /** @var TeamClient $teamClient */
        $teamClient = $this->smiteClient->getHttpClient('team');
        $response = $teamClient->searchTeams($term, $this->sessionId, $this->timestamp);

        if ($response->getStatus() === 200) {
            $data = $response->getContent();
            if (empty($data)) {
                return null;
            }
            $cache->set($data[0]);
            $cache->expiresAfter(3600 * 24); // 1 day
            $this->cache->save($cache);
            return $data[0];
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_team_search_{$term}",
                'response' => $response
            ]);
        }
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
        } else {
            $this->logger->error('API call failed.', [
                'item' => "smite_team_players_{$teamId}",
                'response' => $response
            ]);
        }
    }
}
