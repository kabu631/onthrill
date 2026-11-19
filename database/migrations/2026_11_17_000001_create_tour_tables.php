<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Destinations (temples and sacred places), tour types and tour packages.
 * A package visits one or more destinations.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('country', 40)->index();          // Nepal, India, Tibet (China)
            $table->string('region')->nullable();             // e.g. Kathmandu, Bagmati Province
            $table->text('summary')->nullable();              // one or two plain sentences
            $table->longText('description')->nullable();      // rich text
            // Key facts, shown as a table and in structured data.
            $table->unsignedInteger('altitude_m')->nullable();
            $table->string('best_time')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('nearest_airport')->nullable();
            $table->text('how_to_reach')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('faqs')->nullable();                 // [{question, answer}]
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('image_credit')->nullable();
            $table->string('image_credit_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tour_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('show_in_menu')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('duration_days')->default(1);
            $table->unsignedSmallInteger('duration_nights')->default(0);
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->text('summary')->nullable();
            $table->longText('overview')->nullable();
            $table->json('highlights')->nullable();           // [string]
            $table->json('itinerary')->nullable();            // [{title, description}], one per day
            $table->json('inclusions')->nullable();           // [string]
            $table->json('exclusions')->nullable();           // [string]
            $table->longText('notes')->nullable();            // documents, permits, health
            $table->json('faqs')->nullable();                 // [{question, answer}]
            // Price per person. NPR is worked out from INR (Site Settings) unless set.
            $table->decimal('price_inr', 12, 2)->nullable();
            $table->decimal('sale_price_inr', 12, 2)->nullable();
            $table->decimal('price_npr', 12, 2)->nullable();
            $table->decimal('sale_price_npr', 12, 2)->nullable();
            $table->string('best_season')->nullable();
            $table->string('group_size')->nullable();
            $table->string('difficulty', 20)->nullable();
            $table->unsignedInteger('max_altitude_m')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('destination_package', function (Blueprint $table) {
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['destination_id', 'package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_package');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('tour_categories');
        Schema::dropIfExists('destinations');
    }
};
