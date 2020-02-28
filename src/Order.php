<?php
namespace Xehub\Xepay;


interface Order
{
    /**
     * @return string|null
     */
    public function getOrderId();

    /**
     * @return string
     */
    public function getOrderTitle();

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @return float
     */
    public function getRefundedAmount();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return int
     */
    public function getItemCount();

    /**
     * @return string|null
     */
    public function getTransactionId();

    /**
     * @return string|null
     */
    public function getPayMethod();

    /**
     * @return string
     */
    public function getPayerName();

    /**
     * @return string
     */
    public function getPayerEmail();

    /**
     * @return string
     */
    public function getPayerPhone();
}
