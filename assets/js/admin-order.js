'use strict';

jQuery(function ($) {

    window.UniCpo = {};

    var _unicpo_i18n$flatpick = unicpo_i18n.flatpickr,
        weekdays = _unicpo_i18n$flatpick.weekdays,
        months = _unicpo_i18n$flatpick.months,
        scrollTitle = _unicpo_i18n$flatpick.scrollTitle,
        toggleTitle = _unicpo_i18n$flatpick.toggleTitle;

    window.UniCpo.flatpickrCfg = {
        locale: {
            weekdays: weekdays,
            months: months,
            daysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
            firstDayOfWeek: 0,
            ordinal: function ordinal(nth) {
                var s = nth % 100;
                if (s > 3 && s < 21) return 'th';
                switch (s % 10) {
                    case 1:
                        return 'st';
                    case 2:
                        return 'nd';
                    case 3:
                        return 'rd';
                    default:
                        return 'th';
                }
            },
            rangeSeparator: ' - ',
            weekAbbreviation: 'Wk',
            scrollTitle: scrollTitle,
            toggleTitle: toggleTitle,
            amPM: ['AM', 'PM']
        }
    };

    var blockDefaults = {
        message: null,
        overlayCSS: {
            backgroundColor: '#fff',
            opacity: 0.7
        }
    };

    $(document.body).on('click', 'tr.item', function () {
        var $table = $(this).closest('table');
        var $rows = $table.find('tr.item.selected');
        $('.cpo-edit-options-btn').hide();

        if ($rows.length === 1) {
            // ONLY one at a time
            var order_item_id = $rows.get(0).getAttribute('data-order_item_id');

            $('.cpo-for-item-' + order_item_id).show();
        }
    });

    $(document.body).on('click', '.cpo-edit-options-btn', function (e) {
        e.stopPropagation();

        var btnData = $(e.target).data();

        $('#woocommerce-order-items').WCBackboneModal({
            template: 'uni-cpo-modal-add-options',
            variable: btnData
        });

        return false;
    });

    $(document.body).on('wc_backbone_modal_loaded', uni_bb_modal_order_init).on('wc_backbone_modal_response', uni_bb_modal_order_response);

    function uni_bb_modal_order_init(e, target) {
        if ('uni-cpo-modal-add-options' === target) {
            var $el = $('#cpo-order-edit-options-wrapper');
            var $form = $el.find('#cpo-item-options-form');
            var product_id = $el.find('#cpo-order-product-id').val();
            var security = $el.find('#cpo-order-security').val();
            var order_item_id = $el.find('#cpo-order-item-id').val();

            $('.wc-backbone-modal-content').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            $form.empty();
            $('#btn-ok').prop('disabled', true);

            var data = {
                action: 'uni_cpo_order_item_edit',
                dataType: 'json',
                product_id: product_id,
                order_item_id: order_item_id,
                security: security,
                data: {}
            };

            $.post(woocommerce_admin_meta_boxes.ajax_url, data, function (r) {
                if (r.success) {
                    $form.append(r.data);
                    var $options = $form.find('.cpo-cart-item-option:not(:disabled)');
                    uni_cpo_validate_order_form($options);
                    $(document.body).on('change focusout', $options, function () {
                        uni_cpo_validate_order_form($options);
                    });
                } else {
                    window.alert(r.data.error);
                }
                $('.wc-backbone-modal-content').unblock();
            });
        }
    }

    function uni_bb_modal_order_response(e, target, data) {
        if ('uni-cpo-modal-add-options' === target) {
            data.dataType = 'json';
            var $item = $('#woocommerce-order-items').find('tr[data-order_item_id="' + data.order_item_id + '"]');

            $item.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            $.post(woocommerce_admin_meta_boxes.ajax_url, data, function (r) {
                if (r.success) {
                    $item.replaceWith(r.data.html);
                    $item.unblock();
                } else {
                    window.alert(r.data.error);
                }
            });
        }
    }

    function uni_cpo_validate_order_form($options) {
        var isValid = true;

        $.each($options, function (i, el) {
            var $el = $(el);

            $el.parsley().validate();

            if (!$el.parsley().isValid()) {
                isValid = false;
            }
        });

        if (isValid) {
            $('#btn-ok').prop('disabled', false);
        } else {
            $('#btn-ok').prop('disabled', true);
        }
    }
});