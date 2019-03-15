<?php

namespace App\Http\Controllers;

use App\Repositories\LeagueRepository;
use App\Repositories\MatchRepository;

class HomeController extends Controller
{
    protected $leagueRepository;
    protected $matchRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LeagueRepository $leagueRepository, MatchRepository $matchRepository)
    {
        $this->leagueRepository = $leagueRepository;
        $this->matchRepository = $matchRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->matchRepository->createFixture();
        $fixture = $this->matchRepository->getFixture();
        $collection = collect($fixture);
        $grouped = $collection->groupBy('week_id');

        return view('home', [
            'league' => $this->leagueRepository->getAll(),
            'matches' => $grouped->toArray(),
            'fixture' => $grouped->toArray(),
            'weeks' => $this->matchRepository->getWeeks(),
            'strength' => $this->matchRepository->getAllStrength(),
            'types' => ['weak', 'average', 'strong']
        ]);
    }
}
