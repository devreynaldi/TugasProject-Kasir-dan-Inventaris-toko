<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['from' => 'nullable|date', 'to' => 'nullable|date|after_or_equal:from']);

        $from = ($data['from'] ?? now()->startOfMonth()->toDateString()).' 00:00:00';
        $to = ($data['to'] ?? now()->toDateString()).' 23:59:59';

        $sales = Sale::whereBetween('created_at', [$from, $to]);

        return view('reports.index', [
            'from' => substr($from, 0, 10),
            'to' => substr($to, 0, 10),
            'revenue' => (clone $sales)->sum('total'),
            'count' => (clone $sales)->count(),
            'daily' => (clone $sales)->selectRaw('date(created_at) as day, count(*) as trx, sum(total) as total')
                ->groupBy('day')->orderBy('day')->get(),
            'top' => SaleItem::whereBetween('created_at', [$from, $to])
                ->selectRaw('product_name, sum(qty) as qty, sum(subtotal) as revenue')
                ->groupBy('product_name')->orderByDesc('qty')->limit(5)->get(),
        ]);
    }
}
