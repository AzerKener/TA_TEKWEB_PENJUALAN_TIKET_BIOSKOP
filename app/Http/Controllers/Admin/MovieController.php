<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::withCount('reviews')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $movies = $query->paginate(15)->withQueryString();

        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        return view('admin.movies.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateMovie($request);

        if ($request->hasFile('poster_image')) {
            $validated['poster_image'] = $request->file('poster_image')
                ->store('posters', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        // Handle genre from text input
        $genreInput = $request->input('genre_input', '');
        $validated['genre'] = $genreInput
            ? array_map('trim', explode(',', $genreInput))
            : [];

        Movie::create($validated);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Film berhasil ditambahkan!');
    }

    public function show(Movie $movie)
    {
        $movie->load(['schedules.studio', 'reviews.user']);
        return view('admin.movies.show', compact('movie'));
    }

    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $this->validateMovie($request, $movie->id);

        if ($request->hasFile('poster_image')) {
            // Hapus poster lama
            if ($movie->poster_image) {
                Storage::disk('public')->delete($movie->poster_image);
            }
            $validated['poster_image'] = $request->file('poster_image')
                ->store('posters', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        // Handle genre from text input
        $genreInput = $request->input('genre_input', '');
        $validated['genre'] = $genreInput
            ? array_map('trim', explode(',', $genreInput))
            : $movie->genre ?? [];

        $movie->update($validated);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Film berhasil diperbarui!');
    }

    public function destroy(Movie $movie)
    {
        if ($movie->poster_image) {
            Storage::disk('public')->delete($movie->poster_image);
        }

        $movie->delete();

        return redirect()->route('admin.movies.index')
            ->with('success', 'Film berhasil dihapus.');
    }

    private function validateMovie(Request $request, ?int $movieId = null): array
    {
        return $request->validate([
            'title'              => ['required', 'string', 'max:200'],
            'synopsis'           => ['required', 'string'],
            'duration'           => ['required', 'integer', 'min:1', 'max:600'],
            'rating'             => ['required', 'in:SU,G,PG,PG-13,R,D17'],
            'director'           => ['required', 'string', 'max:200'],
            'cast'               => ['nullable', 'string'],
            'poster_image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'trailer_url'        => ['nullable', 'url'],
            'language'           => ['required', 'in:Indonesia,Inggris,Korea,Jepang,Mandarin'],
            'has_subtitle'       => ['boolean'],
            'status'             => ['required', 'in:now_playing,coming_soon,ended'],
            'release_date'       => ['required', 'date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:release_date'],
            'imdb_rating'        => ['nullable', 'numeric', 'min:0', 'max:10'],
            'production_company' => ['nullable', 'string', 'max:200'],
            'distributor'        => ['nullable', 'string', 'max:200'],
        ]);
    }
}
