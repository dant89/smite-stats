<?php

namespace App\Mapper;

use App\Entity\Player;
use Psr\Log\LoggerInterface;

class PlayerMapper
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function from(array $data): Player
    {
        try {
            $player = new Player();
            $player->setSmitePlayerId($data['ActivePlayerId']);
            $player->setAvatarUrl($data['Avatar_URL']);
            $player->setDateRegistered(new \DateTime($data['Created_Datetime']));
            $player->setDateLastLogin(new \DateTime($data['Last_Login_Datetime']));
            $player->setHoursPlayed($data['HoursPlayed'] ?? 0);
            $player->setLeaves($data['Leaves'] ?? 0);
            $player->setLevel($data['Level'] ?? 0);
            $player->setLosses($data['Losses'] ?? 0);
            $player->setMasteryLevel($data['MasteryLevel'] ?? 0);
            $player->setName($data['Name']);
            $player->setPersonalStatusMessage($data['Personal_Status_Message']);
            $player->setRankStatConquest($data['Rank_Stat_Conquest'] ?? 0);
            $player->setRankStatDuel($data['Rank_Stat_Duel'] ?? 0);
            $player->setRankStatJoust($data['Rank_Stat_Joust'] ?? 0);
            $player->setRegion($data['Region']);
            $player->setTeamId($data['TeamId']);
            $player->setTeamName($data['Team_Name']);
            $player->setTierConquest($data['Tier_Conquest'] ?? 0);
            $player->setTierDuel($data['Tier_Duel'] ?? 0);
            $player->setTierJoust($data['Tier_Joust'] ?? 0);
            $player->setTotalAchievements($data['Total_Achievements'] ?? 0);
            $player->setTotalWorshippers($data['Total_Worshippers'] ?? 0);
            $player->setDateCreated(new \DateTime());
            $player->setDateUpdated(new \DateTime());
            $player->setWins($data['Wins'] ?? 0);
            $player->setCrawled(1);
        } catch (\Exception $e) {
            $this->logger->error('Could not map data to player.', [
                'exception' => $e,
                'player' => $data
            ]);
            throw new \RuntimeException('Could not map data to player.');
        }

        return $player;
    }
    public function fromExisting(Player $player, array $data): Player
    {
        try {
            $player->setAvatarUrl($data['Avatar_URL']);
            $player->setDateRegistered(new \DateTime($data['Created_Datetime']));
            $player->setDateLastLogin(new \DateTime($data['Last_Login_Datetime']));
            $player->setHoursPlayed($data['HoursPlayed'] ?? 0);
            $player->setLeaves($data['Leaves'] ?? 0);
            $player->setLevel($data['Level'] ?? 0);
            $player->setLosses($data['Losses'] ?? 0);
            $player->setMasteryLevel($data['MasteryLevel'] ?? 0);
            $player->setName($data['Name']);
            $player->setPersonalStatusMessage($data['Personal_Status_Message']);
            $player->setRankStatConquest($data['Rank_Stat_Conquest'] ?? 0);
            $player->setRankStatDuel($data['Rank_Stat_Duel'] ?? 0);
            $player->setRankStatJoust($data['Rank_Stat_Joust'] ?? 0);
            $player->setRegion($data['Region']);
            $player->setTeamId($data['TeamId']);
            $player->setTeamName($data['Team_Name']);
            $player->setTierConquest($data['Tier_Conquest'] ?? 0);
            $player->setTierDuel($data['Tier_Duel'] ?? 0);
            $player->setTierJoust($data['Tier_Joust'] ?? 0);
            $player->setTotalAchievements($data['Total_Achievements'] ?? 0);
            $player->setTotalWorshippers($data['Total_Worshippers'] ?? 0);
            $player->setDateUpdated(new \DateTime());
            $player->setWins($data['Wins'] ?? 0);
            $player->setCrawled(1);
        } catch (\Exception $e) {
            $this->logger->error('Could not map data to player.', [
                'exception' => $e,
                'player' => $data
            ]);
            throw new \RuntimeException('Could not map data to player.');
        }

        return $player;
    }


}
