<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('role')->default('patient');
            $table->string('google_id')->nullable();
            $table->string('ktp_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('not_active');
            $table->string('certification')->nullable();
            $table->integer('telemedicine_fee')->default(0);
            $table->string('photo')->nullable();
            $table->integer('chat_fee')->default(0);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->foreignId('clinic_id')
                ->nullable()
                ->constrained('clinics')
                ->nullOnDelete();
            $table->foreignId('specialization_id')
                ->nullable()
                ->constrained('specializations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
