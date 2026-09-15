<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Services/offers belonging to a game (e.g. "600 gems").
 *
 * Price is stored in USD; the storefront converts to SDG with the active
 * exchange rate (Setting::exchangeRate()). Deleting a game cascades to
 * its services. Inactive services are hidden from the storefront.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('price_usd', 10, 2);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['game_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_services');
    }
};
