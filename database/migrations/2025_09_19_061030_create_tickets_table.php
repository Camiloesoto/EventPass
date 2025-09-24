<?php // create_tickets_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { if (Schema::hasTable('tickets')) { return; } Schema::create('tickets', function (Blueprint $t) {
  $t->id(); $t->unsignedBigInteger('order_item_id');
  $t->foreignId('user_id')->constrained('users');
  $t->string('qr_code_hash'); $t->string('pdf_url'); $t->string('status');
  $t->dateTime('redeemed_at')->nullable(); $t->softDeletes(); $t->timestamps();
  $t->index(['user_id','status']); }); }
  public function down(): void { Schema::dropIfExists('tickets'); } };
