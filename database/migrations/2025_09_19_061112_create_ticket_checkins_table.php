<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('ticket_checkins', function (Blueprint $t) {
      $t->id();
      $t->foreignId('ticket_id')->constrained()->cascadeOnDelete();
      $t->foreignId('scanned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
      $t->timestamp('scanned_at');
      $t->string('device')->nullable();
      $t->string('location')->nullable();
      $t->timestamps();
      $t->index(['ticket_id', 'scanned_at']);
    });
  }
  public function down(): void { Schema::dropIfExists('ticket_checkins'); }
};