<?php
namespace Xehub\Xepay;

use InvalidArgumentException;

trait CreateOrderProviders
{
    protected $providerCreators = [];

    protected $providers = [];

    public function provider($name, \Closure $callback)
    {
        $this->providerCreators[$name] = $callback;
    }

    protected function createProvider($name)
    {
        if (!isset($this->providerCreators[$name])) {
            throw new InvalidArgumentException("Unknown provider [$name]");
        }

        return call_user_func($this->providerCreators[$name]);
    }

    protected function getProvider($name = null)
    {
        $name = $name ?: $this->getDefaultProvider();

        if (!isset($this->providers[$name])) {
            $this->providers[$name] = $this->createProvider($name);
        }

        return $this->providers[$name];
    }

    public function getDefaultProvider()
    {
        return $this->app['config']['xepay.default.provider'];
    }
}
