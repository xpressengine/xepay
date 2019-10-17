<?php
namespace Xehub\Xepay;

use GuzzleHttp\Client;
use InvalidArgumentException;

class Exchanger
{
    /** @var int[]|float[]  */
    private $rates;

    public function __construct()
    {
        $this->load();
    }

    /**
     * @param Money $money
     * @param string $currency
     * @return int|float
     */
    public function exchangeTo(Money $money, $currency)
    {
        return $money->getAmount() * $this->getRate($currency) / $this->getRate($money->getCurrency());
    }

    protected function getRate($currency)
    {
        if (!isset($this->rates[$currency])) {
            throw new InvalidArgumentException("[{$currency}] is not available currency.");
        }

        return $this->rates[$currency];
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @todo cache
     */
    protected function load()
    {
        $client = new Client();

        // published by the European Central Bank
        $response = $client->request('GET', 'https://api.exchangeratesapi.io/latest?base=KRW', [
            'http_errors' => false
        ]);

        if ($response->getStatusCode() !== 200) {
            // handle error
        }

        $content = $response->getBody()->getContents();
        $content = json_decode($content);

        $this->rates = $content['rates'];
    }
}
