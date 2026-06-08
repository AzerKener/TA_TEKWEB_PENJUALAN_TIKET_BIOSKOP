<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 30)->unique(); // TRX-20250108-XXXXX
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('schedule_id')->constrained()->onDelete('restrict');
            $table->decimal('subtotal_ticket', 12, 2)->default(0);
            $table->decimal('subtotal_fnb', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0); // 10%
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_method', [
                'transfer_bank', 'gopay', 'ovo', 'dana', 'shopee_pay',
                'credit_card', 'debit_card', 'cash', 'qris'
            ])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'refunded'])->default('pending');
            $table->string('payment_proof')->nullable(); // bukti transfer
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // batas waktu pembayaran (15 menit)
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('payment_status');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
