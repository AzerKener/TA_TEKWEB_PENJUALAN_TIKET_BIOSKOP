<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\SeatLock;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    /**
     * Kunci kursi sementara (10 menit)
     */
    public function lock(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'seat_id'     => ['required', 'exists:seats,id'],
        ]);

        $scheduleId = $request->schedule_id;
        $seatId     = $request->seat_id;
        $token      = session()->getId();

        // Cek apakah kursi sudah dipesan
        $isBooked = Ticket::where('schedule_id', $scheduleId)
            ->where('seat_id', $seatId)
            ->whereIn('status', ['active', 'used'])
            ->exists();

        if ($isBooked) {
            return response()->json(['success' => false, 'message' => 'Kursi ini sudah dipesan.'], 409);
        }

        // Cek apakah kursi sedang dikunci oleh user lain
        $existingLock = SeatLock::where('schedule_id', $scheduleId)
            ->where('seat_id', $seatId)
            ->active()
            ->first();

        if ($existingLock && $existingLock->session_token !== $token) {
            return response()->json(['success' => false, 'message' => 'Kursi sedang dipilih oleh pengguna lain.'], 409);
        }

        // Buat atau update lock
        $lock = SeatLock::updateOrCreate(
            ['schedule_id' => $scheduleId, 'seat_id' => $seatId],
            [
                'user_id'       => auth()->id(),
                'session_token' => $token,
                'locked_at'     => now(),
                'expires_at'    => now()->addMinutes(10),
            ]
        );

        return response()->json([
            'success'           => true,
            'message'           => 'Kursi berhasil dikunci.',
            'expires_at'        => $lock->expires_at->toISOString(),
            'remaining_seconds' => 600,
        ]);
    }

    /**
     * Lepas kunci kursi
     */
    public function unlock(Request $request): JsonResponse
    {
        $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'seat_id'     => ['required', 'exists:seats,id'],
        ]);

        SeatLock::where('schedule_id', $request->schedule_id)
            ->where('seat_id', $request->seat_id)
            ->where('session_token', session()->getId())
            ->delete();

        return response()->json(['success' => true, 'message' => 'Kursi berhasil dilepas.']);
    }

    /**
     * Status semua kursi untuk jadwal tertentu (untuk seat map)
     */
    public function status(Schedule $schedule): JsonResponse
    {
        $schedule->load('studio.seats');
        $token = session()->getId();

        $bookedSeatIds = Ticket::where('schedule_id', $schedule->id)
            ->whereIn('status', ['active', 'used'])
            ->pluck('seat_id')
            ->toArray();

        $activeLocks = SeatLock::where('schedule_id', $schedule->id)
            ->active()
            ->get()
            ->keyBy('seat_id');

        $seats = $schedule->studio->seats->map(function ($seat) use ($bookedSeatIds, $activeLocks, $token) {
            $lock = $activeLocks->get($seat->id);
            $status = 'available';

            if (in_array($seat->id, $bookedSeatIds)) {
                $status = 'booked';
            } elseif ($lock) {
                $status = ($lock->session_token === $token) ? 'mine' : 'locked';
            }

            return [
                'id'        => $seat->id,
                'code'      => $seat->seat_code,
                'row'       => $seat->row_label,
                'number'    => $seat->seat_number,
                'type'      => $seat->type,
                'status'    => $status,
            ];
        });

        return response()->json([
            'seats'    => $seats,
            'schedule' => [
                'price_regular' => $schedule->price_regular,
                'price_vip'     => $schedule->price_vip,
                'price_couple'  => $schedule->price_couple,
            ],
        ]);
    }
}
