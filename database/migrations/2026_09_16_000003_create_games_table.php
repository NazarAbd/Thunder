<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Games catalog (admin-managed).
 *
 * The homepage keeps its hardcoded cards for now; rows added here are
 * APPENDED to the matching homepage section by `group`, excluding slugs
 * that already have a hardcoded card (see GameController/PageController).
 * When the hardcoded cards are removed later, every row shows automatically.
 *
 * Groups: `direct` (الشحن المباشر), `account` (الشحن بالحساب),
 * `subscriptions` (الاشتراكات الرقمية).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('name');
            $table->string('description', 1000)->nullable();
            // Storage path on the `public` disk (e.g. images/games/x.jpg)
            // or a legacy public/ relative path for seeded rows.
            $table->string('image_path')->nullable();
            $table->string('group', 30)->default('direct');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['group', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
