<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::with(['reviews'])
            ->whereIn('status', ['now_playing', 'coming_soon']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('genre')) {
            $query->whereJsonContains('genre', $request->genre);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $movies = $query->latest('release_date')->paginate(12)->withQueryString();

        $allGenres = Movie::active()->get()->pluck('genre')->flatten()->unique()->sort()->values();

        return view('customer.movies.index', compact('movies', 'allGenres'));
    }

    public function show(string $slug)
    {
        $movie = Movie::where('slug', $slug)
            ->with([
                'reviews' => fn($q) => $q->approved()->with('user')->latest()->take(10),
                'schedules' => fn($q) => $q->upcoming()->with('studio')->orderBy('show_date')->orderBy('start_time'),
            ])
            ->firstOrFail();

        $relatedMovies = Movie::active()
            ->where('id', '!=', $movie->id)
            ->where(function ($q) use ($movie) {
                if (is_array($movie->genre) && count($movie->genre) > 0) {
                    $q->whereJsonContains('genre', $movie->genre[0]);
                }
            })
            ->take(4)
            ->get();

        $userHasReviewed = auth()->check()
            ? Review::where('user_id', auth()->id())->where('movie_id', $movie->id)->exists()
            : false;

        // Group schedules by date
        $schedulesByDate = $movie->schedules->groupBy(fn($s) => $s->show_date->format('Y-m-d'));

        return view('customer.movies.show', compact('movie', 'relatedMovies', 'userHasReviewed', 'schedulesByDate'));
    }

    public function storeReview(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if user already reviewed
        $existing = Review::where('user_id', auth()->id())->where('movie_id', $movie->id)->first();

        if ($existing) {
            $existing->update($validated);
            return back()->with('success', 'Ulasan Anda berhasil diperbarui.');
        }

        Review::create([
            ...$validated,
            'user_id'  => auth()->id(),
            'movie_id' => $movie->id,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda telah dikirim.');
    }
}
