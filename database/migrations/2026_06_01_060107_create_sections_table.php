<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('page')->default('home'); // which page this section belongs to
            $table->string('section_key')->unique(); // hero, categories, deals, grades, trust, eco, b2b, newsletter
            $table->string('label'); // human-readable name
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('content')->nullable(); // editable content fields
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sections'); }
};