window.xepay.defineGateway({
  name: 'paypal',
  exec: function () {
    var $f = $('form#fpaypalpayment');
    if (!$f.is('form')) {
      console.error('load fail');
      return false;
    }

    var url = $f.attr('action');

    var width = 560, height = 602,
      left = ((window.screen.width - window.screenX) / 2) - ((width / 2) + 10) + window.screenX,
      top = ((window.screen.height - window.screenY) / 2) - ((height / 2) + 50) + window.screenY;

    var pop = window.open('about:blank', 'PAYPAl_PAY_POP',
      "status=no,height=" + height + ",width=" + width + ",left="
      + left + ",top=" + top + ",screenX=" + left + ",screenY="
      + top + ",toolbar=no,menubar=no,scrollbars=yes,location=no,directories=no");


    $('<div>').attr('id', 'paypal_dim_for_pay').css({
      'position': 'fixed',
      'top': 0,
      'left': 0,
      'bottom': 0,
      'right': 0,
      'background-color': '#000',
      'opacity': 0.5,
      'cursor': 'no-drop',
      'z-index':'99'
    }).click(function () {
      pop.focus();
    }).appendTo('body');

    var timer = setInterval(function() {
      if(pop.closed) {
        clearInterval(timer);
        $('#paypal_dim_for_pay').remove();
      }
    }, 1000);

    setTimeout(function () {
      pop.location.assign(url);
    }, 100);
  }
});
