<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'schedule.movie', 'tickets'])->latest();

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('transaction_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => Transaction::count(),
            'paid'    => Transaction::paid()->count(),
            'pending' => Transaction::pending()->count(),
            'revenue' => Transaction::paid()->sum('total_amount'),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load([
            'user',
            'schedule.movie',
            'schedule.studio',
            'tickets.seat',
            'fnbItems.fnbItem',
        ]);

        return view('admin.transactions.show', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,expired,refunded'],
        ]);

        $transaction->update([
            'payment_status' => $request->payment_status,
            'paid_at'        => $request->payment_status === 'paid' ? now() : $transaction->paid_at,
        ]);

        // Update tiket jika refunded/failed
        if (in_array($request->payment_status, ['refunded', 'failed'])) {
            $transaction->tickets()->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Status transaksi berhasil diperbarui.');
    }
}
