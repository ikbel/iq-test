(function ($) {
    'use strict';

    jQuery(function () {
        jQuery('#iq-select-all').click(function () {
            jQuery('input:checkbox').not(this).prop('checked', this.checked);
        });
        jQuery('#iq-select-all2').click(function () {
            jQuery('input:checkbox').not(this).prop('checked', this.checked);
        });


    });

}(jQuery));
