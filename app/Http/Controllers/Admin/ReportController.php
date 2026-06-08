<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->format('Y-m-d'));

        // Pendapatan per hari dalam rentang
        $dailyRevenue = Transaction::paid()
            ->whereBetween(DB::raw('DATE(paid_at)'), [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->selectRaw('DATE(paid_at) as date, SUM(total_amount) as revenue, COUNT(*) as count')
            ->orderBy('date')
            ->get();

        // Total periode
        $periodRevenue = $dailyRevenue->sum('revenue');
        $periodCount   = $dailyRevenue->sum('count');

        // Revenue per metode pembayaran
        $paymentMethodStats = Transaction::paid()
            ->whereBetween(DB::raw('DATE(paid_at)'), [$startDate, $endDate])
            ->groupBy('payment_method')
            ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
            ->get();

        // Top film by revenue
        $topMoviesByRevenue = Movie::select('movies.*')
            ->join('schedules', 'movies.id', '=', 'schedules.movie_id')
            ->join('transactions', 'schedules.id', '=', 'transactions.schedule_id')
            ->where('transactions.payment_status', 'paid')
            ->whereBetween(DB::raw('DATE(transactions.paid_at)'), [$startDate, $endDate])
            ->groupBy('movies.id', 'movies.title', 'movies.poster_image', 'movies.status')
            ->selectRaw('movies.id, movies.title, movies.poster_image, movies.status, SUM(transactions.total_amount) as revenue, COUNT(transactions.id) as trx_count')
            ->orderByDesc('revenue')
            ->take(10)
            ->get();

        // Occupancy rate per studio
        $studioOccupancy = Schedule::select('studios.name as studio_name')
            ->join('studios', 'schedules.studio_id', '=', 'studios.id')
            ->whereBetween('schedules.show_date', [$startDate, $endDate])
            ->groupBy('studios.id', 'studios.name')
            ->selectRaw('studios.id, studios.name as studio_name, COUNT(schedules.id) as schedule_count')
            ->get();

        return view('admin.reports.index', compact(
            'dailyRevenue', 'periodRevenue', 'periodCount',
            'paymentMethodStats', 'topMoviesByRevenue', 'studioOccupancy',
            'startDate', 'endDate'
        ));
    }

    public function export(Request $request)
    {
        // Simple CSV export
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->format('Y-m-d'));

        $transactions = Transaction::paid()
            ->whereBetween(DB::raw('DATE(paid_at)'), [$startDate, $endDate])
            ->with(['user', 'schedule.movie', 'tickets'])
            ->get();

        $filename = "laporan_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['Kode Transaksi', 'Nama Customer', 'Film', 'Tanggal Bayar', 'Metode', 'Total', 'Jumlah Tiket']);

            foreach ($transactions as $trx) {
                fputcsv($file, [
                    $trx->transaction_code,
                    $trx->user->name,
                    $trx->schedule->movie->title ?? '-',
                    $trx->paid_at?->format('d/m/Y H:i'),
                    $trx->payment_method_label,
                    $trx->total_amount,
                    $trx->tickets->count(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
