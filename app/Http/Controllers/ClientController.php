<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
        ]);

        $client = \App\Models\Client::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'balance' => $validated['balance'] ?? 0,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = \App\Models\Client::findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = \App\Models\Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = \App\Models\Client::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
        ]);

        $client->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'balance' => $validated['balance'] ?? $client->balance,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = \App\Models\Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès!');
    }

    /**
     * Export clients to CSV
     */
    public function export()
    {
        $clients = \App\Models\Client::latest()->get();
        
        $csvHeader = array('Nom', 'Email', 'Téléphone', 'Adresse', 'Solde', 'Créé le');
        
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=clients_' . date('Y-m-d') . '.csv');
        
        fputcsv($handle, $csvHeader);
        
        foreach ($clients as $client) {
            fputcsv($handle, [
                $client->name,
                $client->email ?? '',
                $client->phone ?? '',
                $client->address ?? '',
                number_format($client->balance, 2),
                $client->created_at->format('d/m/Y'),
            ]);
        }
        
        fclose($handle);
        exit;
    }
}
