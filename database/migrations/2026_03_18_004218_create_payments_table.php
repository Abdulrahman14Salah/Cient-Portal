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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('case_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->integer('stage'); // 1,2,3
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('usd');

            $table->string('status')->default('pending'); // pending, paid, failed

            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_client_secret')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
