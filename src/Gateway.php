<?php
namespace Xehub\Xepay;

use Illuminate\Foundation\Http\Events\RequestHandled;
use Xehub\Xepay\Events\Paid;
use Xehub\Xepay\Events\PaymentRolledBack;
use Xehub\Xepay\Exceptions\PaymentFailedException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

class Gateway
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Merchant
     */
    protected $pg;

    /**
     * @var OrderProvider
     */
    protected $provider;

    /**
     * @var Dispatcher
     */
    protected static $dispatcher;

    /**
     * @var Response
     */
    protected $response;

    public function __construct($name, Merchant $pg, OrderProvider $provider)
    {
        $this->pg = $pg;
        $this->name = $name;
        $this->provider = $provider;
    }

    public function approve(Request $request)
    {
        $this->createLog(PaymentLog::TYPE_PAY, $this->response = $this->pg->approve($request));

        return $this->response;
    }

    protected function createLog($type, Response $response, PaymentLog $parent = null)
    {
        $log = new PaymentLog();
        $log->fill([
            'pg' => $this->getName(),
            'oid' => $response->orderId(),
            'tid' => $response->transactionId(),
            'type' => $type,
            'method' => $response->payMethod(),
            'currency' => $response->currency(),
            'amount' => $response->amount(),
            'success' => $response->success(),
            'response' => $response->getAll(),
        ]);
        if ($type !== PaymentLog::TYPE_PAY && $parent) {
            $log->parent()->associate($parent);
        }

        return $log->save();
    }

    public function rollback()
    {
        $log = PaymentLog::paid()->succeeded()
            ->byPg($this->getName())
            ->byOid($this->response->orderId())
            ->byTid($this->response->transactionId())
            ->first();

        if (!$log) {
            throw new \Exception('Not exists the log for cancel');
        }

        $response = $this->pg->rollback($this->response);

        static::getEventDispatcher()->dispatch(new PaymentRolledBack($response, $log));

        // rollback 처리는 결제가 성공한 후 비지니스로직을 처리하다 오류가 발생하는 경우 수행됨.
        // 일반적으로 transaction 을 사용하여 데이터 저장처리를 수행하므로, 오류발생시 로그기록 또한
        // 저장되지 않을수 있음. lifecycle 이 종료되는 시점에 로그를 기록하게 하여 이 문제를 해결.
        static::getEventDispatcher()->listen(RequestHandled::class, function () use ($response, $log) {
            $this->createLog(PaymentLog::TYPE_ROLLBACK, $response, $log);
        });

        return $response;
    }


    public function cancel(Order $order, $message, $amount = 0, $transactionId = null)
    {
        $log = PaymentLog::paid()->succeeded()
            ->byPg($this->getName())
            ->byOid($order->getOrderId())
            ->byTid($transactionId ?: $order->getTransactionId())
            ->first();

        if (!$log) {
            throw new \Exception('Not exists the log for cancel');
        }

        $response = $this->pg->cancel($order, $message, $log->response, $amount, $transactionId);

        $this->createLog(PaymentLog::TYPE_CANCEL, $response, $log);

        return $response;
    }

    public function getOrder($id)
    {
        return $this->provider->retrieveById($id);
    }

    public function updateOrder(Order $order, Response $response)
    {
        $provider = $this->provider;
        if ($response->fails()) {
            $provider->fail($order);

            throw new PaymentFailedException();
        }

        try {
            static::getEventDispatcher()->dispatch(new Paid($order, $response, $this));

            $provider->success($order);
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }

        return $order;
    }

    public function getName()
    {
        return $this->name;
    }

    public static function setEventDispatcher(Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    public static function getEventDispatcher()
    {
        return static::$dispatcher;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->pg, $name], $arguments);
    }
}
