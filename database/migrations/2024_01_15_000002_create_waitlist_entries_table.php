<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('waitlist_entries')) { return; }
        Schema::create('waitlist_entries', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users');
            $t->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $t->string('status');
            $t->timestamp('notified_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist_entries');
    }
};
