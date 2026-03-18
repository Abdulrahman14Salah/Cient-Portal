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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            // Contact
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Personal
            $table->integer('adults');
            $table->integer('kids')->default(0);
            $table->string('nationality');
            $table->string('country');
            $table->string('city');

            // Work
            $table->string('employment');
            $table->boolean('remote');
            $table->decimal('income', 10, 2);

            // Other
            $table->date('move_date')->nullable();
            $table->string('referral')->nullable();
            $table->text('notes')->nullable();

            // Pricing
            $table->decimal('total_price', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
