<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom include_wash dan include_iron ke tabel orders.
 *
 * Dengan kedua flag ini, kasir bisa membuat order fleksibel:
 *  - Cuci + Setrika (default, keduanya true)
 *  - Cuci saja        (include_wash=true,  include_iron=false)
 *  - Setrika saja     (include_wash=false, include_iron=true)
 *
 * Juga tambah default flags ke service_packages agar paket
 * tertentu (misal "Setrika Saja") bisa pre-fill form kasir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('include_wash')->default(true)->after('perfume_variant')
                  ->comment('true = order termasuk proses cuci');
            $table->boolean('include_iron')->default(true)->after('include_wash')
                  ->comment('true = order termasuk proses setrika');
        });

        Schema::table('service_packages', function (Blueprint $table) {
            $table->boolean('default_include_wash')->default(true)->after('perfume_ml_per_unit')
                  ->comment('Default include_wash saat paket ini dipilih di POS');
            $table->boolean('default_include_iron')->default(true)->after('default_include_wash')
                  ->comment('Default include_iron saat paket ini dipilih di POS');
        });
    }

    public function down(): void
    {
        Schema::table('service_packages', function (Blueprint $table) {
            $table->dropColumn(['default_include_wash', 'default_include_iron']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['include_wash', 'include_iron']);
        });
    }
};
