<form name="fpaypalcb" method="post" action="{{ route('payment.update', ['pg' => 'paypal', 'id' => $orderId]) }}">
    {{ csrf_field() }}
    <input type="hidden" name="paymentId" value="{{ $paymentId }}">
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="payerId" value="{{ $payerId }}">
</form>
<script>
  (function () {
    document.fpaypalcb.submit();
  })();
</script>
