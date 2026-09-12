<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VacationRequest;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $requests = $user->isAdmin()
            ? VacationRequest::with('user')->latest()->get()
            : VacationRequest::with('user')->where('user_id', $user->id)->latest()->get();

        return view('vacation.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $request->user()->vacationRequests()->create($data + ['status' => 'pending']);

        return back()->with('status', 'Žádost o dovolenou byla odeslána ke schválení.');
    }

    public function approve(VacationRequest $vacation)
    {
        $vacation->update(['status' => 'approved']);

        return back()->with('status', 'Dovolená byla schválena.');
    }

    public function reject(VacationRequest $vacation)
    {
        $vacation->update(['status' => 'rejected']);

        return back()->with('status', 'Dovolená byla zamítnuta.');
    }
}
