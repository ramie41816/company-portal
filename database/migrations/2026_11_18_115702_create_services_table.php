<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon_class')->default('bi-tools');
            $table->text('short_description');
            $table->text('full_description');
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
