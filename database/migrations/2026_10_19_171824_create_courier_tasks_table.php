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
        Schema::create('courier_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('task_type', ['JEMPUT', 'ANTAR'])->default('JEMPUT');
            $table->enum('status', ['MENUNGGU', 'DIPROSES', 'SELESAI', 'BATAL'])->default('MENUNGGU');
            $table->string('customer_name');
            $table->string('customer_phone', 30);
            $table->text('address');
            $table->string('maps_url', 500)->nullable();
            $table->date('scheduled_date');
            $table->enum('time_slot', ['PAGI', 'SIANG', 'SORE', 'MALAM'])->default('PAGI');
            $table->unsignedInteger('delivery_fee')->default(0);
            $table->decimal('estimated_kg', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_tasks');
    }
};
