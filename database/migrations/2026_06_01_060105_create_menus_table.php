<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->unique(); // header, footer-col1, footer-col2, footer-col3, mobile
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('menus'); }
};