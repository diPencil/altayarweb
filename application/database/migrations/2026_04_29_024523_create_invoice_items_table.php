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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->integer('qty')->default(1);
            $table->integer('guests')->default(0);
            $table->decimal('unit_price', 28, 8)->default(0);
            $table->decimal('total', 28, 8)->default(0);            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
