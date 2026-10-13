<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = Sale::whereDate('created_at', today());

        $week = Sale::where('created_at', '>=', today()->subDays(6))
            ->selectRaw('date(created_at) as d, sum(total) as t')
            ->groupBy('d')->pluck('t', 'd');

        $days = collect(range(6, 0))->map(fn ($i) => [
            'label' => today()->subDays($i)->format('d/m'),
            'total' => (int) ($week[today()->subDays($i)->toDateString()] ?? 0),
        ]);

        return view('dashboard', [
            'todayTotal' => (clone $today)->sum('total'),
            'todayCount' => (clone $today)->count(),
            'days' => $days,
            'lowStock' => Product::active()->lowStock()->orderBy('stock')->limit(10)->get(),
            'recentSales' => Sale::with('cashier')->latest()->limit(5)->get(),
        ]);
    }
}
