<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) { return; }
        Schema::create('events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $t->string('name');
            $t->text('description');
            $t->dateTime('start_time');
            $t->dateTime('end_time');
            $t->unsignedInteger('capacity');
            $t->string('status');
            $t->softDeletes();
            $t->timestamps();
            $t->index(['start_time', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
