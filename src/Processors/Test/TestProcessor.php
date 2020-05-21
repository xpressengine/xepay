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
        $money = $money ?: new Money($order->getAmount(), $order->getCurrency());
        return $this->getView('xepay::test.form', [
            'order' => $order,
            'data' => $data,
            'amount' => $money->getAmount(),
            'currency' => $money->getCurrency()
        ]);
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
     * @param Order $order
     * @param Request $request
     * @return Response
     */
    public function approve(Order $order, Request $request)
    {
        return new \Xehub\Xepay\Processors\Test\Response(
            $order,
            $request->get('_payment_amount'),
            $request->get('_payment_currency')
        );
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
        return new \Xehub\Xepay\Processors\Test\Response(
            $order,
            $money ? $money->getAmount() : $order->getAmount(),
            $money ? $money->getCurrency() : $order->getCurrency()
        );
    }
}
