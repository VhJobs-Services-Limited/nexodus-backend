<?php

declare(strict_types=1);

use App\Enums\BillEnum;
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
        Schema::create('bill_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('transaction_id')->constrained('transactions');
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedBigInteger('provider_amount')->default(0);
            $table->string('reference')->unique();
            $table->string('provider_reference')->unique()->nullable();
            $table->string('provider_name')->nullable();
            $table->enum('type', BillEnum::values());
            $table->enum('status', StatusEnum::values())->default(StatusEnum::PENDING);
            $table->json('payload');
            $table->timestamp('last_retried_at')->nullable();
            $table->timestamps();

            $table->index('last_retried_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_transactions');
    }
};
