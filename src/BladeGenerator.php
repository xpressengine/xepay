<?php
namespace Xehub\Xepay;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Collection;

class BladeGenerator
{
    protected $urls;

    public function __construct(UrlGenerator $urls)
    {
        $this->urls = $urls;
    }

//    public function generate(Order $order, Gateway $gateway)
    public function generate(Order $order, array $gateways)
    {
        $route = $this->urls->route('xepay::before', ['id' => $order->getOrderId()]);
        $defaultJs = $this->getContent(__DIR__.'/../resources/js/payment.js');
//        $gatewayJs = $this->getGatewayJs($gateway);
        $gatewayJs = '';
        foreach ($gateways as $gateway) {
            $gatewayJs .= $this->getGatewayJs($gateway).PHP_EOL;
        }

            return <<<EOP
<div id="__payment-pg-form" data-url="{$route}"></div>
<script>
    $defaultJs
</script>
$gatewayJs
EOP;
    }

    protected function getGatewayJs(Gateway $gateway)
    {
        $scripts = Collection::make($gateway->scripts())->partition(function ($item) {
            return $this->urls->isValidUrl($item['file']);
        });

        $internal = $scripts->last()->map(function ($script) {
            return $this->getContent($script['file']);
        })->implode(PHP_EOL);

        $external = $scripts->first()->map(function ($script) {
            $attrs = Collection::make($script['attributes'] ?? [])->map(function ($val, $key) {
                return sprintf('%s="%s"', $key, $val);
            })->implode(' ');
            return "<script src=".$this->urls->asset($script['file'])." {$attrs}></script>";
        })->implode(PHP_EOL);

//        $methods = json_encode($gateway->getMethods());

        return <<<EOP
$external
<script>
    $internal
</script>
EOP;
    }

    protected function getContent($path)
    {
        return file_get_contents($path = $this->pathNormalize($path));
    }

    protected function pathNormalize($path)
    {
        $segments = explode('/', $path);
        if (false === $key = array_search('..', $segments, true)) {
            return $path;
        }
        $segments = array_filter($segments, function ($segment) {
            return $segment !== '.';
        });
        $heads = array_slice($segments, 0, $key-1);
        $tails = [];
        foreach (array_slice($segments, $key-1) as $segment) {
            if ($segment === '..') {
                empty($tails) ? array_pop($heads) : array_shift($tails);
            } else {
                $tails[] = $segment;
            }
        }

        return realpath(implode('/', $heads) .'/'. implode('/', $tails));
    }
}
