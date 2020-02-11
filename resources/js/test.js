window.xepay.defineGateway({
  name: 'test',
  exec: function (method) {
    $('#__form-test-pay').submit();
  }
});
