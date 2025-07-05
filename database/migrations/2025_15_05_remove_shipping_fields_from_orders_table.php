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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_address');
            $table->dropColumn('shipping_city');
            $table->dropColumn('shipping_postal_code');
            $table->dropColumn('shipping_phone');
            $table->dropColumn('shipping_notes');
            $table->dropColumn('shipping_status');
            $table->dropColumn('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->text('shipping_notes')->nullable();
            $table->string('shipping_status')->default('pending');
            $table->decimal('shipping_cost', 10, 2)->default(0);
        });
    }
};
