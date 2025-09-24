<?php // create_payments_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if (Schema::hasTable('payments')) { return; } Schema::create('payments', function (Blueprint $t) {
  $t->id(); $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
  $t->string('provider'); $t->string('provider_payment_id'); $t->decimal('amount',12,2); $t->string('currency',3);
  $t->string('status'); $t->dateTime('paid_at')->nullable(); $t->timestamps();
  $t->index(['order_id','status']); }); }
  public function down(): void { Schema::dropIfExists('payments'); } };
