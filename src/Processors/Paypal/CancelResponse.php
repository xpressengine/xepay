<?php
namespace Xehub\Xepay\Processors\Paypal;

use PayPal\Api\DetailedRefund;

class CancelResponse implements \Xehub\Xepay\Response
{
    protected $refund;

    protected $orderId;

    protected $transactionId;

    public function __construct(DetailedRefund $refund, $orderId, $transactionId)
    {
        $this->refund = $refund;
        $this->orderId = $orderId;
        $this->transactionId = $transactionId;
    }

    /**
     * @return bool
     */
    public function success()
    {
        return $this->refund->getState() === 'completed';
    }

    /**
     * @return bool
     */
    public function fails()
    {
//        return $this->refund->getState() === 'failed';
        return !$this->success();
    }

    /**
     * @return string
     */
    public function orderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function transactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return string
     */
    public function payMethod()
    {
        return 'paypal';
    }

    public function currency()
    {
        return 'USD';
    }

    /**
     * @return int
     */
    public function amount()
    {
        return $this->refund->getAmount()->getTotal();
    }

    /**
     * @return null|string
     */
    public function message()
    {
        return $this->fails() ? 'Refunding fail..' : null;
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
        return $this->refund->toArray();
    }
}
