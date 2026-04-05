/**
 * User / rider dashboard tables — RoyalCommerce expects this file.
 * Tables use Laravel pagination; DataTables only adds column sorting on the current page.
 */
(function ($) {
    'use strict';
    $(function () {
        if (!$.fn.DataTable) {
            return;
        }
        $('table.gs-data-table').each(function () {
            var $table = $(this);
            if ($table.closest('.dataTables_wrapper').length) {
                return;
            }
            $table.DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                retrieve: true,
            });
        });
    });
})(jQuery);
