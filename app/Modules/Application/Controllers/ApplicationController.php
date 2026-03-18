<?php

namespace App\Modules\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Application\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function create()
    {
        return view('apply');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',

            'adults' => 'required|integer|min:1',
            'kids' => 'nullable|integer|min:0',

            'nationality' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',

            'employment' => 'required|string',
            'remote' => 'required|boolean',
            'income' => 'required|numeric',

            'move_date' => 'nullable|date',
            'referral' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // create or get user
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => bcrypt('12345678')
                // 'password' => bcrypt(Str::random(10))
            ]
        );

        Auth::login($user);

        $total = ($data['adults'] * 100) + ($data['kids'] * 50);

        $application = Application::create([
            ...$data,
            'user_id' => $user->id,
            'total_price' => $total,
        ]);

        collect([1, 2, 3])->each(function ($stage) use ($application, $user) {

            $application->payments()->create([
                'case_id' => 1,
                'user_id' => $user->id,
                'amount' => $stage * 100,
                'currency' => 'usd',
                'status' => $stage === 1 ? 'pending' : 'locked',
                'stage' => $stage,
            ]);
        });


        return redirect()->route('client.dashboard');
    }
}
