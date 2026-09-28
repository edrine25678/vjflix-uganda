<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Subscription Plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price_ugx');
            $table->string('interval_unit')->default('month'); // day, week, month, year
            $table->unsignedInteger('interval_count')->default(1);
            $table->unsignedInteger('duration_days')->default(30);
            $table->json('features')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // active, pending, expired, cancelled
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->string('payment_method')->nullable(); // mtn_momo, airtel_money, card, voucher
            $table->string('external_reference')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'expires_at']);
        });

        // Payments & Transactions
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_reference')->unique();
            $table->string('external_reference')->nullable();
            $table->string('network'); // mtn, airtel, card, voucher
            $table->string('phone_number')->nullable();
            $table->unsignedInteger('amount');
            $table->string('currency')->default('UGX');
            $table->string('status')->default('pending'); // pending, completed, failed, cancelled
            $table->string('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Content premium flags
        Schema::table('movies', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('status');
        });

        Schema::table('series', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });

        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });

        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
