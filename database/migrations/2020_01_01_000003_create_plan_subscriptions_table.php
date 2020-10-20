<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('elsayed85.subscriptions.tables.plan_subscriptions'), function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->unsignedBigInteger('plan_id');
            $table->string('slug');
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('cancels_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique('slug');
            $table->foreign('plan_id')->references('id')->on(config('elsayed85.subscriptions.tables.plans'))
                ->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('plan_subscriptions_translations', function (Blueprint $table) {
            // mandatory fields
            $table->id();
            $table->string('locale')->index();

            // Foreign key to the main model
            $table->unsignedBigInteger('subscription_id');
            $table->unique(['subscription_id', 'locale']);
            $table->foreign('subscription_id')->references('id')->on(config('elsayed85.subscriptions.tables.plan_subscriptions'))
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
        Schema::dropIfExists(config('elsayed85.subscriptions.tables.plan_subscriptions'));
        Schema::dropIfExists(config('plan_subscriptions_translations'));
    }
}
