<?php // create_ticket_checkins_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if (Schema::hasTable('ticket_checkins')) { return; } Schema::create('ticket_checkins', function (Blueprint $t) {
  $t->id(); $t->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
  $t->foreignId('scanned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
  $t->dateTime('scanned_at'); $t->string('device')->nullable(); $t->string('location')->nullable(); $t->timestamps(); }); }
  public function down(): void { Schema::dropIfExists('ticket_checkins'); } };
