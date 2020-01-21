window.payment.defineGateway({
  name: 'paypal',
  exec: function () {
    var url = $('form#fpaypalpayment').attr('action');

    location.assign(url);
    // var $layer = $('#fpaypal_layer'), $iframe = $('iframe', $layer);
    //
    // $layer.one('hidden.bs.modal', function () {
    //   $iframe.attr('src', 'about:blank');
    // });
    //
    // $iframe.attr('src', url);
    // $layer.modal('show');
  }
});
