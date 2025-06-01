<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Payment status fields
            $table->string('payment_status')->default('pending')->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->decimal('total_amount', 10, 2)->after('payment_reference');
            $table->timestamp('paid_at')->nullable()->after('total_amount');
            
            // Refund fields
            $table->decimal('refund_amount', 10, 2)->nullable()->after('paid_at');
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            $table->string('refund_reason')->nullable()->after('refunded_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_id')->nullable()->after('remember_token');
            $table->string('pm_type')->nullable()->after('stripe_id');
            $table->string('pm_last_four', 4)->nullable()->after('pm_type');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'payment_reference',
                'total_amount',
                'paid_at',
                'refund_amount',
                'refunded_at',
                'refund_reason'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_id', 'pm_type', 'pm_last_four']);
        });
    }
};