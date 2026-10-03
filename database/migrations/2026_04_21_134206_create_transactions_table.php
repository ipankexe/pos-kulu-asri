<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('customer_name');
            $table->string('table_number');
            $table->decimal('total', 10, 2);
            $table->decimal('payment', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->enum('status', ['paid', 'void', 'unpaid'])->default('unpaid');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
