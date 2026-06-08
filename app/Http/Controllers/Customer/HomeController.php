<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Movie;

class HomeController extends Controller
{
    public function index()
    {
        $nowPlaying = Movie::nowPlaying()
            ->with(['reviews', 'schedules' => fn($q) => $q->upcoming()])
            ->latest('release_date')
            ->take(8)
            ->get();

        $comingSoon = Movie::comingSoon()
            ->latest('release_date')
            ->take(6)
            ->get();

        $featuredMovie = $nowPlaying->first();

        return view('customer.home.index', compact('nowPlaying', 'comingSoon', 'featuredMovie'));
    }
}
