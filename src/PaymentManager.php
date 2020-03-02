<?php
namespace Xehub\Xepay;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaymentManager
{
    use CreateOrderProviders;

    protected $app;

    protected $gateways = [];

    protected $customCreators = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $name
     * @return Gateway
     */
    public function gateway($name, $force = false)
    {
        if (!$force) {
            if ($this->isTest()) {
                $name = 'test';
            } elseif ($name === 'test') {
                throw new InvalidArgumentException("PG [$name] not supported.");
            }
        }

        if (!isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($name);
        }

        return $this->gateways[$name];
    }

    protected function createGateway($name)
    {
        if (isset($this->customCreators[$name])) {
            return $this->adapt($this->customCreators[$name]($this->app), $name);
        }

        $method = 'create' . Str::studly($name) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->$method($name, $this->getConfig($name));
        }

        throw new InvalidArgumentException("PG [$name] not supported.");
    }

    protected function createPaypalDriver($name, $config)
    {
        $context = new ApiContext(
            new OAuthTokenCredential($config['id'], $config['secret'])
        );
        $context->setConfig([
            'mode' => $config['debug'] ? 'sandbox' : 'live',
            'log.LogEnabled' => $config['log_enabled'],
            'log.FileName' => $config['log_path'],
            'log.LogLevel' => 'DEBUG',
            'validation.level' => 'log',
            'cache.enabled' => $config['cache_enabled'],
            'cache.FileName' => $config['cache_path'],
        ]);

        return $this->adapt(new Processors\Paypal\Paypal($context), $name);
    }

    protected function createTestDriver($name, $config)
    {
        return $this->adapt(new Processors\Test\TestProcessor(), $name);
    }

    protected function createZeroDriver($name, $config)
    {
        return $this->adapt(new Processors\Zero\ZeroProcessor(), $name);
    }

    protected function adapt(Processor $gateway, $name)
    {
        return new Gateway($name, $gateway, $this->getProvider());
    }

    protected function getConfig($name)
    {
        return $this->app['config']["xepay.drivers.{$name}"] ?: [];
    }

    protected function isTest()
    {
        return !!$this->app['config']['xepay.test_mode'];
    }

    public function extend($name, Closure $callback)
    {
        $this->customCreators[$name] = $callback;

        return $this;
    }

    public function getMethods()
    {
        $gateways = $this->getEnables();

        $methods = [];
        foreach ($gateways as $gateway) {
            foreach ($gateway->getMethods() as $code => $text) {
                $key = $gateway->getName().':'.$code;
                $methods[$key] = $text;
            }
        }

        return $methods;
    }

    protected function getEnables()
    {
        if ($this->isTest()) {
            return [$this->gateway('test')];
        }

        $enables = array_intersect_key(
            $this->app['config']['xepay.drivers'],
            array_flip(explode(',', $this->app['config']['xepay.enables']))
        );

        $gateways = [];
        foreach ($enables as $name => $config) {
            $gateways[] = $this->gateway($name);
        }

        return $gateways;
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function generate(Order $order)
    {
        return $this->app->make(BladeGenerator::class)->generate($order, $this->getEnables());
    }
}
