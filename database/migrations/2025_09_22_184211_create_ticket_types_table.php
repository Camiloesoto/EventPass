<?php // create_ticket_types_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if (Schema::hasTable('ticket_types')) { return; } Schema::create('ticket_types', function (Blueprint $t) {
  $t->id(); $t->foreignId('event_id')->constrained('events')->cascadeOnDelete();
  $t->string('name'); $t->decimal('price', 12, 2); $t->unsignedInteger('quantity');
  $t->softDeletes(); $t->timestamps(); }); }
  public function down(): void { Schema::dropIfExists('ticket_types'); } };
