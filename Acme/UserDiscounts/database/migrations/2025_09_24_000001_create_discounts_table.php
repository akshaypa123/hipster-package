<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('percentage', 5, 2);
            $table->boolean('active')->default(true);
            $table->dateTime('expires_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('discounts');
    }
};
