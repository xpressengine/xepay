
<form id="__form-zero-pay" method="post" action="{{ route('payment.update', ['id' => $order->getOrderId()]) }}">
    {{ csrf_field() }}
    <input type="hidden" name="_payment_token" value="{{ Session::flash('_payment_token', Str::random(32)) }}">
    @foreach($data as $key => $value)
        @if(is_array($value))
            @foreach($value as $k => $v)
                <input type="hidden" name="{{ $key.(is_int($k) ? '[]' : '['.$k.']') }}" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
</form>