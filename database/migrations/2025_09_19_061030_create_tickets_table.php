<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('tickets', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // owner
      $t->string('code')->unique();          // public identifier (ULID)
      $t->string('qr_hash')->unique();       // secure scanner payload
      $t->string('status');                  // see Ticket::STATUS_*
      $t->timestamp('redeemed_at')->nullable();
      $t->timestamp('revoked_at')->nullable();
      $t->json('meta')->nullable();          // optional: store event_id, seat, price snapshot
      $t->timestamps();

      $t->index(['user_id', 'status']);
    });
  }
  public function down(): void { Schema::dropIfExists('tickets'); }
};
