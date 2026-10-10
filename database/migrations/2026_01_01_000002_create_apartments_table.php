<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()->constrained('buildings')->nullOnDelete();
            $table->string('code');
            $table->string('label')->nullable();
            $table->string('address')->nullable();
            $table->decimal('base_rent', 12, 2)->default(0);
            $table->string('status')->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['landlord_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
