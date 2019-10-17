### 사전 작업
payment 패키지를 사용하기 위해선 `PaymentManager` 에 `Order` interface 가 구현된 주문객체를
제공하는 `OrderProvider` 가 등록되어야 합니다.

```php
use use Xehub\Xepay\OrderProvider;

class CustomOrderProvider implements OrderProvider
{
    ...
}
```

```php
class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving('xepay', function ($payment, $app) {
           $payment->provider('custom', function () {
               return new CustomOrderProvider();
            });
        });
    }
}
```

그리고 등록한 provider 를 config 에 지정합니다.
```php

return [
    'default' => [
        'merchant' => env('PAYMENT_DEFAULT', 'paypal'),
        'provider' => 'custom',
    ],
    
    ...
];
```

~~또한, 동작에 필요한 스크립트 파일을 public 디렉토리에 퍼블리싱합니다.~~
```
# php artisan vendor:publish --tag=payment   
```
~~패키지가 업데이트된경우 `--force` 옵션을 추가하여 실행합니다.~~

> PaymentServiceProvider 가 auto discover 되지 않은 경우에는 config/app.php 에 service provider 를 등록해야 합니다.

### 결제페이지 기능 삽입
결제페이지 하단에 `@paying($order)`와 같이 blade 지시자를 삽입합니다.
전달하는 `$order` 는 주문객체(Order) 입니다.

결제 기능에서 사용하는 결제수단은 1개 이상일 수 있습니다. 결제수단은 `app('xepay')->getMethods()` 를 이용하여 처리하거나,
script `payment.methods` 값을 통해 동적으로 폼에 삽입할 수 있습니다.

이제 사용자가 결제페이지에서 결제 버튼을 클릭(혹은 폼 submit) 시 해당 이벤트 리스너에서 결제동작을 호출해야 합니다.
```javascript
<script>
$('.btn-submit').click(function () {
  payment.exec(method);
});
</script>
```
메서드를 실행할때 사용자가 선택한 결제수단 코드를 전달하세요.

결제완료시 사용해야할 기타 정보들이 있다면 두번째 인자로 값들을 전달하세요.
```javascript
payment.exec(method, params);
```

결제기능의 js 내에는 몇가지 이벤트가 심어저 있습니다. 결제요청중 필요한 처리가 있는 경우 이 이벤트를 통해 작업을 수행할 수 있습니다.
예를들어 중복 요청 방지를 위해 `exec()` 를 실행하기전 결제버튼을 disabled 한 경우 결제 폼 요청이 완료된 후 `prepare.complete` 이벤트를 통해
disabled 를 해제할 수 있습니다.
```javascript
payment.listen('prepare.complete', function () {
  $('.btn-submit').prop('disabled', false);
});
```


### 결제 후 처리
결제가 성공하고 주문객체(Order)에 성공처리를 요청하기 전, Paid 이벤트가 호출됩니다.
필요한 작업을 이벤트로 등록하여 처리합니다.
```php
use Xehub\Xepay\Events\Paid;
Event::listen(Paid::class, function($event) {
    // 필요한 비지니스 로직을 작성합니다.
});
``` 

주문객체에 성공처리가 끝난 후에 처리해야 하는 코드는 `OrderProvider::success` 를 통해 처리하세요.

### 페이지 이동
결제가 성공, 또는 실패하는 경우 이동될 페이지를 지정할 수 있습니다.
```php
app('xepay.redirect')->completing(function ($order) {
    return route('complete', $order->getOrderId());
});
```

이동시 세션처리등 부가적인 작업을 수행하는 경우라면, RedirectResponse 를 직접 반환하여 처리할 수 있습니다.
```php
app('xepay.redirect')->completing(function ($order) {
    return redirect()->route('complete', $order->getOrderId())
        ->with('message', 'payment complete!');
});
```

결제 실패시에는 `failing` 메소드를 사용하세요.