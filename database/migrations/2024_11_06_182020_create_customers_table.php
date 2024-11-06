<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID for ID, compatible with SQLite as text
            $table->string('name');         // String for name
            $table->string('email')->unique(); // Email field, with a unique constraint
            $table->decimal('annualSpend', 8, 2)->nullable(); // Decimal for spending
            $table->dateTime('lastPurchaseDate')->nullable(); // Datetime for last purchase

            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
