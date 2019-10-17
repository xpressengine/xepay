<?php
namespace Xehub\Xepay\Merchants\Zero;

use Xehub\Xepay\Order;
use Xehub\Xepay\Response as ResponseInterface;

class Response implements ResponseInterface
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
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
        return null;
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
        return 0;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return [];
    }
}