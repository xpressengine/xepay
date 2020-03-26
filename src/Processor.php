<?php
namespace Xehub\Xepay;

use Illuminate\Contracts\View\Factory as ViewFactoryContract;

abstract class Processor implements PayProcess
{
    protected static $view;

    protected $methods = [];

    public function getMethods()
    {
        if (method_exists($this, 'enabledMethods')) {
           return array_intersect_key($this->methods, array_flip($this->enabledMethods()));
        }

        return $this->methods;
    }

    public function getView($view, $data = [])
    {
        return static::getViewResolver()->make($view, $data);
    }

    public function getViewFile($path, $data = [])
    {
        return static::getViewResolver()->file($path, $data);
    }

    public static function setViewResolver(ViewFactoryContract $view)
    {
        static::$view = $view;
    }

    /**
     * @return ViewFactoryContract
     */
    public static function getViewResolver()
    {
        return static::$view;
    }

    protected function exchangeMoney(Money $money, $currency)
    {
        if ($money->getCurrency() === $currency) {
            return $money;
        }

        return $money->exchange($currency);
    }
}
