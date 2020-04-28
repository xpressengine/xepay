<?php
namespace Xehub\Xepay;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Xehub\Xepay\Exceptions\PaymentFailedException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Xehub\Xepay\Exceptions\UnableMakePaymentException;

class PaymentController extends Controller
{
    public function before(Request $request, PaymentManager $payment, $id)
    {
        $gateway = $payment->gateway($request->get('_pg'));
        if (!$order = $gateway->getOrder($id)) {
            abort(404);
        }

        try {
            return $gateway->render($order, $request->except('_token'));
        } catch (UnableMakePaymentException $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }

    public function callback(Request $request, PaymentManager $payment, $pg)
    {
        $gateway = $payment->gateway($pg);

        return $gateway->callback($request);
    }

    public function update(Request $request, PaymentManager $payment, Redirector $redirector, $pg, $id)
    {
        $gateway = $payment->gateway($pg);

        if (!$order = $gateway->getOrder($id)) {
            abort(404);
        }

        try {
            $gateway->approve($request, $order);
        } catch (PaymentFailedException $e) {
            if (!$response = $redirector->redirectToFail($order)) {
                throw $e;
            }

            return $response;
        }

        return $redirector->redirectToComplete($order);
    }

    public function misc(Request $request, PaymentManager $payment, $pg)
    {
        $gateway = $payment->gateway($pg);

        return $gateway->misc($request);

    }
}
