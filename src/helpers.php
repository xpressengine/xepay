<?php

if (function_exists('pay_method_name') === false) {
    function pay_method_name($pg, $key)
    {
        $methods = app('xepay')->gateway($pg)->getMethods();

        return $methods[$key] ?? null;
    }
}
