<?php
namespace Xehub\Xepay;


class Money
{
    protected $currency;

    protected $amount;

    protected static $exchanger;

    /**
     * @param int|float $amount
     * @param string $currency
     */
    public function __construct($amount, $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function exchange($currency)
    {
        $amount = call_user_func(static::getExchanger(), $this, $currency);
        return new static($amount, $currency);
    }

    /**
     * @param int|float $amount
     * @return Money
     */
    public static function KRW($amount)
    {
        return new static($amount, 'KRW');
    }

    /**
     * @param int|float $amount
     * @return Money
     */
    public static function USD($amount)
    {
        return new static($amount, 'USD');
    }

    public static function setExchanger(callable $exchanger)
    {
        static::$exchanger = $exchanger;
    }

    public static function getExchanger()
    {
        return static::$exchanger;
    }
}
