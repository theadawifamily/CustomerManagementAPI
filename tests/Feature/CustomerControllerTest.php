<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Customer;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new customer.
     */
    public function test_create_customer()
    {
        $response = $this->postJson('/api/customers', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'annualSpend' => 1000.50,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'name', 'email', 'annualSpend', 'lastPurchaseDate', 'created_at', 'updated_at'
            ]);
    }

    /**
     * Test retrieving a customer by ID.
     */
    public function test_retrieve_customer_by_id()
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/api/customers/{$customer->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'annualSpend' => $customer->annualSpend,
                'lastPurchaseDate' => $customer->lastPurchaseDate,
            ]);
    }

    /**
     * Test retrieving a customer by name.
     */
    public function test_retrieve_customer_by_name()
    {
        Customer::factory()->create(['name' => 'Jane Doe']);

        $response = $this->getJson('/api/customers?name=Jane Doe');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Jane Doe',
            ]);
    }

    /**
     * Test retrieving a customer by email.
     */
    public function test_retrieve_customer_by_email()
    {
        Customer::factory()->create(['email' => 'test_customer@yahoo.com']);

        $response = $this->getJson('/api/customers?email=test_customer@yahoo.com');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'test_customer@yahoo.com',
            ]);
    }

    /**
     * Test updating a customer.
     */
    public function test_update_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->putJson("/api/customers/{$customer->id}", [
            'name' => 'John Smith',
            'email' => 'johnsmith@example.com',
            'annualSpend' => 2000.75,
            'lastPurchaseDate' => '2024-06-01T00:00:00Z',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $customer->id,
                'name' => 'John Smith',
                'email' => 'johnsmith@example.com',
                'annualSpend' => 2000.75,
                'lastPurchaseDate' => '2024-06-01T00:00:00Z',
            ]);
    }

    /**
     * Test deleting a customer.
     */
    public function test_delete_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson("/api/customers/{$customer->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Customer deleted successfully']);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
