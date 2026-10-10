<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->unsignedBigInteger('number');
            $table->decimal('total', 12, 2);
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->unique(['landlord_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
