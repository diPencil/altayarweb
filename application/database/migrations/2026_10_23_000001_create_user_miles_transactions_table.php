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
        Schema::create('user_miles_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('trx', 40)->unique();

            // Core accounting semantics
            $table->enum('type', ['credit', 'debit']);
            $table->tinyInteger('direction'); // +1 (adds) or -1 (subtracts)
            $table->unsignedInteger('miles'); // Strict integer units, non-negative
            $table->unsignedInteger('balance_after'); // Authoritative snapshot after this transaction

            // Classification & Reference
            $table->string('source', 50)->default('manual');
            $table->string('reference_type', 50)->nullable();
            $table->string('reference_id', 100)->nullable();
            $table->string('remark', 255);

            // Audit & Reversals
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->boolean('is_reversal')->default(false);
            $table->unsignedBigInteger('reversal_of_id')->nullable();
            $table->boolean('is_reversed')->default(false);
            $table->timestamp('reversed_at')->nullable();

            // Concurrency & Idempotency
            $table->string('idempotency_key', 64)->nullable()->unique();
            $table->json('meta')->nullable();

            $table->timestamps();

            // Foreign Key Constraints:
            // RESTRICT prevents cascading deletion of mileage history with users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('admin_id')->references('id')->on('admins')->nullOnDelete();
            $table->foreign('agent_id')->references('id')->on('agents')->nullOnDelete();
            $table->foreign('reversal_of_id')->references('id')->on('user_miles_transactions')->nullOnDelete();

            // Indexes for fast lookup & aggregation
            $table->index(['user_id', 'created_at'], 'idx_umt_user_created');
            $table->index(['user_id', 'type', 'is_reversal'], 'idx_umt_user_metrics');
            $table->index(['source', 'reference_id'], 'idx_umt_source_ref');
            $table->index('admin_id', 'idx_umt_admin');
            $table->index('agent_id', 'idx_umt_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_miles_transactions');
    }
};
