<?php
/**
 * Created by PhpStorm.
 * User: piripasa
 * Date: 15/3/19
 * Time: 5:17 PM
 */

namespace App\Repositories;

use App\Models\Team;
use App\Models\Match;
use App\Models\Strength;
use App\Models\Week;

class MatchRepository
{
    protected $team;
    protected $match;
    protected $week;
    protected $teamStrength;
    public $result;

    /**
     * MatchRepository constructor.
     * @param Team $team
     * @param Match $match
     * @param Week $week
     * @param Strength $teamStrength
     */
    public function __construct(Team $team, Match $match, Week $week, Strength $teamStrength)
    {
        $this->team = $team;
        $this->match = $match;
        $this->week = $week;
        $this->teamStrength = $teamStrength;
    }

    /**
     * @return mixed
     */
    public function getWeeks()
    {
        return $this->week->get();
    }

    /**
     * Create Fixture
     */
    public function createFixture()
    {
        foreach ($this->week->pluck('id') as $week) {
            foreach ($this->iterateTeams($this->team->pluck('id')) as $value) {
                if (0 == $this->checkMatch($week, $value)) {
                    $this->match->create(['home' => $value[0], 'away' => $value[1], 'week_id' => $week]);
                }
            }
        }
    }

    /**
     * @param $week_id
     * @param $teams
     * @return mixed
     */
    public function checkMatch($week_id, $teams)
    {
        return $this->match->where('week_id', '=', $week_id)
            ->whereRaw('(home IN(' . implode(',', $teams) . ') OR away IN(' . implode(',', $teams) . '))')
            ->count();
    }


    /**
     * @return mixed
     */
    public function getFixture()
    {
        return $this->match->select(
            'matches.id',
            'matches.played',
            'matches.week_id',
            'matches.home_goal',
            'matches.away_goal',
            'week_id',
            'home.name as home_team',
            'away.name as away_team')
            ->join('weeks', 'weeks.id', '=', 'matches.week_id')
            ->join('teams as home', 'home.id', '=', 'matches.home')
            ->join('teams as away', 'away.id', '=', 'matches.away')
            ->orderBy('week_id', 'ASC')
            ->get();
    }

    /**
     * @param $week_id
     * @return mixed
     */
    public function getFixtureByWeekId($week_id)
    {
        return $this->match->select(
            'matches.id',
            'matches.played',
            'matches.week_id',
            'matches.home_goal',
            'matches.away_goal',
            'week_id',
            'weeks.name',
            'home.name as home_team',
            'away.name as away_team')
            ->join('weeks', 'weeks.id', '=', 'matches.week_id')
            ->join('teams as home', 'home.id', '=', 'matches.home')
            ->join('teams as away', 'away.id', '=', 'matches.away')
            ->where('matches.week_id', '=', $week_id)
            ->orderBy('matches.id', 'ASC')
            ->get();
    }

    /**
     * @param $team_id
     * @param $is_home
     * @return mixed
     */
    public function getTeamStrength($team_id, $is_home)
    {
        return $this->teamStrength->where([['team_id', '=', $team_id], ['is_home', '=', $is_home]])->get();
    }

    /**
     * @param $team_id
     * @param $is_home
     * @return int
     */
    public function createStrength($team_id, $is_home)
    {

        foreach ($this->getTeamStrength($team_id, $is_home) as $value) {
            switch ($value->strength) {
                case 'strong':
                    $this->result = rand(4, 5);
                    break;
                case 'average':
                    $this->result = rand(2, 3);
                    break;
                case 'weak' :
                    $this->result = rand(0, 2);
                    break;
            }

            return $this->result;
        }
    }

    /**
     * @param $week
     * @return mixed
     */
    public function getMatchesFromWeek($week)
    {
        return $this->match->where([['week_id', '=', $week], ['played', '=', 0]])->get();
    }

    /**
     * @param int $played
     * @return mixed
     */
    public function getAllMatches($played = 0)
    {
        return $this->match->where('played', '=', $played)->get();
    }

    /**
     * @param $homeScore
     * @param $awayScore
     * @param $home
     * @param $away
     */
    public function calculateScore($homeScore, $awayScore, $home, $away)
    {
        if ($homeScore > $awayScore) {
            $home->won += 1;
            $home->points += 3;
            $home->goal_difference += ($homeScore - $awayScore);
            $away->lost += 1;
            $away->goal_difference += ($awayScore - $homeScore);
        } elseif ($awayScore > $homeScore) {
            $away->won += 1;
            $away->points += 3;
            $away->goal_difference += ($awayScore - $homeScore);
            $home->lost += 1;
            $home->goal_difference += ($homeScore - $awayScore);
        } else {
            $home->drawn += 1;
            $away->drawn += 1;
            $home->points += 1;
            $away->points += 1;
        }

        $home->played += 1;
        $away->played += 1;
        $home->save();
        $away->save();
    }


    /**
     *
     */
    public function deleteMatches()
    {
        $this->match->truncate();
    }

    public function getAllStrength()
    {
        return $this->teamStrength->select('strengths.id', 'teams.name', 'strengths.is_home', 'strengths.strength')
            ->join('teams', 'teams.id', '=', 'strengths.team_id')
            ->orderBy('teams.id')
            ->get();
    }

    /**
     * @param $id
     * @param $column
     * @param $value
     * @return mixed
     */
    public function updateMatch($id, $column, $value)
    {
        $match = $this->match->find($id);
        $match->$column = $value;
        $match->played = 1;
        $match->save();
        return $match;
    }

    /**
     * @param $teams
     * @return array
     */
    private function iterateTeams($teams)
    {
        $collection = collect($teams);
        $matrix = $collection->crossJoin($teams);
        $data = $matrix->reject(function ($items) {
            if ($items[0] == $items[1]) {
                return $items;
            }
        })->shuffle();
        return $data->all();
    }
}