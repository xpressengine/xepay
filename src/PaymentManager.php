<?php
namespace Xehub\Xepay;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Xehub\Xepay\Merchants\Zero\ZeroMerchant;

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

    public function gateway($name = null)
    {
        $name = $name ?: $this->getDefaultGateway();

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

        $method = 'create' . Str::studly($name) . 'Gateway';
        if (method_exists($this, $method)) {
            return $this->$method($name, $this->getMerchantInfo($name));
        }

        throw new InvalidArgumentException("PG [$name] not supported.");
    }

    protected function createPaypalGateway($name, $config)
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

        return $this->adapt(new Merchants\Paypal\Paypal($context), $name);
    }

    protected function createTestGateway($name, $config)
    {
        return $this->adapt(new Merchants\Test\TestMerchant(), $name);
    }

    protected function adapt(Merchant $gateway, $name)
    {
        return new Gateway($name, $gateway, $this->getProvider());
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultGateway()
    {
        return $this->app['config']['xepay.default.merchant'];
    }

    protected function getMerchantInfo($name)
    {
        return $this->app['config']["xepay.merchants.{$name}"];
    }

    public function extend($name, Closure $callback)
    {
        $this->customCreators[$name] = $callback;

        return $this;
    }

    public function callback(Request $request)
    {
        return $this->findGateway($request)->callback($request);
    }

    public function approve(Request $request, Order $order)
    {
        $gateway = $this->findGateway($request);
        if (in_array($gateway->getName(), ['zero', 'test'])) {
            $gateway->setOrder($order);
        }

        $gateway->updateOrder($order, $response = $gateway->approve($request));

        return $response;
    }

    public function findGateway(Request $request)
    {
        if ($this->checkZeroPayment($request)) {
            return $this->adapt(new ZeroMerchant(), 'zero');
        }

        return $this->gateway();
    }

    protected function checkZeroPayment(Request $request)
    {
        return $request->get('_payment_token', 0) === $request->session()->get('_payment_token', 1);
    }

    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->gateway(), $method], $parameters);
    }

    /**
     * @param Order $order
     * @param string $gateway gateway name
     * @return mixed
     */
    public function generate(Order $order, $gateway = null)
    {
        return $this->app->make(BladeGenerator::class)->generate($order, $this->gateway($gateway));
    }
}
