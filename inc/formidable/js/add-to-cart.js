(function ($) {
    'use strict';
    if (typeof vforce_helper.variable['formidable_product-selector'] !== 'undefined') {
        var itemMeta = vforce_helper.variable['formidable_product-selector']
        var input = $(`[name="${itemMeta}"]`)
        if ($(`[name=${itemMeta}]`)) {
            input.on('click', function () {
                localStorage.setItem('appTypeCode', $(this).val())
            })

            $('.frm_final_submit').on('click', (e) => {
                e.preventDefault()
                e.stopPropagation()
                var appTypeCode = localStorage.getItem('appTypeCode');
                if ('undefined' === typeof wc_add_to_cart_params) {
                    // The add to cart params are not present.
                    return false;
                }

                var data = {
                    product_id: appTypeCode,
                    quantity: 1,
                };


                $.post(wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'), data, function (response) {
                    console.log('checking for response')
                    if (!response) {
                        return;
                    }

                    localStorage.removeItem('appTypeCode')
                    jQuery('.frm_final_submit').closest('form').submit()

                });

            })
        }
    }
})(jQuery);