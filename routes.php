<?php

Route::middleware('web')
    ->namespace('Xehub\Xepay')
    ->prefix(config('xepay.route'))
    ->group(function () {
        Route::match(['get', 'post'], '{pg}/callback', 'PaymentController@callback')->name('payment.callback');
        Route::post('{id}/before', 'PaymentController@before')->name('payment.before');
        Route::post('{pg}/{id}/update', 'PaymentController@update')->name('payment.update');
        Route::match(['get', 'post'], '{pg}/misc', 'PaymentController@misc')->name('payment.misc');
    });
