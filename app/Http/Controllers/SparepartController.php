<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\spareparts;

class SparepartController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        $results = spareparts::where('name', 'like', $query . '%')->pluck('name');
        return response()->json($results);
    }
}
