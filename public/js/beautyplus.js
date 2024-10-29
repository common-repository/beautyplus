(function($) {
    "use strict";

    function BeautyPlus() {

        var self = this;

        self.$window = $(window);
        self.$document = $(document);

        // Let's Start
        self.init();
    }

    BeautyPlus.prototype = {

        /*
         *	Initialize
         */

        init: function() {
            var self = this;

            self.pulse();
        },

        extensions: function(fn) {
            $.BeautyPlus[fn]();
        },

        changeUrl: function(page) {
            window.history.pushState("", "", page);
        },

        pulse: function() {

          $.ajax({
            type: 'POST',
            url: BeautyPlus_vars.ajax_url,
            data: {
              action: 'beautyplus_pulse',
              t: BeautyPlus_vars.beautyplus_t,
              i: BeautyPlus_vars.beautyplus_i
            },
            dataType: 'json',
            cache: false,
            headers: {
              'cache-control': 'no-cache'
            }
          });
        }
    };

    $.BeautyPlus = BeautyPlus.prototype;

    $(document).ready(function() {
        new BeautyPlus();
    });


})(jQuery);
