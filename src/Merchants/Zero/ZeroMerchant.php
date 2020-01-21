<?php
namespace Xehub\Xepay\Merchants\Zero;

use Illuminate\Support\Str;
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
        $tokenName = $this->getTokenName($order);
        $token = Str::random(32);
        session()->flash($tokenName, $token);

        return $this->getView('xepay::zero.form', compact('order', 'data', 'token'));
    }

    protected function getTokenName(Order $order)
    {
        return '_payment_token'.hash_hmac('sha1', $order->getOrderId(), config('app.key'));
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
        throw new \Exception('Unhandled.', 400);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function approve(Request $request)
    {
        $tokenName = $this->getTokenName($this->order);
        $proof = $request->get('_payment_token', 0) === $request->session()->get($tokenName, 1);
        return new \Xehub\Xepay\Merchants\Zero\Response($this->order, $proof);
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
        return new \Xehub\Xepay\Merchants\Zero\Response($order, true);
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
