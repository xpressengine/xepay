<?php
namespace Xehub\Xepay;

use Illuminate\Http\Request;
use Illuminate\View\View;

interface PayProcess
{
    /**
     * @return array
     */
    public function scripts();

    /**
     * @param Order $order
     * @param array $data
     * @param Money|null $money
     * @return View
     */
    public function render(Order $order, $data = [], Money $money = null);

    /**
     * @param Request $request
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getData(Request $request, $key, $default = null);

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callback(Request $request);

    /**
     * @param Request $request
     * @return Response
     */
    public function approve(Request $request);

    /**
     * @param Response $response
     * @return Response
     */
    public function rollback(Response $response);

    /**
     * @param Order       $order
     * @param string      $message
     * @param array       $data
     * @param Money|null  $money
     * @param string|null $transactionId
     * @return Response
     */
    public function cancel(Order $order, $message, array $data, Money $money = null, $transactionId = null);
}
