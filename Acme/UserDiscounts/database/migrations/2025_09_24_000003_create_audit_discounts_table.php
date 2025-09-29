<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
  Schema::create('audit_discount', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
    $table->decimal('applied_value',8,2);
    $table->string('context')->nullable();
    $table->timestamps();
});


    }

    public function down() {
        Schema::dropIfExists('audit_discounts');
    }
};