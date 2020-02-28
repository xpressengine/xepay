<?php
namespace Xehub\Xepay\Processors\Paypal;


use Xehub\Xepay\Money;
use Xehub\Xepay\Order;
use Xehub\Xepay\Processor;
use Xehub\Xepay\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment as PaypalPayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;

class Paypal extends Processor
{
    protected $context;

    protected $methods = [
        'paypal' => 'Paypal',
    ];

    public function __construct(ApiContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function scripts()
    {
        return [
            ['file' => __DIR__.'/../../../resources/js/paypal.js']
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
//        $money = $money ?: Money::KRW($order->getAmount());
        $money = $money ?: new Money($order->getAmount(), $order->getCurrency());

        $usdAmount = $this->getUsdAmount($money);

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item = new Item();
        $item->setName($order->getOrderTitle())->setCurrency('USD')->setQuantity(1)
            ->setSku($order->getOrderId())->setPrice($usdAmount);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $amt = new Amount();
        $amt->setCurrency('USD') //->setDetails($details)
            ->setTotal($usdAmount);

        $transaction = new Transaction();
        $transaction->setAmount($amt)
            ->setItemList($itemList)
//            ->setDescription($order->getOrderTitle())
            ->setInvoiceNumber($order->getOrderId());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('xepay::callback', ['pg' => 'paypal', 'orderId' => $order->getOrderId()]))
            ->setCancelUrl(route('xepay::misc', ['pg' => 'paypal', 'orderId' => $order->getOrderId()]));

        $payment = new PaypalPayment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        $payment->create($this->context);

        $approvalUrl = $payment->getApprovalLink();

        return $this->getView('xepay::paypal.payment', compact('approvalUrl'));
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

    public function callback(Request $request)
    {
        $paypalPayment = PaypalPayment::get($request->get('paymentId'), $this->context);
        $orderId = $paypalPayment->getTransactions()[0]->getInvoiceNumber();

        return $this->getView('xepay::paypal.callback', [
            'paymentId' => $request->get('paymentId'),
            'token' => $request->get('token'),
            'payerId' => $request->get('PayerID'),
            'orderId' => $orderId,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function approve(Request $request)
    {
        $payment = PaypalPayment::get($request->get('paymentId'), $this->context);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('payerId'));

        $payment = $payment->execute($execution, $this->context);

        return new \Xehub\Xepay\Processors\Paypal\Response($payment);
    }

    /**
     * @param Response $response
     * @return Response
     */
    public function rollback(Response $response, Order $order)
    {
        $request = new RefundRequest();

        $sale = new Sale();
        $refunded = $sale->setId($response->transactionId())->refundSale($request, $this->context);

        return new \Xehub\Xepay\Processors\Paypal\CancelResponse(
            $refunded,
            $response->orderId(),
            $response->transactionId()
        );
    }

    /**
     * @param Order  $order
     * @param string $message
     * @param array  $data
     * @param Money  $money
     * @param string $transactionId
     * @return Response
     */
    public function cancel(Order $order, $message, array $data, Money $money = null, $transactionId = null)
    {
        $transactionId = $transactionId ?: $order->getTransactionId();

        $request = new RefundRequest();

        // 부분 취소
        if ($money) {
            $amt = new Amount();
            $amt->setCurrency('USD')
                ->setTotal($this->getUsdAmount($money));

            $request->setAmount($amt);
        }

        $sale = new Sale();
        $refunded = $sale->setId($transactionId)->refundSale($request, $this->context);

        return new \Xehub\Xepay\Processors\Paypal\CancelResponse(
            $refunded,
            $order->getOrderId(),
            $transactionId
        );
    }

    public function misc(Request $request)
    {
        return '<script>window.close();</script>';
    }

    protected function getUsdAmount(Money $money, $precision = 2)
    {
        return round($this->exchangeMoney($money, 'USD')->getAmount(), $precision);
    }

    public function getContext()
    {
        return $this->context;
    }
}
