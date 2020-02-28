<?php
namespace Xehub\Xepay;

interface Response
{
    /**
     * @return bool
     */
    public function success();

    /**
     * @return bool
     */
    public function fails();

    /**
     * @return string
     */
    public function orderId();

    /**
     * @return string
     */
    public function transactionId();

    /**
     * @return string
     */
    public function payMethod();

    /**
     * @return string|null
     */
    public function currency();

    /**
     * @return int
     */
    public function amount();

    /**
     * @return string|null
     */
    public function message();

    /**
     * @return bool
     */
    public function isPending();

    /**
     * @return array
     */
    public function getAll();
}
