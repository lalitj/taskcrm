<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CompanysController extends Controller
{
    public function index()
    {
        return Inertia::render('Companys/Index', [
            'filters' => Request::all('search', 'trashed'),
            'companys' => Auth::user()->account->companys()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->withQueryString()
                ->through(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'phone' => $company->phone,
                        'city' => $company->city,
                        'deleted_at' => $company->deleted_at,
                    ];
                }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Companys/Create');
    }

    public function store()
    {
        Auth::user()->account->companys()->create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'email' => ['required', 'max:50', 'email'],
                'phone' => ['required', 'max:50'],
                'address' => ['required', 'max:150'],
                'city' => ['required', 'max:50'],
                'region' => ['required', 'max:50'],
                'country' => ['required', 'max:2'],
                'postal_code' => ['required', 'max:25'],
            ])
        );

        return Redirect::route('companys')->with('success', 'Company created.');
    }

    public function edit(Company $company)
    {
        return Inertia::render('Companys/Edit', [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => $company->address,
                'city' => $company->city,
                'region' => $company->region,
                'country' => $company->country,
                'postal_code' => $company->postal_code,
                'deleted_at' => $company->deleted_at,
                'contacts' => $company->contacts()->orderByName()->get()->map->only('id', 'name', 'city', 'phone'),
            ],
        ]);
    }

    public function update(Company $company)
    {
        $company->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'email' => ['required', 'max:50', 'email'],
                'phone' => ['required', 'max:50'],
                'address' => ['required', 'max:150'],
                'city' => ['required', 'max:50'],
                'region' => ['required', 'max:50'],
                'country' => ['required', 'max:2'],
                'postal_code' => ['required', 'max:25'],
            ])
        );

        return Redirect::back()->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return Redirect::back()->with('success', 'Company deleted.');
    }

    public function restore(Companys $company)
    {
        $company->restore();

        return Redirect::back()->with('success', 'Company restored.');
    }
}
