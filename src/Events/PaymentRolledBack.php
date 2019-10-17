<?php
namespace Xehub\Xepay\Events;

use Xehub\Xepay\PaymentLog;
use Xehub\Xepay\Response;

class PaymentRolledBack
{
    /**
     * The response from the merchant
     *
     * @var Response
     */
    public $response;

    /**
     * The log object on success
     *
     * @var PaymentLog
     */
    public $log;

    public function __construct(Response $response, PaymentLog $log)
    {
        $this->response = $response;
        $this->log = $log;
    }
}
