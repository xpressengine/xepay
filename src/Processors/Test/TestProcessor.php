<?php
namespace Xehub\Xepay\Processors\Test;

use Xehub\Xepay\Money;
use Xehub\Xepay\Order;
use Xehub\Xepay\Processor;
use Xehub\Xepay\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestProcessor extends Processor
{
    protected $order;

    protected $methods = [
        'card' => '카드',
    ];

    /**
     * @return mixed
     */
    public function scripts()
    {
        return [
            ['file' => __DIR__.'/../../../resources/js/test.js'],
        ];
    }

    /**
     * @param Order $order
     * @param array $data
     * @param Money|null $money
     * @return View
     */
    public function render(Order $order, $data = [], Money $money = null)
    {
        $money = $money ?: Money::KRW($order->getAmount());
        $amount = $this->exchangeMoney($money, 'KRW')->getAmount();
        return $this->getView('xepay::test.form', compact('order', 'data', 'amount'));
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
        return new \Xehub\Xepay\Processors\Test\Response($this->order, $request->get('_payment_amount'));
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
        return new \Xehub\Xepay\Processors\Test\Response($order, $money ? $money->getAmount() : $order->getAmount());
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
