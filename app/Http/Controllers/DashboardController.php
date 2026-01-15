<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = Invoice::sum('total_amount');
        $totalPurchases = Purchase::sum('total_amount');
        $grossProfit = $totalSales - $totalPurchases;
        
        // Simplified pending payments: Invoices that are not 'paid'
        $pendingPayments = Invoice::where('status', '!=', 'paid')->sum('total_amount');

        $recentInvoices = Invoice::with('client')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalSales',
            'totalPurchases',
            'grossProfit',
            'pendingPayments',
            'recentInvoices'
        ));
    }
}
