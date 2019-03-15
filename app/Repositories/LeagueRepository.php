<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 15/3/19
 * Time: 5:15 PM
 */

namespace App\Repositories;

use App\Models\League;
use App\Models\Team;

class LeagueRepository
{
    protected $league;
    protected $team;
    public $result = array();

    /**
     * LeagueRepository constructor.
     * @param League $league
     * @param Team $team
     */
    public function __construct(League $league, Team $team)
    {
        $this->league = $league;
        $this->team = $team;

    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->team->leftJoin('leagues', 'teams.id', '=', 'leagues.team_id')
            ->orderBy('leagues.points', 'DESC')
            ->get();
    }

    /**
     * Create league if no league exist
     */
    public function createLeague()
    {
        if (!$this->league->count()) {
            foreach ($this->team->pluck('id') as $value) {
                $data = [
                    'team_id' => $value,
                    'points' => 0,
                    'played' => 0,
                    'won' => 0,
                    'lost' => 0,
                    'drawn' => 0,
                    'goal_difference' => 0
                ];
                $this->league->create($data);
            }
        }
    }

    /**
     * Update league
     * @param array $data
     * @param $team_id
     * @return mixed
     */
    public function updateLeague($data = array(), $team_id)
    {
        return $this->league->where('team_id', '=', $team_id)->update($data);
    }

    /**
     * @param $team_id
     * @return mixed
     */
    public function getLeagueByTeamId($team_id)
    {
        return $this->league->where('team_id', $team_id)->first();
    }

    /**
     * Delete all league
     */
    public function deleteLeague()
    {
        $this->league->truncate();
    }


}