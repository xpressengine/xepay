(function (exports, $) {
  'use strict';

  var gateway = null;
  var easiers = {};

  var validator = function (obj) {
    var valid = true,
      interfaces = ['exec'];
    for (var i in interfaces) {
      if (!obj.hasOwnProperty(interfaces[i])) {
        valid = false;
      }
    }

    return valid;
  };

  var events = {
    listeners: {},
    add: function (name, callback) {
      if (!this.listeners[name]) {
        this.listeners[name] = [];
      }
      this.listeners[name].push(callback);
    },
    fire: function (name) {
      var args = Array.prototype.slice.call(arguments, 1)
      var items = this.listeners[name] || [];
      for (var i in items) {
        items[i].apply(null, args);
      }
    }
  };

  var prepare = function (params, callback) {
    $.ajax({
      type: 'post',
      dataType: 'html',
      data: params,
      url: $('#__payment-pg-form').data('url'),
      success: function(response) {
        $('#__payment-pg-form').empty().append(response);

        events.fire('prepare.ready');

        callback();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        events.fire('prepare.error', qXHR, textStatus, errorThrown);
      },
      complete: function(qXHR, textStatus) {
        events.fire('prepare.complete', qXHR, textStatus);
      }
    });
  };

  exports.payment = (function () {
    return {
      defineGateway: function (obj) {
        if (gateway) {
          console.error('Already defined payment gateway');
          return false;
        }

        if (!validator(obj)) {
          console.error('Must be declared implement the remaining methods');
          return false;
        }

        gateway = obj;
      },
      defineEasier: function (obj) {
        if (!validator(obj)) {
          console.error('Must be declared implement the remaining methods');
          return false;
        }

        var name = obj.name || '';
        if (name.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '') === '') {
          console.error('A name entry must be defined.');
          return false;
        }

        easiers[obj.name] = obj;
      },
      listen: function (name, callback) {
        events.add(name, callback);
      },
      exec: function (method, params) {
        prepare(params, function () {

          events.fire('executing');

          if ($('#__form-zero-pay').is('form')) {
            $('#__form-zero-pay').submit();
            return;
          }

          if (easiers[method]) {
            // easy payment
            easiers[method].exec();
          } else {
            if (!gateway) {
              console.error('payment gateway is not defined');
              return false;
            }

            gateway.exec(method);
          }


        }.bind(this));
      }
    };
  })();
})(window, jQuery);
