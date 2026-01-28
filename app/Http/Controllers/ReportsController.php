<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Quote;
use App\Models\Article;
use App\Models\Client;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDateCustom = null;
        $endDateCustom = null;
        
        // Handle custom date range
        if ($period === 'custom') {
            $startDateCustom = Carbon::parse($request->get('start_date'))->startOfDay();
            $endDateCustom = Carbon::parse($request->get('end_date'))->endOfDay();
        }
        
        return view('reports.index', [
            'period' => $period,
            'startDateCustom' => $startDateCustom,
            'endDateCustom' => $endDateCustom
        ]);
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'month');
        $now = now();
        
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
            $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        }
        
        // Get data
        $totalSales = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalPurchases = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $grossProfit = $totalSales - $totalPurchases;
        $profitMargin = $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) : 0;
        
        $prevStartDate = $startDate->clone()->subMonths(1);
        $prevEndDate = $endDate->clone()->subMonths(1);
        $prevSales = Invoice::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total_amount');
        $salesGrowth = $prevSales > 0 ? round((($totalSales - $prevSales) / $prevSales) * 100, 1) : 0;
        
        $topProducts = Article::with('invoiceItems')
            ->withCount('invoiceItems')
            ->orderBy('invoice_items_count', 'desc')
            ->limit(5)
            ->get();
        
        $recentInvoices = Invoice::with('client')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(10)
            ->get();
        
        $clientCount = Client::count();
        $quoteCount = Quote::count();
        $acceptedQuotes = Quote::where('status', 'accepted')->count();
        $paidInvoices = Invoice::where('status', 'payee')->count();
        $totalInvoices = Invoice::count();
        
        $data = [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'grossProfit' => $grossProfit,
            'profitMargin' => $profitMargin,
            'salesGrowth' => $salesGrowth,
            'topProducts' => $topProducts,
            'recentInvoices' => $recentInvoices,
            'clientCount' => $clientCount,
            'quoteCount' => $quoteCount,
            'acceptedQuotes' => $acceptedQuotes,
            'paidInvoices' => $paidInvoices,
            'totalInvoices' => $totalInvoices,
        ];
        
        // Generate HTML report
        $html = view('reports.export', $data)->render();
        
        return response($html, 200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="rapport-' . now()->format('Y-m-d-H-i-s') . '.html"');
    }
}
