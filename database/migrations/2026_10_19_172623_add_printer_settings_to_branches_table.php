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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('printer_type', 20)->default('SYSTEM')->after('receipt_footer');
            $table->string('printer_paper_size', 10)->default('58mm')->after('printer_type');
            $table->boolean('printer_auto_print')->default(false)->after('printer_paper_size');
            $table->string('printer_device_name')->nullable()->after('printer_auto_print');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['printer_type', 'printer_paper_size', 'printer_auto_print', 'printer_device_name']);
        });
    }
};
