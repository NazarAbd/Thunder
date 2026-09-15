<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Prices table for the site-wide exchange rate (SDG per 1 USD).
 *
 * Design:
 * - Append-only history: every change inserts a new row, old rows stay
 *   for audit. Exactly one row should have `is_active = true` — that is
 *   the rate the storefront uses (see Setting::exchangeRate()).
 * - `created_by` records which admin set the rate. Nullified (not cascade
 *   deleted) if that admin user is removed, so history survives.
 *
 * Security: no public routes touch this table. Writes go only through the
 * admin-only Filament resource (panel auth + role checks + validation).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            // SDG per 1 USD. 14,2 matches top_up_requests.amount precision.
            $table->decimal('rate', 14, 2);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
