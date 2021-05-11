<?php

namespace App\Http\Api\Controllers;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class AddressesController extends Controller
{
    public function __construct()
    {
        auth()->loginUsingId(1);
    }

    public function index()
    {
        return [
            'filters' => Request::all('search', 'trashed'),
            'addresses' => Auth::user()->account->addresses()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->withQueryString()
                ->through(function ($address) {
                    return [
                        'id' => $address -> id,
                        'name' => $address -> name,
                        'phone' => $address -> phone,
                        'address_line1' =>$address -> address_line1,
                        'address_line2' =>$address -> address_line2,
                        'city' => $address -> city,
                        'region' => $address -> region,
                        'country' => $address -> country,
                        'postal_code' => $address -> postal_code,
                        'deleted_at' => $address -> deleted_at,
                    ];
                }),
        ];
    }


   
    public function store()
    {
        $address=Auth::user()->account->addresses()->create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'phone' => ['nullable', 'max:20'],
                'address_line1' => ['max:200'],
                'address_line2' => ['max:200'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:50'],
                'postal_code' => ['nullable', 'max:25'],
            
            
                ])
        );


        return $address->refresh();
    }

    public function show(Address $address)
    {
        return $address;
    }
 
    public function edit(Address $address)
    {
        return Inertia::render('Addresses/Edit', [
            'address' => [
                'id' => $address -> id,
                'name' => $address -> name,
                'phone' => $address -> phone,
                'address_line1' => $address -> address_line1,
                'address_line2' => $address -> address_line2,
                'city' => $address -> city,
                'region' => $address -> region,
                'country' => $address -> country,
                'postal_code' => $address -> postal_code,   
                'deleted_at' => $address -> deleted_at,
                ],
        ]);
    }

    public function update(Address $address)
    {
        $address->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'phone' => ['nullable', 'max:20'],
                'address_line1' => ['max:200'],
                'address_line2' => ['max:200'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:50'],
                'postal_code' => ['nullable', 'max:25'],

            ])
        );

        return $address;
     }

    public function destroy(Address $address)
    {
        $address->delete();
        return response()->json(['success' => 'Address deleted.']);
    }

    public function restore(Address $address)
    {
        $address->restore();
        return response()->json(['success' => 'Address restored.']);
     }
}
