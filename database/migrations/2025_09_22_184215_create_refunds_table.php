<?php // create_refunds_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if (Schema::hasTable('refunds')) { return; } Schema::create('refunds', function (Blueprint $t) {
  $t->id(); $t->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
  $t->string('provider_refund_id'); $t->decimal('amount',12,2); $t->string('status'); $t->dateTime('refunded_at')->nullable(); $t->timestamps(); }); }
  public function down(): void { Schema::dropIfExists('refunds'); } };
