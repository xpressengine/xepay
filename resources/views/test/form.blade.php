<form id="__form-test-pay" method="post" action="{{ route('payment.update', ['pg' => 'test', 'id' => $order->getOrderId()]) }}">
    {{ csrf_field() }}
    <input type="hidden" name="_payment_method" value="card">
    <input type="hidden" name="_payment_amount" value="{{ $amount }}">
    @foreach($data as $key => $value)
        @if(is_array($value))
            @foreach($value as $v)
                <input type="hidden" name="{{ $key.(is_int($k) ? '[]' : '['.$k.']') }}" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
</form>
