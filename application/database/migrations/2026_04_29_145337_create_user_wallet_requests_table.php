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
        Schema::create('user_wallet_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->decimal('amount', 28, 8)->default(0);
            $table->string('type')->comment('use: تخصيص/استخدام, refund: استرجاع');
            $table->text('details')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Approved, 2: Rejected');
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallet_requests');
    }
};
