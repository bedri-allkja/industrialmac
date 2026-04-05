(function ($) {
    'use strict';
    var token = $('meta[name="csrf-token"]').attr('content');
    if (token) {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
    }
})(jQuery);
