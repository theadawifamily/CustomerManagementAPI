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
        $response = $this->postJson('/api/v1/customers', [
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

        $response = $this->getJson("/api/v1/customers/{$customer->id}");

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

        $response = $this->getJson('/api/v1/customers?name=Jane Doe');

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

        $response = $this->getJson('/api/v1/customers?email=test_customer@yahoo.com');

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
        $response = $this->getJson('/api/v1/customers?name=Alice Smith&email=nonexistent@example.com');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
            ]);

        // If both are incorrect, no match should be found
        $response = $this->getJson('/api/v1/customers?name=Nonexistent&email=nonexistent@example.com');

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
        $response = $this->getJson('/api/v1/customers?name=Alice Smith&email=nonexistent@example.com');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
            ]);

        // Scenario: Email exists, name does not exist
        $response = $this->getJson('/api/v1/customers?name=Nonexistent Name&email=bob@example.com');

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

        $response = $this->putJson("/api/v1/customers/{$customer->id}", [
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

        $response = $this->deleteJson("/api/v1/customers/{$customer->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Customer deleted successfully']);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    //Email test section:

    /**
     * Test that email is required.
     */
    public function test_email_is_required()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Missing Email User',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that email must be a string.
     */
    public function test_email_must_be_a_string()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Non-string Email User',
            'email' => 12345, // Non-string value
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that email must follow valid format.
     */
    public function test_email_format_validation()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Invalid Format User',
            'email' => 'invalid-email-format',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that email does not exceed maximum length.
     */
    public function test_email_max_length_validation()
    {
        $longEmail = str_repeat('a', 246) . '@example.com'; // 256 characters
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Long Email User',
            'email' => $longEmail,
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that email must be unique.
     */
    public function test_email_must_be_unique()
    {
        Customer::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Duplicate Email User',
            'email' => 'duplicate@example.com', // Already exists in database
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that a valid email passes all validation checks.
     */
    public function test_valid_email_passes_validation()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Valid Email User',
            'email' => 'valid.email@example.com',
            'annualSpend' => 1000.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Valid Email User',
                'email' => 'valid.email@example.com',
            ]);
    }

    /**
     * Test updating a customer with the same email (unique validation should ignore the current record).
     */
    public function test_update_customer_with_same_email()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'name' => 'Existing Customer',
            'email' => 'existing.email@example.com',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        // Attempt to update the customer without changing the email
        $response = $this->putJson("/api/v1/customers/{$customer->id}", [
            'name' => 'Updated Customer',
            'email' => 'existing.email@example.com', // Same email as before
            'annualSpend' => 1000.00,
            'lastPurchaseDate' => '2024-06-01T00:00:00Z',
        ]);

        // Assert that the update is successful
        $response->assertStatus(200)
            ->assertJson([
                'id' => $customer->id,
                'name' => 'Updated Customer',
                'email' => 'existing.email@example.com',
                'annualSpend' => 1000.00,
                'lastPurchaseDate' => '2024-06-01T00:00:00Z',
            ]);
    }

    // Name test section:

    /**
     * Test that name is required.
     */
    public function test_name_is_required()
    {
        $response = $this->postJson('/api/v1/customers', [
            'email' => 'valid.email@example.com',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that name must be a string.
     */
    public function test_name_must_be_a_string()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 12345, // Non-string value
            'email' => 'valid.email@example.com',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that name does not exceed maximum length.
     */
    public function test_name_max_length_validation()
    {
        $longName = str_repeat('a', 256); // 256 characters
        $response = $this->postJson('/api/v1/customers', [
            'name' => $longName,
            'email' => 'valid.email@example.com',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test that a valid name passes all validation checks.
     */
    public function test_valid_name_passes_validation()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Valid Name User',
            'email' => 'valid.email@example.com',
            'annualSpend' => 1000.00,
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Valid Name User',
                'email' => 'valid.email@example.com',
            ]);
    }

    // annualSpend test section:

    /**
     * Test that annualSpend can be null.
     */
    public function test_annualSpend_can_be_null()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Null Annual Spend User',
            'email' => 'null.annualspend@example.com',
            'annualSpend' => null, // Null value
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['annualSpend' => null]);
    }

    /**
     * Test that annualSpend must be numeric.
     */
    public function test_annualSpend_must_be_numeric()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Non-numeric Annual Spend User',
            'email' => 'non.numeric@example.com',
            'annualSpend' => 'not-a-number', // Invalid non-numeric value
            'lastPurchaseDate' => '2024-01-01T00:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['annualSpend']);
    }

    // lastPurchaseDate test section:

    /**
     * Test that lastPurchaseDate can be null.
     */
    public function test_lastPurchaseDate_can_be_null()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Null Last Purchase Date User',
            'email' => 'null.lastpurchase@example.com',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => null, // Null value
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['lastPurchaseDate' => null]);
    }

    /**
     * Test that lastPurchaseDate must be a valid date.
     */
    public function test_lastPurchaseDate_must_be_a_valid_date()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Invalid Date User',
            'email' => 'invalid.date@example.com',
            'annualSpend' => 500.00,
            'lastPurchaseDate' => 'invalid-date', // Invalid date format
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lastPurchaseDate']);
    }

    /**
     * Test that valid annualSpend and lastPurchaseDate pass validation.
     */
    public function test_valid_annualSpend_and_lastPurchaseDate_pass_validation()
    {
        $response = $this->postJson('/api/v1/customers', [
            'name' => 'Valid Data User',
            'email' => 'valid.data@example.com',
            'annualSpend' => 1000.00,
            'lastPurchaseDate' => '2024-06-01T00:00:00Z', // Valid date
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Valid Data User',
                'email' => 'valid.data@example.com',
                'annualSpend' => 1000.00,
                'lastPurchaseDate' => '2024-06-01T00:00:00Z',
            ]);
    }
}
