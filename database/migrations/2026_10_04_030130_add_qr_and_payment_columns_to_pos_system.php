<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modifikasi tabel dining_tables
        Schema::table('dining_tables', function (Blueprint $table) {
            if (!Schema::hasColumn('dining_tables', 'qr_token')) {
                $table->string('qr_token', 64)->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('dining_tables', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('qr_token');
            }
        });

        // Generate token awal untuk meja yang sudah ada
        $existingTables = DB::table('dining_tables')->whereNull('qr_token')->get();
        foreach ($existingTables as $table) {
            DB::table('dining_tables')->where('id', $table->id)->update([
                'qr_token' => Str::random(32),
                'is_active' => true
            ]);
        }

        // 2. Modifikasi tabel transactions
        // Buat user_id nullable untuk order QR
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN user_id BIGINT UNSIGNED NULL");
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'order_source')) {
                $table->enum('order_source', ['pos', 'qr'])->default('pos')->after('table_number');
            }
            if (!Schema::hasColumn('transactions', 'order_status')) {
                $table->enum('order_status', [
                    'pending_payment', 'paid', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled', 'void'
                ])->default('completed')->after('status');
            }
            if (!Schema::hasColumn('transactions', 'payment_status')) {
                $table->enum('payment_status', [
                    'pending', 'paid', 'failed', 'expired', 'cancelled', 'refunded'
                ])->default('paid')->after('order_status');
            }
            if (!Schema::hasColumn('transactions', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('transactions', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_gateway');
            }
            if (!Schema::hasColumn('transactions', 'external_payment_id')) {
                $table->string('external_payment_id')->nullable()->index()->after('payment_reference');
            }
        });

        // Set status order awal untuk transaksi lama agar konsisten
        DB::table('transactions')->where('status', 'paid')->update([
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'order_source' => 'pos'
        ]);
        DB::table('transactions')->where('status', 'void')->update([
            'order_status' => 'void',
            'payment_status' => 'cancelled',
            'order_source' => 'pos'
        ]);
        DB::table('transactions')->where('status', 'unpaid')->update([
            'order_status' => 'confirmed',
            'payment_status' => 'pending',
            'order_source' => 'pos'
        ]);

        // 3. Modifikasi tabel products untuk foto dan deskripsi menu
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('image');
            }
        });

        // 4. Tabel order_sessions (Sesi Meja Aktif untuk Self-Order Meja)
        if (!Schema::hasTable('order_sessions')) {
            Schema::create('order_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dining_table_id')->constrained('dining_tables')->cascadeOnDelete();
                $table->string('session_token', 64)->unique();
                $table->string('customer_name')->nullable();
                $table->enum('status', ['active', 'closed'])->default('active');
                $table->timestamps();
            });
        }

        // 5. Tabel payments (Log Transaksi Payment Gateway Idempoten)
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
                $table->string('payment_gateway');
                $table->string('payment_method')->nullable();
                $table->string('external_id')->nullable()->index();
                $table->decimal('amount', 10, 2);
                $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'cancelled', 'refunded'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('expired_at')->nullable();
                $table->longText('raw_response')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_sessions');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image', 'description']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'order_source',
                'order_status',
                'payment_status',
                'payment_gateway',
                'payment_reference',
                'external_payment_id'
            ]);
        });

        Schema::table('dining_tables', function (Blueprint $table) {
            $table->dropColumn(['qr_token', 'is_active']);
        });
    }
};
