<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateway_logs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_type')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('invoice_key')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_key')->nullable();
            $table->unsignedBigInteger('deposit_id')->nullable();
            $table->string('trx')->nullable();
            $table->tinyInteger('local_status_before')->nullable();
            $table->tinyInteger('local_status_after')->nullable();
            $table->string('decision')->nullable();
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'invoice_id']);
            $table->index('deposit_id');
            $table->index('trx');
            $table->index('decision');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_logs');
    }
};
