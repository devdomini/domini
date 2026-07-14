<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Services\CommercialPortfolioService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(CommercialPortfolioService $portfolio)
    {
        $stats = $portfolio->dashboardStats(Auth::user());

        return view('commercial.dashboard', compact('stats'));
    }
}
