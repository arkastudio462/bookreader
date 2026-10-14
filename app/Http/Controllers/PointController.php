<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use App\Services\PointService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PointController extends Controller
{
    public function __construct(private PointService $points)
    {
    }

    public function index()
    {
        $user = Auth::user();

        $transactions = PointTransaction::where('user_id', $user->id)
            ->latest()
            ->limit(100)
            ->get();

        return Inertia::render('Points/Index', [
            'balance' => (int) $user->points_balance,
            'redeem_rate' => $this->points->redeemRate(),
            'transactions' => $transactions,
        ]);
    }
}
