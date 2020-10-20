<?php

namespace elsayed85\Subscriptions;

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('elsayed85.subscriptions.tables.plan_features'), function (Blueprint $table) {
            // Columns
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->string('slug');
            $table->string('value');
            $table->smallInteger('resettable_period')->unsigned()->default(0);
            $table->string('resettable_interval')->default('month');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['plan_id', 'slug']);
            $table->foreign('plan_id')->references('id')->on(config('elsayed85.subscriptions.tables.plans'))
                ->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('plan_features_translations', function (Blueprint $table) {
            // mandatory fields
            $table->id(); // Laravel 5.8+ use bigIncrements() instead of increments()
            $table->string('locale')->index();

            // Foreign key to the main model
            $table->unsignedBigInteger('feature_id');
            $table->unique(['feature_id', 'locale']);
            $table->foreign('feature_id')->references('id')->on(config('elsayed85.subscriptions.tables.plan_features'))
                ->onDelete('cascade')->onUpdate('cascade');
            // Actual fields you want to translate
            $table->string('name');
            $table->longText('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('elsayed85.subscriptions.tables.plan_features'));
        Schema::dropIfExists(config('plan_features_translations'));
    }
}
