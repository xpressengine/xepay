<?php
namespace Xehub\Xepay;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory as ViewFactory;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xepay');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        }

        $this->app['blade.compiler']->directive('paying', function ($expression) {
            return "<?php echo app('xepay')->generate({$expression}); ?>";
        });

        Money::setExchanger(function ($money, $currency) {
            return $this->app[Exchanger::class]->exchangeTo($money, $currency);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'xepay');

        $this->app->singleton('xepay', function ($app) {
            Gateway::setEventDispatcher($app['events']);
            Merchant::setViewResolver($app[ViewFactory::class]);

            return new PaymentManager($app);
        });
        $this->app->alias('xepay', PaymentManager::class);

        $this->app->singleton('xepay.redirect', function ($app) {
            return new Redirector($app['redirect']);
        });
        $this->app->alias('xepay.redirect', Redirector::class);

        $this->app->singleton(Exchanger::class, function ($app) {
            return new Exchanger();
        });
    }
}
