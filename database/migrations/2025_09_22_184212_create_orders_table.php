<?php // create_orders_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if (Schema::hasTable('orders')) { return; } Schema::create('orders', function (Blueprint $t) {
  $t->id(); $t->foreignId('user_id')->constrained('users'); $t->dateTime('order_date');
  $t->string('status'); $t->decimal('subtotal_amount',12,2); $t->decimal('discount_amount',12,2)->default(0);
  $t->decimal('total_amount',12,2); $t->softDeletes(); $t->timestamps();
  $t->index(['user_id','status']); }); }
  public function down(): void { Schema::dropIfExists('orders'); } };
