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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('grade')->default('A'); // A, B, C
            $table->unsignedBigInteger('price'); // in paise (₹ × 100)
            $table->unsignedBigInteger('mrp')->nullable();
            $table->string('stock_status')->default('in_stock'); // in_stock, low_stock, out_of_stock
            $table->unsignedInteger('stock_qty')->default(0);
            $table->unsignedInteger('warranty_months')->default(6);
            $table->json('specs')->nullable(); // flexible key-value specs
            $table->json('quick_specs')->nullable(); // short chip display (e.g. ["i5 8th Gen","8GB"])
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
