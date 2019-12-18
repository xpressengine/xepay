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
     * @return int
     */
    public function getAmount();

    /**
     * @return int
     */
    public function getItemCount();

    /**
     * @return string|null
     */
    public function getTransactionId();
}
