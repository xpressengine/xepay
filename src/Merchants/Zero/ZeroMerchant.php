<?php
namespace Xehub\Xepay\Merchants\Zero;

use Xehub\Xepay\Money;
use Xehub\Xepay\Order;
use Xehub\Xepay\Merchant;
use Xehub\Xepay\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZeroMerchant extends Merchant
{
    protected $order;

    /**
     * @return mixed
     */
    public function scripts()
    {
        return [];
    }

    /**
     * @param Order $order
     * @param array $data
     * @param Money|null $money
     * @return View
     */
    public function render(Order $order, $data = [], Money $money = null)
    {
        return $this->getView('xepay::zero.form', compact('order', 'data'));
    }

    /**
     * @param Request $request
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getData(Request $request, $key, $default = null)
    {
        return $request->get($key, $default);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callback(Request $request)
    {
        //
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function approve(Request $request)
    {
        return new \Xehub\Xepay\Merchants\Zero\Response($this->order);
    }

    /**
     * @param Response $response
     * @return Response
     */
    public function rollback(Response $response)
    {
        return $response;
    }

    /**
     * @param Order $order
     * @param string $message
     * @param array  $data
     * @param Money  $money
     * @param string $transactionId
     * @return Response
     */
    public function cancel(Order $order, $message, array $data, Money $money = null, $transactionId = null)
    {
        return new \Xehub\Xepay\Merchants\Zero\Response($order);
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
