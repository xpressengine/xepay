<?php
namespace Xehub\Xepay\Events;

use Xehub\Xepay\Order;
use Xehub\Xepay\Gateway;
use Xehub\Xepay\Response;

class Paid
{
    public $order;

    public $response;

    public $gateway;

    public function __construct(Order $order, Response $response, Gateway $gateway)
    {
        $this->order = $order;
        $this->response = $response;
        $this->gateway = $gateway;
    }
}
