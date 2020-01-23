<?php
namespace Xehub\Xepay\Processors\Test;

use Xehub\Xepay\Order;
use Xehub\Xepay\Response as ResponseInterface;

class Response implements ResponseInterface
{
    protected $order;

    protected $amount;

    public function __construct(Order $order, $amount)
    {
        $this->order = $order;
        $this->amount = $amount;
    }

    /**
     * @return bool
     */
    public function success()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return false;
    }

    /**
     * @return string
     */
    public function orderId()
    {
        return $this->order->getOrderId();
    }

    /**
     * @return string
     */
    public function transactionId()
    {
        return $this->orderId();
    }

    /**
     * @return string
     */
    public function payMethod()
    {
        return 'card';
    }

    public function currency()
    {
        return 'KRW';
    }

    /**
     * @return int
     */
    public function amount()
    {
        return (int)$this->amount;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return [];
    }
}
