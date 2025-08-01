<?php

use App\Enums\StatusEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedBigInteger('amount');
            $table->string('reference')->unique();
            $table->string('description')->nullable();
            $table->enum('transaction_type', TransactionTypeEnum::values())->index();
            $table->enum('status', StatusEnum::values())->default(StatusEnum::PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
