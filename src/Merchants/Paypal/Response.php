<?php
namespace Xehub\Xepay\Merchants\Paypal;

use Xehub\Xepay\Response as ResponseInterface;

class Response implements ResponseInterface
{
    protected $payment;

    protected $transaction;

    public function __construct(\PayPal\Api\Payment $payment)
    {
        $this->payment = $payment;
        $this->transaction = $payment->getTransactions()[0];
    }

    /**
     * @return bool
     */
    public function success()
    {
        return $this->payment->getState() === 'approved';
    }

    /**
     * @return bool
     */
    public function fails()
    {
//        return $this->payment->getState() === 'failed';
        return !$this->success();
    }

    /**
     * @return string
     */
    public function orderId()
    {
        return $this->transaction->getInvoiceNumber();
    }

    /**
     * @return string
     */
    public function transactionId()
    {
        return $this->transaction->getRelatedResources()[0]->sale->getId();
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
        return $this->transaction->getAmount()->getTotal();
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->payment->toArray();
    }
}