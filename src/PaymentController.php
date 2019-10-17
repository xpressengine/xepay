<?php
namespace Xehub\Xepay;

use Xehub\Xepay\Exceptions\PaymentFailedException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function before(Request $request, PaymentManager $payment, $id)
    {
        if (!$order = $payment->getOrder($id)) {
            abort(404);
        }

        return $payment->render($order, $request->except('_token'));
    }

    public function callback(Request $request, PaymentManager $payment)
    {
        return $payment->callback($request);
    }

    public function update(Request $request, PaymentManager $payment, Redirector $redirector, $id)
    {
        if (!$order = $payment->getOrder($id)) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $payment->approve($request, $order);
            DB::commit();
        } catch (PaymentFailedException $e) {
            DB::rollBack();

            if (!$response = $redirector->redirectToFail($order)) {
                throw $e;
            }

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $redirector->redirectToComplete($order);
    }
}
