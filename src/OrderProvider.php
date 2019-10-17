<?php
namespace Xehub\Xepay;


interface OrderProvider
{
    public function retrieveById($id);

    public function success(Order $order);

    public function fail(Order $order);
}
