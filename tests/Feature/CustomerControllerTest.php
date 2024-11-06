<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Customer;

/**
 * @group crud
 */
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
     * Test retrieving a customer by either email or name.
     */
    public function test_retrieve_customer_by_email_or_name()
    {
        // Create two customers with different names and emails
        Customer::factory()->create(['name' => 'Alice Smith', 'email' => 'alice@example.com']);
        Customer::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

        // Try retrieving with both name and email (should match one if OR logic is applied)
        $response = $this->getJson('/api/customers?name=Alice Smith&email=nonexistent@example.com');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
            ]);

        // If both are incorrect, no match should be found
        $response = $this->getJson('/api/customers?name=Nonexistent&email=nonexistent@example.com');

        $response->assertStatus(200)
            ->assertJsonMissing([
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
            ])
            ->assertJsonMissing([
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
            ]);

        // Scenario: Name exists and email does not exist
        $response = $this->getJson('/api/customers?name=Alice Smith&email=nonexistent@example.com');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
            ]);

        // Scenario: Email exists, name does not exist
        $response = $this->getJson('/api/customers?name=Nonexistent Name&email=bob@example.com');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
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
