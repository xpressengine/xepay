window.payment.defineGateway({
  exec: function (method) {
    $('#__form-test-pay').submit();
  }
});
