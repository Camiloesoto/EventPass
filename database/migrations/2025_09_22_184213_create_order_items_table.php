<?php // create_order_items_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if (Schema::hasTable('order_items')) { return; } Schema::create('order_items', function (Blueprint $t) {
  $t->id(); $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
  $t->foreignId('ticket_type_id')->constrained('ticket_types');
  $t->unsignedInteger('quantity'); $t->decimal('unit_price',12,2); $t->timestamps(); }); }
  public function down(): void { Schema::dropIfExists('order_items'); } };
