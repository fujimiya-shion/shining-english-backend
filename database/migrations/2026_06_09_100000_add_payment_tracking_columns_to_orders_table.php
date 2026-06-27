<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->text('payment_checkout_url')->nullable()->after('payment_reference');
            $table->json('payment_metadata')->nullable()->after('payment_checkout_url');
            $table->timestamp('paid_at')->nullable()->after('payment_metadata');

            $table->index('payment_reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_reference']);
            $table->dropColumn([
                'payment_reference',
                'payment_checkout_url',
                'payment_metadata',
                'paid_at',
            ]);
        });
    }
};
