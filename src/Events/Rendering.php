<?php
namespace Xehub\Xepay\Events;

use Xehub\Xepay\Gateway;
use Xehub\Xepay\Money;
use Xehub\Xepay\Order;

class Rendering
{
    public $gateway;

    public $order;

    public $data;

    public $money;

    public function __construct(Gateway $gateway, Order $order, array $data, Money $money = null)
    {
        $this->gateway = $gateway;
        $this->order = $order;
        $this->data = $data;
        $this->money = $money;
    }
}
