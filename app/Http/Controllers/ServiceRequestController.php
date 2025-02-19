<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::where('user_id', Auth::id())->get();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        return view('requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sparepart_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'kebutuhan_part' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        ServiceRequest::create([
            'user_id' => Auth::id(),
            'sparepart_name' => $request->sparepart_name,
            'quantity' => $request->quantity,
            'kebutuhan_part' => $request->kebutuhan_part,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('requests.index')->with('success', 'Permintaan sparepart berhasil ditambahkan.');
    }

    public function show(ServiceRequest $request)
    {
        return view('requests.show', compact('request'));
    }

    public function edit($id)
    {
        $request = ServiceRequest::findOrFail($id);
        return view('requests.edit', compact('request'));
    }

    public function update(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        
        // Add logging to debug
        \Log::info('Update method - Input:', $request->all());
        \Log::info('Update method - ServiceRequest before:', $serviceRequest->toArray());

        $validated = $request->validate([
            'sparepart_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'kebutuhan_part' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Keep the existing user_id and update other fields
        $result = $serviceRequest->update([
            'user_id' => $serviceRequest->user_id, // Preserve the existing user_id
            'sparepart_name' => $validated['sparepart_name'],
            'quantity' => $validated['quantity'],
            'kebutuhan_part' => $validated['kebutuhan_part'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        \Log::info('Update method - Update result:', ['success' => $result]);
        \Log::info('Update method - ServiceRequest after:', $serviceRequest->fresh()->toArray());

        return redirect()->route('requests.index')->with('success', 'Permintaan berhasil diperbarui.');
    }

    public function destroy(ServiceRequest $request)
    {
        $request->delete();
        return redirect()->route('requests.index')->with('success', 'Permintaan dihapus.');
    }
}
