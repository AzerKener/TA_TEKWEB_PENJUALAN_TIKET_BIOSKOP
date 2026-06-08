<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Studio;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudioController extends Controller
{
    public function index()
    {
        $studios = Studio::withCount('seats')->orderBy('name')->get();
        return view('admin.studios.index', compact('studios'));
    }

    public function create()
    {
        return view('admin.studios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'type'            => ['required', 'in:regular,imax,4dx,vip,premiere'],
            'total_rows'      => ['required', 'integer', 'min:1', 'max:30'],
            'seats_per_row'   => ['required', 'integer', 'min:1', 'max:30'],
            'vip_rows'        => ['nullable', 'string'],
            'couple_rows'     => ['nullable', 'string'],
            'description'     => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $studio = Studio::create([
                'name'            => $validated['name'],
                'type'            => $validated['type'],
                'total_rows'      => $validated['total_rows'],
                'columns_layout'  => "1-{$validated['seats_per_row']}",
                'description'     => $validated['description'] ?? null,
                'is_active'       => true,
            ]);

            // Auto-generate kursi
            $this->generateSeats($studio, $validated);

            return redirect()->route('admin.studios.index')
                ->with('success', "Studio '{$studio->name}' berhasil dibuat dengan {$studio->seats()->count()} kursi.");
        });
    }

    public function show(Studio $studio)
    {
        $studio->load(['seats' => fn($q) => $q->orderBy('row_label')->orderBy('seat_number')]);
        $seatsByRow = $studio->seats->groupBy('row_label');
        return view('admin.studios.show', compact('studio', 'seatsByRow'));
    }

    public function edit(Studio $studio)
    {
        return view('admin.studios.edit', compact('studio'));
    }

    public function update(Request $request, Studio $studio)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'type'        => ['required', 'in:regular,imax,4dx,vip,premiere'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ]);

        $studio->update($validated);

        return redirect()->route('admin.studios.index')
            ->with('success', 'Studio berhasil diperbarui!');
    }

    public function destroy(Studio $studio)
    {
        if ($studio->schedules()->exists()) {
            return back()->with('error', 'Studio tidak dapat dihapus karena masih memiliki jadwal tayang.');
        }

        $studio->seats()->delete();
        $studio->delete();

        return redirect()->route('admin.studios.index')
            ->with('success', 'Studio berhasil dihapus.');
    }

    public function seats(Studio $studio)
    {
        $studio->load(['seats' => fn($q) => $q->orderBy('row_label')->orderBy('seat_number')]);
        $seatsByRow = $studio->seats->groupBy('row_label');
        return view('admin.studios.seats', compact('studio', 'seatsByRow'));
    }

    private function generateSeats(Studio $studio, array $data): void
    {
        $rows        = $data['total_rows'];
        $seatsPerRow = $data['seats_per_row'];
        $vipRows     = $data['vip_rows'] ? array_map('trim', explode(',', $data['vip_rows'])) : [];
        $coupleRows  = $data['couple_rows'] ? array_map('trim', explode(',', $data['couple_rows'])) : [];

        $rowLabels = array_map(fn($i) => chr(65 + $i), range(0, $rows - 1)); // A, B, C...

        foreach ($rowLabels as $rowLabel) {
            $seatType = 'regular';
            if (in_array($rowLabel, $vipRows)) {
                $seatType = 'vip';
            } elseif (in_array($rowLabel, $coupleRows)) {
                $seatType = 'couple';
            }

            for ($n = 1; $n <= $seatsPerRow; $n++) {
                Seat::create([
                    'studio_id'   => $studio->id,
                    'row_label'   => $rowLabel,
                    'seat_number' => $n,
                    'seat_code'   => $rowLabel . $n,
                    'type'        => $seatType,
                    'is_active'   => true,
                ]);
            }
        }
    }
}
