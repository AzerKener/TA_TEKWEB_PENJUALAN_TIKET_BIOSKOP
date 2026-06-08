<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with(['schedule.movie', 'schedule.studio', 'tickets'])
            ->latest()
            ->paginate(10);

        return view('customer.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
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

        return view('customer.transactions.show', compact('transaction'));
    }

    public function eTicket(string $code)
    {
        $transaction = Transaction::where('transaction_code', $code)
            ->where('user_id', auth()->id())
            ->with(['schedule.movie', 'schedule.studio', 'tickets.seat'])
            ->firstOrFail();

        return view('customer.transactions.eticket', compact('transaction'));
    }
}
