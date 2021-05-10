<?php

namespace App\Http\Api\Controllers;

use App\Models\Bank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class BanksController extends Controller
{
    public function __construct()
    {
        auth()->loginUsingId(1);
    }

    public function index()
    {
        return Inertia::render('Banks/Index', [
            'filters' => Request::all('search', 'trashed'),
            'banks' => Auth::user()->account->banks()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->withQueryString()
                ->through(function ($bank) {
                    return [
                        'id' => $bank -> id,
                        'name' => $bank -> name,
                        'phone' => $bank -> phone,
                        'account_number' => $bank -> account_number,
                        'ifsc_code' => $bank -> ifsc_code,
                        'bank_name' =>$bank->bank_name,
                      'email'=> $bank ->email,
                        'deleted_at' => $bank -> deleted_at,
                    ];
                }),
        ]);
    }

    

    public function store()
    {
        Auth::user()->account->banks()->create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'phone' => ['nullable', 'max:50'],
                'account_number' => ['nullable','max:100'],
                'ifsc_code' => ['nullable','max:100'],
                'bank_name' => ['nullable','max:100'],
                'email' => ['nullable','max:100'],
                
                
            ])
        );


        return $bank->refresh();
    }

    public function show(Bank $bank)
    {
        return $bank;
    }
 

    public function edit(Bank $bank)
    {
        return Inertia::render('Banks/Edit', [
            'bank' => [
                'id' => $bank -> id,
                'name' => $bank -> name,
                'phone' => $bank -> phone,
                'account_number' => $bank -> account_number,
                'ifsc_code' => $bank -> ifsc_code,
                'bank_name' =>$bank->bank_name,
                'email'=> $bank ->email,
                'deleted_at' => $bank -> deleted_at,
                ],
        ]);
    }

    public function update(Bank $bank)
    {
        $bank->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'phone' => ['nullable', 'max:50'],
                'account_number' => ['nullable','max:100'],
                'ifsc_code' => ['nullable','max:100'],
                'bank_name' => ['nullable','max:100'],
                'email' => ['nullable','max:100'],
                
            ])

        
        );

        
        return $task;
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();

        return response()->json(['success' => 'Bank restored.']);
   }

    public function restore(Bank $bank)
    {
        $bank->restore();

        return response()->json(['success' => 'Bank restored.']);
   }
}
