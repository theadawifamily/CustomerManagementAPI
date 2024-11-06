<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'annualSpend' => 'nullable|numeric',
            'lastPurchaseDate' => 'nullable|date',
        ]);

        $customer = Customer::create([
            'id' => (string) Str::uuid(),
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'annualSpend' => $validatedData['annualSpend'] ?? null,
            'lastPurchaseDate' => $validatedData['lastPurchaseDate'] ?? null,
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
    public function update(Request $request, string $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:customers,email,' . $id,
            'annualSpend' => 'nullable|numeric',
            'lastPurchaseDate' => 'nullable|date',
        ]);

        $customer->update($validatedData);

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
