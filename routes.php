<?php

Route::middleware('web')
    ->namespace('Xehub\Xepay')
    ->prefix('xepay')
    ->group(function () {
        Route::match(['get', 'post'], 'callback', 'PaymentController@callback')->name('payment.callback');
        Route::post('{id}/before', 'PaymentController@before')->name('payment.before');
        Route::post('{id}/update', 'PaymentController@update')->name('payment.update');
    });
