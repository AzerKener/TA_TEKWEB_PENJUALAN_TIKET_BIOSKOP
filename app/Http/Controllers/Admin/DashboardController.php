<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI Cards
        $totalRevenue    = Transaction::paid()->sum('total_amount');
        $totalTickets    = \App\Models\Ticket::where('status', 'active')->count();
        $totalCustomers  = User::customer()->count();
        $totalMovies     = Movie::nowPlaying()->count();

        // Revenue chart: last 7 days
        $revenueChart = Transaction::paid()
            ->where('paid_at', '>=', now()->subDays(6))
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->selectRaw('DATE(paid_at) as date, SUM(total_amount) as total')
            ->orderBy('date')
            ->get();

        // Top 5 movies by ticket count
        $topMovies = Movie::withCount(['schedules as ticket_count' => function ($q) {
                $q->join('tickets', 'schedules.id', '=', 'tickets.schedule_id')
                  ->where('tickets.status', 'active');
            }])
            ->orderByDesc('ticket_count')
            ->take(5)
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::with(['user', 'schedule.movie'])
            ->latest()
            ->take(10)
            ->get();

        // Occupancy today
        $todaySchedules = Schedule::byDate(today())
            ->with(['studio', 'tickets'])
            ->get();

        return view('admin.dashboard.index', compact(
            'totalRevenue', 'totalTickets', 'totalCustomers', 'totalMovies',
            'revenueChart', 'topMovies', 'recentTransactions', 'todaySchedules'
        ));
    }
}
