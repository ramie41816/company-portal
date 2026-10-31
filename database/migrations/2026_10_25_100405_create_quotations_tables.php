<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->nullable()->unique();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->string('reference_project')->nullable();
            $table->string('prepared_by')->nullable();

            // Other information / rich text notes (Quill editor content)
            $table->text('other_info')->nullable();

            // Approval Details Remarks
            $table->text('remarks')->nullable();
            $table->string('status_modified_by')->nullable();
            $table->date('status_modified_at')->nullable();
            $table->string('status')->default('draft');

            // Client details matching the template
            $table->string('quoted_to');                  // Client Name
            $table->string('client_company')->nullable(); // Company / Business Name
            $table->string('client_address')->nullable(); // Address
            $table->string('client_phone')->nullable();   // Phone Number
            $table->string('attention')->nullable();      // Contact Person
            $table->string('contact_email')->nullable();  // Email Address

            $table->decimal('total', 12, 2)->default(0);

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();

            // Self-referencing parent_id for sub-items support
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('quotation_items')->cascadeOnDelete();

            $table->string('description');                      // Product / Service description or Section Header
            $table->decimal('quantity', 10, 2)->nullable();     // Quantity (nullable if main item acts as a header)
            $table->decimal('unit_price', 12, 2)->nullable();   // Unit Price (nullable if main item acts as a header)
            $table->decimal('total', 12, 2)->default(0);        // Line Total
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
