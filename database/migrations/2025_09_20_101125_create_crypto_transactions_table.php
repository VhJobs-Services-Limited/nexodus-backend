<?php

declare(strict_types=1);

use App\Enums\PaymentMethodEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crypto_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->string('reference')->unique();
            $table->foreignId('transfer_transaction_id')->nullable()->constrained('transactions');
            $table->enum('status', StatusEnum::values())->default(StatusEnum::PENDING);
            $table->string('currency', 100);
            $table->string('amount', 100);
            $table->string('payment_reference')->nullable();
            $table->enum('payment_method', PaymentMethodEnum::values())->nullable();
            $table->string('provider_reference')->unique()->nullable();
            $table->string('provider_name')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_transactions');
    }
};
