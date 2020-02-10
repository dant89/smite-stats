<?php

namespace App\Entity;

class MappedMatch
{
    /**
     * @var int
     */
    protected $smiteMatchId;

    /**
     * @var MatchPlayer[]
     */
    protected $matchPlayers = [];

    /**
     * @var string
     */
    protected $matchType;

    /**
     * @var int
     */
    protected $minutes;

    /**
     * @var int
     */
    protected $winningTaskforce;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @return int
     */
    public function getSmiteMatchId(): int
    {
        return $this->smiteMatchId;
    }

    /**
     * @param int $smiteMatchId
     * @return MappedMatch
     */
    public function setSmiteMatchId(int $smiteMatchId): MappedMatch
    {
        $this->smiteMatchId = $smiteMatchId;
        return $this;
    }

    /**
     * @return MatchPlayer[]
     */
    public function getMatchPlayers(): array
    {
        return $this->matchPlayers;
    }

    /**
     * @param MatchPlayer[] $matchPlayers
     * @return MappedMatch
     */
    public function setMatchPlayers(array $matchPlayers): MappedMatch
    {
        $this->matchPlayers = $matchPlayers;
        return $this;
    }

    /**
     * @return string
     */
    public function getMatchType(): string
    {
        return $this->matchType;
    }

    /**
     * @param string $matchType
     * @return MappedMatch
     */
    public function setMatchType(string $matchType): MappedMatch
    {
        $this->matchType = $matchType;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinutes(): int
    {
        return $this->minutes;
    }

    /**
     * @param int $minutes
     * @return MappedMatch
     */
    public function setMinutes(int $minutes): MappedMatch
    {
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @return int
     */
    public function getWinningTaskforce(): int
    {
        return $this->winningTaskforce;
    }

    /**
     * @param int $winningTaskforce
     * @return MappedMatch
     */
    public function setWinningTaskforce(int $winningTaskforce): MappedMatch
    {
        $this->winningTaskforce = $winningTaskforce;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return MappedMatch
     */
    public function setDate(\DateTime $date): MappedMatch
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return MatchPlayer[][]
     */
    public function getPlayersByTeamIndex(): array
    {
        $teamPlayers = [];
        foreach ($this->matchPlayers as $matchPlayer) {
            $teamPlayers[$matchPlayer->getTaskForce()][] = $matchPlayer;
        }
        return $teamPlayers;
    }
}
