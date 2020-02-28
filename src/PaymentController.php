<?php
namespace Xehub\Xepay;

use Xehub\Xepay\Exceptions\PaymentFailedException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    public function before(Request $request, PaymentManager $payment, $id)
    {
        $gateway = $payment->gateway($request->get('_pg'));
        if (!$order = $gateway->getOrder($id)) {
            abort(404);
        }

        return $gateway->render($order, $request->except('_token'));
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
