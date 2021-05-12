<?php

namespace App\Http\Controllers;

use App\Models\Daily;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DailysController extends Controller
{
    public function index()
    {
        return Inertia::render('Dailys/Index', [
            'filters' => Request::all('search', 'trashed'),
            'dailys' => Auth::user()->account->dailys()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->withQueryString()
                ->through(function ($daily) {
                    return [
                        'id' => $daily->id,
                        'name' => $daily->name,
                        'description' => $daily->description,
                        'status' => $daily->status,
                        // 'priority' => $daily->priority,
                        'hubstaff' => $daily->hubstaff,
                        'date' =>$daily->date,
                        // 'completed_date' => $daily->completed_date,
                        'deleted_at' => $daily->deleted_at,
                    ];
                }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dailys/Create');
    }

    public function store()
    {
        Auth::user()->account->dailys()->create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'description' => ['nullable', 'max:100'],
                'status' => ['nullable', 'max:4'],
                // 'priority' => ['nullable', 'max:4'],
                'hubstaff' => ['nullable', 'max:50'],
                'date' => ['nullable', 'max:30'],
                // 'completed_date' => ['nullable', 'max:30'],
            ])
        );

        return Redirect::route('dailys')->with('success', 'Dailys created.');
    }

    public function edit(Daily $daily)
    {
        return Inertia::render('Dailys/Edit', [
            'daily' => [
                'id' => $daily->id,
                        'name' => $daily->name,
                        'description' => $daily->description,
                        'status' => $daily->status,
                        // 'priority' => $daily->priority,
                        'hubstaff' => $daily->hubstaff,
                        'date' =>$daily->date,
                        'completed_date' => $daily->completed_date,
                        // 'deleted_at' => $daily->deleted_at,
            ],
        ]);
    }

    public function update(Daily $daily)
    {
        $daily->update(
            Request::validate([
              'name' => ['required', 'max:100'],
              'description' => ['nullable', 'max:100'],
              'status' => ['nullable', 'max:4'],
              // 'priority' => ['nullable', 'max:4'],
              // 'status' => ['nullable', 'max:4'],
              'hubstaff' => ['nullable', 'max:50'],
              'date' => ['nullable', 'max:30'],
              // 'completed_date' => ['nullable', 'max:30'],
            ])
        );

        return Redirect::back()->with('success', 'Daily updated.');
    }

    public function destroy(Daily $daily)
    {
        $daily->delete();

        return Redirect::back()->with('success', 'Daily deleted.');
    }

    public function restore(Daily $daily)
    {
        $daily->restore();

        return Redirect::back()->with('success', 'Daily restored.');
    }
}
