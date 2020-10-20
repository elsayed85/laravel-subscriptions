<?php

declare(strict_types=1);

namespace elsayed85\Subscriptions\Providers;

use elsayed85\Subscriptions\Console\Commands\MigrateCommand;
use elsayed85\Subscriptions\Console\Commands\PublishCommand;
use elsayed85\Subscriptions\Console\Commands\RollbackCommand;
use elsayed85\Subscriptions\Models\Plan;
use elsayed85\Subscriptions\Models\PlanFeature;
use elsayed85\Subscriptions\Models\PlanSubscription;
use elsayed85\Subscriptions\Models\PlanSubscriptionUsage;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.elsayed85.subscriptions.migrate',
        PublishCommand::class => 'command.elsayed85.subscriptions.publish',
        RollbackCommand::class => 'command.elsayed85.subscriptions.rollback',
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'elsayed85.subscriptions');

        // Bind eloquent models to IoC container
        $this->app->singleton('elsayed85.subscriptions.plan', $planModel = $this->app['config']['elsayed85.subscriptions.models.plan']);
        $planModel === Plan::class || $this->app->alias('elsayed85.subscriptions.plan', Plan::class);

        $this->app->singleton('elsayed85.subscriptions.plan_feature', $planFeatureModel = $this->app['config']['elsayed85.subscriptions.models.plan_feature']);
        $planFeatureModel === PlanFeature::class || $this->app->alias('elsayed85.subscriptions.plan_feature', PlanFeature::class);

        $this->app->singleton('elsayed85.subscriptions.plan_subscription', $planSubscriptionModel = $this->app['config']['elsayed85.subscriptions.models.plan_subscription']);
        $planSubscriptionModel === PlanSubscription::class || $this->app->alias('elsayed85.subscriptions.plan_subscription', PlanSubscription::class);

        $this->app->singleton('elsayed85.subscriptions.plan_subscription_usage', $planSubscriptionUsageModel = $this->app['config']['elsayed85.subscriptions.models.plan_subscription_usage']);
        $planSubscriptionUsageModel === PlanSubscriptionUsage::class || $this->app->alias('elsayed85.subscriptions.plan_subscription_usage', PlanSubscriptionUsage::class);

        // Register console commands
        $this->registerCommands($this->commands);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish Resources
        $this->publishesConfig('elsayed85/laravel-subscriptions');
        $this->publishesMigrations('elsayed85/laravel-subscriptions');
        ! $this->autoloadMigrations('elsayed85/laravel-subscriptions') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
