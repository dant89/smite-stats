<?php

namespace App\Mapper;

use App\Entity\Player;
use App\Entity\PlayerAchievement;
use Psr\Log\LoggerInterface;

class PlayerAchievementMapper
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function from(Player $player, array $data): PlayerAchievement
    {
        try {
            $playerAchievement = new PlayerAchievement();
            $playerAchievement->setSmitePlayer($player);
            $playerAchievement->setAssistedKills($data['AssistedKills']);
            $playerAchievement->setCampsCleared($data['CampsCleared']);
            $playerAchievement->setDeaths($data['Deaths']);
            $playerAchievement->setDivineSpree($data['DivineSpree']);
            $playerAchievement->setDoubleKills($data['DoubleKills']);
            $playerAchievement->setFireGiantKills($data['FireGiantKills']);
            $playerAchievement->setFirstBloods($data['FirstBloods']);
            $playerAchievement->setGodLikeSpree($data['GodLikeSpree']);
            $playerAchievement->setGoldFuryKills($data['GoldFuryKills']);
            $playerAchievement->setImmortalSpree($data['ImmortalSpree']);
            $playerAchievement->setKillingSpree($data['KillingSpree']);
            $playerAchievement->setMinionKills($data['MinionKills']);
            $playerAchievement->setPentaKills($data['PentaKills']);
            $playerAchievement->setPhoenixKills($data['PhoenixKills']);
            $playerAchievement->setPlayerKills($data['PlayerKills']);
            $playerAchievement->setQuadraKills($data['QuadraKills']);
            $playerAchievement->setRampageSpree($data['RampageSpree']);
            $playerAchievement->setShutdownSpree($data['ShutdownSpree']);
            $playerAchievement->setSiegeJuggernautKills($data['SiegeJuggernautKills']);
            $playerAchievement->setTowerKills($data['TowerKills']);
            $playerAchievement->setTripleKills($data['TripleKills']);
            $playerAchievement->setUnstoppableSpree($data['UnstoppableSpree']);
            $playerAchievement->setWildJuggernautKills($data['WildJuggernautKills']);
            $playerAchievement->setDateUpdated(new \DateTime());

        } catch (\Exception $e) {
            $this->logger->error('Could not map data to player achievement.', [
                'exception' => $e,
                'player_achievement' => $data
            ]);
            throw new \RuntimeException('Could not map data to player achievement.');
        }

        return $playerAchievement;
    }

    public function fromExisting(PlayerAchievement $playerAchievement, array $data): PlayerAchievement
    {
        try {
            $playerAchievement->setAssistedKills($data['AssistedKills']);
            $playerAchievement->setCampsCleared($data['CampsCleared']);
            $playerAchievement->setDeaths($data['Deaths']);
            $playerAchievement->setDivineSpree($data['DivineSpree']);
            $playerAchievement->setDoubleKills($data['DoubleKills']);
            $playerAchievement->setFireGiantKills($data['FireGiantKills']);
            $playerAchievement->setFirstBloods($data['FirstBloods']);
            $playerAchievement->setGodLikeSpree($data['GodLikeSpree']);
            $playerAchievement->setGoldFuryKills($data['GoldFuryKills']);
            $playerAchievement->setImmortalSpree($data['ImmortalSpree']);
            $playerAchievement->setKillingSpree($data['KillingSpree']);
            $playerAchievement->setMinionKills($data['MinionKills']);
            $playerAchievement->setPentaKills($data['PentaKills']);
            $playerAchievement->setPhoenixKills($data['PhoenixKills']);
            $playerAchievement->setPlayerKills($data['PlayerKills']);
            $playerAchievement->setQuadraKills($data['QuadraKills']);
            $playerAchievement->setRampageSpree($data['RampageSpree']);
            $playerAchievement->setShutdownSpree($data['ShutdownSpree']);
            $playerAchievement->setSiegeJuggernautKills($data['SiegeJuggernautKills']);
            $playerAchievement->setTowerKills($data['TowerKills']);
            $playerAchievement->setTripleKills($data['TripleKills']);
            $playerAchievement->setUnstoppableSpree($data['UnstoppableSpree']);
            $playerAchievement->setWildJuggernautKills($data['WildJuggernautKills']);
            $playerAchievement->setDateUpdated(new \DateTime());
        } catch (\Exception $e) {
            $this->logger->error('Could not map data to player achievement.', [
                'exception' => $e,
                'player_achievement' => $data
            ]);
            throw new \RuntimeException('Could not map data to player achievement.');
        }

        return $playerAchievement;
    }
}
