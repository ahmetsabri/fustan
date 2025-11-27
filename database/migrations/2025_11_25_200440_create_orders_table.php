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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_service_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tailor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->date('delivery_date')->nullable();
            
            // Measurements fields
            $table->decimal('length', 8, 2)->nullable()->comment('الطول');
            $table->decimal('shoulder', 8, 2)->nullable()->comment('الكتف');
            $table->decimal('chest', 8, 2)->nullable()->comment('الصدر');
            $table->decimal('waist', 8, 2)->nullable()->comment('الخصر');
            $table->decimal('hip', 8, 2)->nullable()->comment('الورك');
            $table->decimal('sleeve', 8, 2)->nullable()->comment('الكُم');
            $table->text('measurement_notes')->nullable()->comment('ملاحظات_المقاسات');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
