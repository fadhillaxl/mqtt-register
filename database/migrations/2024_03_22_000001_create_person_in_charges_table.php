<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_in_charges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add PIC relationship to devices table
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('person_in_charge_id')
                  ->nullable()
                  ->constrained('person_in_charges')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['person_in_charge_id']);
            $table->dropColumn('person_in_charge_id');
        });
        Schema::dropIfExists('person_in_charges');
    }
}; 