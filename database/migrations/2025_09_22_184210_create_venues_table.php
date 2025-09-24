<?php // create_venues_table
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
  if (Schema::hasTable('venues')) { return; }
  Schema::create('venues', function (Blueprint $t) {
  $t->id(); $t->string('name'); $t->string('address'); $t->string('timezone', 64); $t->timestamps(); }); }
  public function down(): void { Schema::dropIfExists('venues'); } };
