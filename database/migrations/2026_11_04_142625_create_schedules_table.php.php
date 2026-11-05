<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // Optional link if created from an approved quotation
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();

            // Schedule identifiers & metadata
            $table->string('schedule_number')->unique();
            $table->dateTime('schedule_date');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('reference_project')->nullable();
            $table->string('prepared_by')->nullable();

            // Client details
            $table->string('scheduled_to');
            $table->string('client_company')->nullable();
            $table->text('client_address')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('attention')->nullable();

            // Status, remarks, and audit trail
            $table->string('status')->default('scheduled'); // e.g., scheduled, ongoing, completed, cancelled
            $table->text('remarks')->nullable();
            $table->string('status_modified_by')->nullable();
            $table->timestamp('status_modified_at')->nullable();
            $table->string('created_by')->nullable();

            // Financials
            $table->decimal('total', 15, 2)->default(0);

            // Rich text notes or terms
            $table->longText('other_info')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
