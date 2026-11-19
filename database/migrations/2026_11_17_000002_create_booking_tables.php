<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Booking requests for tour packages. The package name and prices are copied in, so later edits never change a booking. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('package_name');
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('nationality', 20);                // indian, nepali, other
            $table->string('travelling_from')->nullable();
            $table->date('travel_date')->nullable();
            $table->unsignedSmallInteger('adults')->default(1);
            $table->unsignedSmallInteger('children')->default(0);
            $table->char('currency', 3);                      // INR or NPR
            $table->decimal('price_per_person', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();       // estimate; confirmed by the team
            $table->decimal('advance', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->string('payment_status', 20)->default('pending')->index();
            $table->string('payment_method', 20)->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
