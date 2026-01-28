<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();
        $period = $request->get('period', 'month');
        $startDateCustom = $request->get('start_date');
        $endDateCustom = $request->get('end_date');
        
        // Calculate date range based on period
        if ($period === 'month') {
            $startDate = $now->clone()->startOfMonth();
            $endDate = $now->clone()->endOfMonth();
        } elseif ($period === 'quarter') {
            $startDate = $now->clone()->startOfQuarter();
            $endDate = $now->clone()->endOfQuarter();
        } elseif ($period === 'year') {
            $startDate = $now->clone()->startOfYear();
            $endDate = $now->clone()->endOfYear();
        } else {
            $startDate = $startDateCustom ? Carbon::parse($startDateCustom) : $now->clone()->startOfMonth();
            $endDate = $endDateCustom ? Carbon::parse($endDateCustom) : $now->clone()->endOfMonth();
        }
        
        // Get data for the selected period
        $totalSales = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalPurchases = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $grossProfit = $totalSales - $totalPurchases;
        $pendingPayments = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'paid')
            ->sum('total_amount');

        // Get sales data for the last 6 months for the chart
        $salesData = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $now->clone()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->clone()->endOfMonth();
            $sales = Invoice::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
            $salesData[] = $sales;
            $monthLabels[] = $monthStart->format('M');
        }

        $recentInvoices = Invoice::with('client')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSales',
            'totalPurchases',
            'grossProfit',
            'pendingPayments',
            'recentInvoices',
            'period',
            'salesData',
            'monthLabels'
        ));
    }
}

