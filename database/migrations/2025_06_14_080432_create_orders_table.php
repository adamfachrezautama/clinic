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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->string('service');
            $table->integer('price');
            $table->string('payment_url')->nullable();
            $table->string('status')->default('waiting'); // waiting, paid, cancel
            $table->integer('duration'); // in minutes
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->dateTime('start_time'); // date and time of the appointment
            $table->dateTime('end_time')->nullable(); // end time of the appointment
            $table->string('status_service')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
