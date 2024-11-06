<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource with optional filters by name or email.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('name') || $request->has('email')) {
            $query->where(function ($q) use ($request) {
                if ($request->has('name')) {
                    $q->where('name', $request->input('name'));
                }
                if ($request->has('email')) {
                    $q->orWhere('email', $request->input('email'));
                }
            });
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create([
            'id' => (string) Str::uuid(),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'annualSpend' => $request->input('annualSpend', null),
            'lastPurchaseDate' => $request->input('lastPurchaseDate', null),
        ]);

        return response()->json($customer, 201);
    }

    /**
     * Display the specified customer by ID.
     */
    public function show(string $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json($customer);
    }

    /**
     * Update the specified customer by ID.
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $customer->update($request->all());

        return response()->json($customer);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }
}
