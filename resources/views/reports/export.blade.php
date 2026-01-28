<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport - {{ now()->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #007bff;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .period-info {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section h2 {
            color: #007bff;
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
            margin-top: 0;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-card {
            display: table-cell;
            width: 23%;
            padding: 15px;
            margin-right: 2%;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            vertical-align: top;
        }
        .stat-card.green {
            border-left-color: #28a745;
        }
        .stat-card.purple {
            border-left-color: #6f42c1;
        }
        .stat-card.orange {
            border-left-color: #fd7e14;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .right-align {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'Analyse</h1>
        <p>Généré le {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="period-info">
        <strong>Période:</strong> {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
    </div>

    <!-- Key Metrics -->
    <div class="section">
        <h2>Indicateurs Clés</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Chiffre d'affaires</div>
                <div class="stat-value">{{ number_format($totalSales, 0, ',', '.') }} MAD</div>
                <div class="stat-label">Croissance: {{ $salesGrowth > 0 ? '+' : '' }}{{ $salesGrowth }}%</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Bénéfice Brut</div>
                <div class="stat-value">{{ number_format($grossProfit, 0, ',', '.') }} MAD</div>
                <div class="stat-label">(Ventes - Achats)</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-label">Marge Bénéficiaire</div>
                <div class="stat-value">{{ $profitMargin }}%</div>
                <div class="stat-label">de rentabilité</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Total Achats</div>
                <div class="stat-value">{{ number_format($totalPurchases, 0, ',', '.') }} MAD</div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="section">
        <h2>Produits les Plus Vendus</h2>
        <table>
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Produit</th>
                    <th class="right-align">Nombre de Ventes</th>
                    <th class="right-align">Montant Total (MAD)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topProducts as $index => $product)
                    @php
                        $totalSold = $product->invoiceItems->sum('total_price');
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->designation }}</td>
                        <td class="right-align">{{ $product->invoice_items_count }}</td>
                        <td class="right-align">{{ number_format($totalSold, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">Aucun produit vendu</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Statistics -->
    <div class="section">
        <h2>Statistiques</h2>
        <table>
            <thead>
                <tr>
                    <th>Indicateur</th>
                    <th class="right-align">Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nombre Total de Clients</td>
                    <td class="right-align">{{ $clientCount }}</td>
                </tr>
                <tr>
                    <td>Nombre Total de Devis</td>
                    <td class="right-align">{{ $quoteCount }}</td>
                </tr>
                <tr>
                    <td>Devis Acceptés</td>
                    <td class="right-align">{{ $acceptedQuotes }} ({{ $quoteCount > 0 ? round(($acceptedQuotes / $quoteCount) * 100) : 0 }}%)</td>
                </tr>
                <tr>
                    <td>Nombre Total de Factures</td>
                    <td class="right-align">{{ $totalInvoices }}</td>
                </tr>
                <tr>
                    <td>Factures Payées</td>
                    <td class="right-align">{{ $paidInvoices }} ({{ $totalInvoices > 0 ? round(($paidInvoices / $totalInvoices) * 100) : 0 }}%)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Recent Invoices -->
    <div class="section">
        <h2>Factures Récentes</h2>
        <table>
            <thead>
                <tr>
                    <th>N° Facture</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th class="right-align">Montant (MAD)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentInvoices as $invoice)
                    <tr>
                        <td>#{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->client->name ?? 'N/A' }}</td>
                        <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                        <td class="right-align">{{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">Aucune facture</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système AluERP</p>
    </div>
</body>
</html>
