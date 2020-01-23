<?php

Route::middleware('web')
    ->namespace('Xehub\Xepay')
    ->prefix(config('xepay.route'))
    ->group(function () {
        Route::match(['get', 'post'], '{pg}/callback', 'PaymentController@callback')->name('xepay::callback');
        Route::post('{id}/before', 'PaymentController@before')->name('xepay::before');
        Route::post('{pg}/{id}/update', 'PaymentController@update')->name('xepay::update');
        Route::match(['get', 'post'], '{pg}/misc', 'PaymentController@misc')->name('xepay::misc');
    });
