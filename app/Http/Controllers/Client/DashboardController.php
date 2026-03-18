<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $application = auth()->user()
            ->applications()
            ->latest()
            ->first();

        $payments = $application?->payments ?? [];

        return view('client.dashboard', compact('payments', 'application'));
    }
}
