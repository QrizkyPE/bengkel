<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'service') {
            return redirect()->route('requests.index');
        } elseif ($user->role === 'estimator') {
            return redirect()->route('estimations.index');
        } elseif ($user->role === 'billing') {
            return redirect()->route('billing.index');
        }
        
        return view('home');
    }
}
