<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->string('billing_cycle')->default('monthly'); // 'monthly' ou 'yearly'
            $table->string('subscription_status')->default('active'); // 'active', 'trialing', 'past_due', 'suspended', 'canceled'
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn([
                'subscription_plan_id',
                'billing_cycle',
                'subscription_status',
                'subscription_ends_at',
                'trial_ends_at',
            ]);
        });
    }
};
