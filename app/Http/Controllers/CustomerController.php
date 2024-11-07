<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
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
        try {
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

            $customers = $query->get();

            return response()->json($customers, 200);

        } catch (QueryException $e) {
            // Log the specific database error
            Log::error('Database error in CustomerController@index: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to retrieve customers due to a database error.'
            ], 500);

        } catch (\Exception $e) {
            // Log any general errors
            Log::error('General error in CustomerController@index: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while retrieving customers.'
            ], 500);
        }
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            $customer = Customer::create([
                'id' => (string) Str::uuid(),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'annualSpend' => $request->input('annualSpend', null),
                'lastPurchaseDate' => $request->input('lastPurchaseDate', null),
            ]);

            return response()->json($customer, 201);

        } catch (QueryException $e) {
            // Log the error for debugging purposes
            Log::error('Database error while creating customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to create customer due to a database error.'
            ], 500);

        } catch (\Exception $e) {
            // Log general errors that aren’t database-related
            Log::error('Error while creating customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while creating the customer.'
            ], 500);
        }
    }

    /**
     * Display the specified customer by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $customer = Customer::find($id);

            // Check if the customer exists
            if (!$customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            return response()->json($customer);

        } catch (QueryException $e) {
            // Handle database-specific errors
            Log::error('Database error while retrieving customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to retrieve customer due to a database error.'
            ], 500);

        } catch (\Exception $e) {
            // Handle general errors
            Log::error('Error while retrieving customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while retrieving the customer.'
            ], 500);
        }
    }

    /**
     * Update the specified customer by ID.
     */
    public function update(UpdateCustomerRequest $request, string $id): JsonResponse
    {
        try {
            $customer = Customer::find($id);

            // Check if the customer exists
            if (!$customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            // Update customer data
            $customer->update($request->all());

            return response()->json($customer);

        } catch (QueryException $e) {
            // Handle database-specific errors
            Log::error('Database error while updating customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to update customer due to a database error.',
                'message' => $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            // Handle general errors
            Log::error('Error while updating customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while updating the customer.'
            ], 500);
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $customer = Customer::find($id);

            // Check if the customer exists
            if (!$customer) {
                return response()->json(['error' => 'Customer not found'], 404);
            }

            // Attempt to delete the customer
            $customer->delete();

            return response()->json(['message' => 'Customer deleted successfully'], 200);

        } catch (QueryException $e) {
            // Handle database-specific errors
            Log::error('Database error while deleting customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to delete customer due to a database error.'
            ], 500);

        } catch (\Exception $e) {
            // Handle general errors
            Log::error('Error while deleting customer: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred while deleting the customer.'
            ], 500);
        }
    }
}
