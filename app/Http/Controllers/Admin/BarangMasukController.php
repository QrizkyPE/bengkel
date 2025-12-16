<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of barang masuk.
     */
    public function index()
    {
        $spareparts = Sparepart::orderBy('nama')->get();
        $barangMasuk = BarangMasuk::with(['sparepart', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        return view('admin.barang-masuk.index', compact('spareparts', 'barangMasuk'));
    }

    /**
     * Store a newly created barang masuk.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sparepart_id' => 'required|exists:spareparts,id',
            'jumlah_masuk' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $sparepart = Sparepart::findOrFail($request->sparepart_id);

            // Increase sparepart stock
            $sparepart->jumlah += $request->jumlah_masuk;
            $sparepart->save();

            // Create barang masuk record
            $barangMasuk = BarangMasuk::create([
                'sparepart_id' => $request->sparepart_id,
                'jumlah_masuk' => $request->jumlah_masuk,
                'keterangan' => $request->keterangan,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('admin.barang-masuk.index')
                ->with('success', 'Barang masuk berhasil ditambahkan. Stok ' . $sparepart->nama . ' bertambah ' . $request->jumlah_masuk . ' ' . $sparepart->satuan);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
