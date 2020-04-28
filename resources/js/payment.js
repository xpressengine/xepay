(function (exports, $) {
  'use strict';

  var gateway = {};

  var validator = function (obj) {
    var valid = true,
      interfaces = ['name', 'exec'];
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

  var prepare = function (name, method, params, callback) {
    if (typeof params === 'object' && params !== null) {
      params['_pg'] = name;
      params['_pay_method'] = method;
    } else {
      params += (params !== '' ? '&':'') + '_pg='+name+'&_pay_method='+method;
    }
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
        events.fire('prepare.error', jqXHR, textStatus, errorThrown);
      },
      complete: function(jqXHR, textStatus) {
        events.fire('prepare.complete', jqXHR, textStatus);
      }
    });
  };

  exports.xepay = (function () {
    return {
      defineGateway: function (obj) {
        if (!validator(obj)) {
          console.error('Must be declared implement the remaining methods');
          return false;
        }

        gateway[obj.name] = obj;
      },
      listen: function (name, callback) {
        events.add(name, callback);
      },
      exec: function (method, params) {
        var arr = method.split(':');
        var gatewayName = arr[0];
        method = arr[1];
        prepare(gatewayName, method, params, function () {

          events.fire('executing');

          if ($('#__form-zero-pay').is('form')) {
            $('#__form-zero-pay').submit();
            return;
          }


          var selected = gateway[gatewayName] || null;
          if (!selected) {
            console.error('payment gateway is not defined');
            return false;
          }

          selected.exec(method);


        }.bind(this));
      }
    };
  })();
})(window, jQuery);
