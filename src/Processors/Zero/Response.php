<?php
namespace Xehub\Xepay\Processors\Zero;

use Xehub\Xepay\Order;
use Xehub\Xepay\Response as ResponseInterface;

class Response implements ResponseInterface
{
    protected $order;

    protected $proof;

    /**
     * Response constructor.
     * @param Order $order
     * @param bool  $proof
     */
    public function __construct(Order $order, $proof)
    {
        $this->order = $order;
        $this->proof = $proof;
    }

    /**
     * @return bool
     */
    public function success()
    {
        return !!$this->proof;
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return !$this->success();
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
     * @return null|string
     */
    public function message()
    {
        return null;
    }

    public function isPending()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return [];
    }
}
