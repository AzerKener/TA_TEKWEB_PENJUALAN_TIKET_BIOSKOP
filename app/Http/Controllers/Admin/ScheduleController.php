<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Studio;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['movie', 'studio'])
            ->orderBy('show_date', 'desc')
            ->orderBy('start_time');

        if ($request->filled('movie_id')) {
            $query->where('movie_id', $request->movie_id);
        }
        if ($request->filled('studio_id')) {
            $query->where('studio_id', $request->studio_id);
        }
        if ($request->filled('date')) {
            $query->where('show_date', $request->date);
        }

        $schedules = $query->paginate(20)->withQueryString();
        $movies    = Movie::active()->orderBy('title')->get();
        $studios   = Studio::where('is_active', true)->orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'movies', 'studios'));
    }

    public function create()
    {
        $movies  = Movie::active()->orderBy('title')->get();
        $studios = Studio::where('is_active', true)->orderBy('name')->get();

        return view('admin.schedules.create', compact('movies', 'studios'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSchedule($request);

        // Cek konflik jadwal
        $conflict = $this->detectConflict($validated);
        if ($conflict) {
            return back()->withInput()
                ->with('error', "Konflik jadwal! Studio sudah digunakan untuk film '{$conflict->movie->title}' pada pukul {$conflict->start_time} - {$conflict->end_time}.");
        }

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal tayang berhasil ditambahkan!');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['movie', 'studio', 'tickets.seat', 'transactions.user']);
        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $movies  = Movie::active()->orderBy('title')->get();
        $studios = Studio::where('is_active', true)->orderBy('name')->get();

        return view('admin.schedules.edit', compact('schedule', 'movies', 'studios'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $this->validateSchedule($request);

        // Cek konflik (kecuali jadwal ini sendiri)
        $conflict = $this->detectConflict($validated, $schedule->id);
        if ($conflict) {
            return back()->withInput()
                ->with('error', "Konflik jadwal! Studio sudah digunakan untuk film '{$conflict->movie->title}' pada pukul {$conflict->start_time} - {$conflict->end_time}.");
        }

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal tayang berhasil diperbarui!');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->tickets()->where('status', 'active')->exists()) {
            return back()->with('error', 'Jadwal tidak dapat dihapus karena masih ada tiket aktif.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal tayang berhasil dihapus.');
    }

    /**
     * AJAX: Cek konflik jadwal real-time
     */
    public function checkConflict(Request $request)
    {
        $conflict = $this->detectConflict($request->all(), $request->input('schedule_id'));

        if ($conflict) {
            return response()->json([
                'conflict' => true,
                'message'  => "Studio sudah digunakan: {$conflict->movie->title} ({$conflict->start_time} - {$conflict->end_time})",
            ]);
        }

        return response()->json(['conflict' => false]);
    }

    private function validateSchedule(Request $request): array
    {
        return $request->validate([
            'movie_id'      => ['required', 'exists:movies,id'],
            'studio_id'     => ['required', 'exists:studios,id'],
            'show_date'     => ['required', 'date'],
            'start_time'    => ['required', 'date_format:H:i'],
            'end_time'      => ['required', 'date_format:H:i', 'after:start_time'],
            'price_regular' => ['required', 'numeric', 'min:0'],
            'price_vip'     => ['required', 'numeric', 'min:0'],
            'price_couple'  => ['required', 'numeric', 'min:0'],
            'language_type' => ['required', 'in:dubbed,subtitled,original'],
            'is_active'     => ['boolean'],
        ]);
    }

    private function detectConflict(array $data, ?int $excludeId = null): ?Schedule
    {
        return Schedule::where('studio_id', $data['studio_id'])
            ->where('show_date', $data['show_date'])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                  ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('start_time', '<=', $data['start_time'])
                         ->where('end_time', '>=', $data['end_time']);
                  });
            })
            ->with('movie')
            ->first();
    }
}
