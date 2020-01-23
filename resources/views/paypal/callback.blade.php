<form id="fpaypalcb" name="fpaypalcb" method="post" action="{{ route('payment.update', ['pg' => 'paypal', 'id' => $orderId]) }}">
    {{ csrf_field() }}
    <input type="hidden" name="paymentId" value="{{ $paymentId }}">
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="payerId" value="{{ $payerId }}">
</form>
<script>
  (function () {
      var f = document.getElementById('fpaypalcb');
      opener.document.body.appendChild(f);
      opener.document.fpaypalcb.submit();
      window.close();
  })();
</script>
