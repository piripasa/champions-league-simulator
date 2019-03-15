<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\LeagueRepository;

class LeagueController extends Controller
{
    protected $leagueRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LeagueRepository $leagueRepository)
    {
        $this->leagueRepository = $leagueRepository;
        $this->leagueRepository->createLeague();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->leagueRepository->getAll());
    }

}
