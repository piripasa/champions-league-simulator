<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\LeagueRepository;
use App\Repositories\MatchRepository;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public $league;
    public $teams;
    public $weeks;
    public $fixture;
    protected $leagueRepository;
    protected $matchRepository;
    public $result = [];
    public $predictions = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LeagueRepository $leagueRepository, MatchRepository $matchRepository)
    {
        $this->leagueRepository = $leagueRepository;
        $this->matchRepository = $matchRepository;
        $this->leagueRepository->createLeague();
    }

    /**
     * Get Fixtures
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $weeks = $this->matchRepository->getWeeks();
        $fixture = $this->matchRepository->getFixture();
        $collection = collect($fixture);
        $grouped = $collection->groupBy('week_id');
        return response()->json(['weeks' => $weeks, 'items' => $grouped->toArray()]);
    }

    /**
     * Play all match
     * @return \Illuminate\Http\JsonResponse
     */
    public function play()
    {
        try {
            $matches = $this->matchRepository->getAllMatches();
            $this->playGame($matches);
            return response()->json(['message' => 'Success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error'], 400);
        }
    }

    /**
     * Play weekly match
     * @param $week
     * @return \Illuminate\Http\JsonResponse
     */
    public function playWeekly($week)
    {
        try {
            $matches = $this->matchRepository->getMatchesFromWeek($week);

            $this->playGame($matches);
            $result = $this->matchRepository->getFixtureByWeekId($week);

            return response()->json(['matches' => $result]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error'], 400);
        }
    }

    /**
     * Reset Fixture
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetFixture()
    {
        try {
            $this->matchRepository->deleteMatches();
            $this->leagueRepository->deleteLeague();
            $this->matchRepository->createFixture();
            return response()->json(['message' => 'Success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error'], 400);
        }
    }

    /**
     * Show Next match
     * @param $week
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($week)
    {
        $matches = $this->matchRepository->getFixtureByWeekId($week);
        return response()->json(['matches' => $matches]);

    }

    /**
     * Get Predictions
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPredictions()
    {
        try {
            $finished = $this->leagueRepository->getAll();

            $this->collectionPredictions($finished);
            $matches = $this->matchRepository->getAllMatches();
            $this->combinePredictions($matches);
            $collection = collect($this->predictions);
            $multiplied = $collection->map(function ($item) {
                return round((($item['points'] / $this->sumPoints()) * 100), 2);
            });

            $combine = $multiplied->all();

            //reset keys after combine
            $values = $collection->values();

            $items = [];
            foreach ($values->all() as $key => $val) {
                array_push($items, [$val['name'], $combine[$val['team_id']]]);
            }

            return response()->json(['items' => $items]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error'], 400);
        }
    }

    /**
     * Update specific match
     * @param $id
     * @param $column
     * @param $value
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, $column, $value)
    {
        try {
            $this->matchRepository->updateMatch($id, $column, $value);
            $this->leagueRepository->deleteLeague();
            $this->leagueRepository->createLeague();
            $matches = $this->matchRepository->getAllMatches(1);

            foreach ($matches as $match) {
                $home = $this->leagueRepository->getLeagueByTeamId($match->home);
                $away = $this->leagueRepository->getLeagueByTeamId($match->away);

                $this->matchRepository->calculateScore($match->home_goal, $match->away_goal, $home, $away);
            }
            return response()->json(['message' => 'Success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error'], 400);
        }
    }

    /**
     * @param $matches
     */
    private function playGame($matches)
    {
        foreach ($matches as $match) {
            $homeScore = $this->matchRepository->createStrength($match->home, 1);
            $awayScore = $this->matchRepository->createStrength($match->away, 0);
            $home = $this->leagueRepository->getLeagueByTeamId($match->home);
            $away = $this->leagueRepository->getLeagueByTeamId($match->away);
            $this->matchRepository->calculateScore($homeScore, $awayScore, $home, $away);
            $match->home_goal = $homeScore;
            $match->away_goal = $awayScore;
            $match->played = 1;
            $match->save();
        }
    }

    /**
     * @param $matches
     */
    private function combinePredictions($matches)
    {
        foreach ($matches as $match) {
            $homeScore = $this->matchRepository->createStrength($match->home, 1);
            $awayScore = $this->matchRepository->createStrength($match->away, 0);

            $points = $this->calculatePredictScore($homeScore, $awayScore);

            if (isset($points['away'])) {
                foreach ($points['away'] as $key => $value) {
                    $this->predictions[$match->away][$key] += $points['away'][$key];
                }
            }
            if (isset($points['home'])) {
                foreach ($points['home'] as $key => $value) {
                    $this->predictions[$match->home][$key] += $points['home'][$key];
                }
            }
        }
    }

    /**
     * @param $data
     */
    private function collectionPredictions($data)
    {
        $collection = collect($data);
        $collection->each(function ($item) {
            $this->predictions[$item->team_id]['points'] = $item->points;
            $this->predictions[$item->team_id]['name'] = $item->name;
            $this->predictions[$item->team_id]['team_id'] = $item->team_id;
        });
    }

    /**
     * @param $homeScore
     * @param $awayScore
     * @return array
     */
    private function calculatePredictScore($homeScore, $awayScore)
    {
        $points = [];
        if ($homeScore > $awayScore) {
            $points['home']['points'] = 3;
        } elseif ($awayScore > $homeScore) {
            $points['away']['points'] = 3;
        } else {
            $points['home']['points'] = 1;
            $points['away']['points'] = 1;
        }
        return $points;
    }

    /**
     * @return float|int
     */
    private function sumPoints()
    {
        return array_sum(array_map(function ($item) {
            return $item['points'];
        }, $this->predictions));
    }
}
