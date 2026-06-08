<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FnbItem;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\SeatLock;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\TransactionFnbItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Langkah 1: Pilih jadwal untuk film tertentu
     */
    public function schedule(Movie $movie)
    {
        $schedules = Schedule::where('movie_id', $movie->id)
            ->upcoming()
            ->with(['studio', 'tickets'])
            ->orderBy('show_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($s) => $s->show_date->format('Y-m-d'));

        return view('customer.booking.schedule', compact('movie', 'schedules'));
    }

    /**
     * Langkah 2: Pilih kursi (seat map interaktif)
     */
    public function seats(Schedule $schedule)
    {
        $schedule->load(['movie', 'studio.seats' => fn($q) => $q->where('is_active', true)->orderBy('row_label')->orderBy('seat_number')]);

        // Kursi yang sudah dipesan (tiket aktif)
        $bookedSeatIds = Ticket::where('schedule_id', $schedule->id)
            ->whereIn('status', ['active', 'used'])
            ->pluck('seat_id')
            ->toArray();

        // Kursi yang sedang dikunci oleh user lain
        $lockedSeatIds = SeatLock::where('schedule_id', $schedule->id)
            ->active()
            ->where('session_token', '!=', session()->getId())
            ->pluck('seat_id')
            ->toArray();

        // Kursi yang dikunci oleh user ini sendiri
        $myLockedSeatIds = SeatLock::where('schedule_id', $schedule->id)
            ->active()
            ->where('session_token', session()->getId())
            ->pluck('seat_id')
            ->toArray();

        // Group seats by row
        $seatsByRow = $schedule->studio->seats->groupBy('row_label');

        return view('customer.booking.seats', compact(
            'schedule',
            'seatsByRow',
            'bookedSeatIds',
            'lockedSeatIds',
            'myLockedSeatIds',
        ));
    }

    /**
     * Langkah 3: Tambah F&B (opsional)
     */
    public function fnb(Schedule $schedule)
    {
        $fnbItems = FnbItem::available()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $lockedSeats = SeatLock::where('schedule_id', $schedule->id)
            ->active()
            ->where('session_token', session()->getId())
            ->with('seat')
            ->get();

        if ($lockedSeats->isEmpty()) {
            return redirect()->route('booking.seats', $schedule)
                ->with('error', 'Silakan pilih kursi terlebih dahulu.');
        }

        $schedule->load('movie', 'studio');

        return view('customer.booking.fnb', compact('schedule', 'fnbItems', 'lockedSeats'));
    }

    /**
     * Langkah 4: Checkout & Ringkasan Pemesanan
     */
    public function checkout(Request $request)
    {
        $scheduleId = $request->session()->get('booking.schedule_id') ?? $request->input('schedule_id');

        if (!$scheduleId) {
            return redirect()->route('home')->with('error', 'Sesi pemesanan tidak valid.');
        }

        $schedule = Schedule::with(['movie', 'studio'])->findOrFail($scheduleId);

        $lockedSeats = SeatLock::where('schedule_id', $schedule->id)
            ->active()
            ->where('session_token', session()->getId())
            ->with('seat')
            ->get();

        if ($lockedSeats->isEmpty()) {
            return redirect()->route('booking.seats', $schedule)
                ->with('error', 'Kursi pilihan Anda telah kedaluwarsa. Silakan pilih ulang.');
        }

        $fnbCart = $request->session()->get('booking.fnb', []);
        $fnbItems = [];
        $subtotalFnb = 0;

        if (!empty($fnbCart)) {
            $fnbItems = FnbItem::whereIn('id', array_keys($fnbCart))->get()->map(function ($item) use ($fnbCart) {
                $qty = $fnbCart[$item->id];
                return [
                    'item'     => $item,
                    'quantity' => $qty,
                    'subtotal' => $item->price * $qty,
                ];
            });
            $subtotalFnb = $fnbItems->sum('subtotal');
        }

        // Hitung subtotal tiket
        $subtotalTicket = $lockedSeats->sum(function ($lock) use ($schedule) {
            return match ($lock->seat->type) {
                'vip'    => $schedule->price_vip,
                'couple' => $schedule->price_couple,
                default  => $schedule->price_regular,
            };
        });

        $taxAmount = ($subtotalTicket + $subtotalFnb) * 0.10;
        $totalAmount = $subtotalTicket + $subtotalFnb + $taxAmount;

        $paymentMethods = [
            'qris'          => 'QRIS',
            'gopay'         => 'GoPay',
            'ovo'           => 'OVO',
            'dana'          => 'DANA',
            'shopee_pay'    => 'ShopeePay',
            'transfer_bank' => 'Transfer Bank',
            'credit_card'   => 'Kartu Kredit',
            'debit_card'    => 'Kartu Debit',
        ];

        return view('customer.booking.checkout', compact(
            'schedule', 'lockedSeats', 'fnbItems', 'fnbCart',
            'subtotalTicket', 'subtotalFnb', 'taxAmount', 'totalAmount',
            'paymentMethods'
        ));
    }

    /**
     * Proses pembayaran & buat transaksi
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'schedule_id'    => ['required', 'exists:schedules,id'],
            'payment_method' => ['required', 'string'],
            'seat_ids'       => ['required', 'array', 'min:1'],
            'seat_ids.*'     => ['exists:seats,id'],
            'fnb'            => ['nullable', 'array'],
            'fnb.*'          => ['integer', 'min:1'],
        ]);

        $schedule = Schedule::with('studio')->findOrFail($validated['schedule_id']);

        return DB::transaction(function () use ($validated, $schedule, $request) {
            // Verify seats masih terkunci oleh user ini
            $lockedSeats = SeatLock::where('schedule_id', $schedule->id)
                ->active()
                ->where('session_token', session()->getId())
                ->whereIn('seat_id', $validated['seat_ids'])
                ->with('seat')
                ->get();

            if ($lockedSeats->count() !== count($validated['seat_ids'])) {
                return back()->with('error', 'Beberapa kursi tidak lagi tersedia. Silakan ulangi pemesanan.');
            }

            // Hitung harga
            $subtotalTicket = $lockedSeats->sum(fn($lock) => match ($lock->seat->type) {
                'vip'    => $schedule->price_vip,
                'couple' => $schedule->price_couple,
                default  => $schedule->price_regular,
            });

            $subtotalFnb = 0;
            $fnbCartData = [];

            if (!empty($validated['fnb'])) {
                $fnbItems = FnbItem::whereIn('id', array_keys($validated['fnb']))->get()->keyBy('id');
                foreach ($validated['fnb'] as $fnbId => $qty) {
                    if ($fnbItems->has($fnbId)) {
                        $item = $fnbItems[$fnbId];
                        $sub = $item->price * $qty;
                        $subtotalFnb += $sub;
                        $fnbCartData[] = ['item' => $item, 'qty' => $qty, 'subtotal' => $sub];
                    }
                }
            }

            $taxAmount   = ($subtotalTicket + $subtotalFnb) * 0.10;
            $totalAmount = $subtotalTicket + $subtotalFnb + $taxAmount;

            // Buat transaksi
            $transaction = Transaction::create([
                'user_id'        => auth()->id(),
                'schedule_id'    => $schedule->id,
                'subtotal_ticket'=> $subtotalTicket,
                'subtotal_fnb'   => $subtotalFnb,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid', // Simulasi: langsung paid
                'paid_at'        => now(),
            ]);

            // Buat tiket per kursi
            foreach ($lockedSeats as $lock) {
                $seatPrice = match ($lock->seat->type) {
                    'vip'    => $schedule->price_vip,
                    'couple' => $schedule->price_couple,
                    default  => $schedule->price_regular,
                };

                Ticket::create([
                    'transaction_id' => $transaction->id,
                    'seat_id'        => $lock->seat_id,
                    'schedule_id'    => $schedule->id,
                    'seat_type'      => $lock->seat->type,
                    'price'          => $seatPrice,
                    'status'         => 'active',
                ]);
            }

            // Buat item F&B
            foreach ($fnbCartData as $fnbData) {
                TransactionFnbItem::create([
                    'transaction_id' => $transaction->id,
                    'fnb_item_id'    => $fnbData['item']->id,
                    'quantity'       => $fnbData['qty'],
                    'unit_price'     => $fnbData['item']->price,
                    'subtotal'       => $fnbData['subtotal'],
                ]);
            }

            // Hapus seat locks
            SeatLock::where('session_token', session()->getId())
                ->where('schedule_id', $schedule->id)
                ->delete();

            // Clear session booking
            $request->session()->forget(['booking']);

            return redirect()->route('booking.success', $transaction)
                ->with('success', 'Pembayaran berhasil! E-Tiket Anda sudah siap.');
        });
    }

    /**
     * Halaman sukses & e-ticket
     */
    public function success(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load([
            'schedule.movie',
            'schedule.studio',
            'tickets.seat',
            'fnbItems.fnbItem',
        ]);

        return view('customer.booking.success', compact('transaction'));
    }
}
