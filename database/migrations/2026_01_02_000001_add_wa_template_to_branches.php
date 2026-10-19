<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('operational_hours')->nullable()->after('address');
            $table->text('wa_template_ready')->nullable()->after('receipt_footer');
            $table->text('wa_template_done')->nullable()->after('wa_template_ready');
            $table->text('wa_template_promo')->nullable()->after('wa_template_done');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['operational_hours', 'wa_template_ready', 'wa_template_done', 'wa_template_promo']);
        });
    }
};
