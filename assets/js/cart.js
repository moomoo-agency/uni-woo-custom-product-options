'use strict';

/* UniCpoCart
----------------------------------------------------------*/

var UniCpoCart = void 0,
    cpoMakeInstance = void 0;

UniCpoCart = {
    flatpickrCfg: {},
    _init: function _init() {
        try {
            var _unicpo_cart_i18n$fla = unicpo_cart_i18n.flatpickr,
                weekdays = _unicpo_cart_i18n$fla.weekdays,
                months = _unicpo_cart_i18n$fla.months,
                scrollTitle = _unicpo_cart_i18n$fla.scrollTitle,
                toggleTitle = _unicpo_cart_i18n$fla.toggleTitle;

            this.flatpickrCfg = {
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

            this.bindOnCartItemDuplicate();
            this.bindOnCartItemEditInline();
            this.bindOnCartItemSaveAfterInlineEdit();
        } catch (e) {
            console.error(e);
        }
    },
    bindOnCartItemDuplicate: function bindOnCartItemDuplicate() {
        var cpoCartObj = this;

        jQuery(document).on('click', '.uni-cpo-action-duplicate', function (e) {
            e.preventDefault();

            var $el = jQuery(e.target);
            var elData = $el.data();
            var $form = $el.parents('form');
            var nonce = elData.nonce;
            var key = elData.key;

            jQuery.ajax({
                type: 'GET',
                url: wc_add_to_cart_params.cart_url + '?cpo_duplicate_cart_item=' + key + '&_nonce=' + nonce,
                dataType: 'html',
                beforeSend: function beforeSend() {
                    cpoCartObj.block($form);
                    cpoCartObj.block(jQuery('div.cart_totals'));
                },
                success: function success(r) {
                    try {
                        cpoCartObj.update_wc_cart(r);
                    } catch (e) {
                        console.error(e);
                    }
                },
                complete: function complete() {
                    cpoCartObj.unblock($form);
                    cpoCartObj.unblock(jQuery('div.cart_totals'));
                }
            });
        });
    },
    bindOnCartItemEditInline: function bindOnCartItemEditInline() {
        var cpoCartObj = this;

        jQuery(document).on('click', '.uni-cpo-action-edit', function (e) {
            e.preventDefault();

            var $el = jQuery(e.target);
            var elData = $el.data();
            var $tr = $el.closest('tr');
            var security = elData.nonce;
            var key = elData.key;
            var action = 'uni_cpo_cart_item_edit';
            var $variationContainer = $tr.find('.variation');
            var $editForm = jQuery('<div class="cpo-cart-item-edit-form"></div>');
            var $saveBtn = jQuery('<button class="cpo-cart-item-save"></button');

            var data = { action: action, key: key, security: security };

            jQuery.ajax({
                type: 'POST',
                url: woocommerce_params.ajax_url,
                data: data,
                dataType: 'json',
                beforeSend: function beforeSend() {
                    $el.remove();
                    cpoCartObj.block($tr);
                },
                success: function success(r) {
                    try {
                        $editForm.append(r.data);
                        $saveBtn.text('Save changes');
                        $editForm.append($saveBtn);
                        $saveBtn.data('action', 'uni_cpo_cart_item_update');
                        $saveBtn.data('key', key);
                        $saveBtn.data('security', security);
                        $variationContainer.replaceWith($editForm);
                    } catch (e) {
                        console.error(e);
                    }
                },
                complete: function complete() {
                    cpoCartObj.unblock($tr);
                }
            });
        });
    },
    bindOnCartItemSaveAfterInlineEdit: function bindOnCartItemSaveAfterInlineEdit() {
        var cpoCartObj = this;

        jQuery(document).on('click', '.cpo-cart-item-save', function (e) {
            e.preventDefault();

            var $el = jQuery(e.target);
            var data = $el.data();
            var $form = $el.closest('.cpo-cart-item-edit-form');
            var $options = $form.find('.cpo-cart-item-option:not(:disabled)');
            var $tr = $el.closest('tr');
            var isValid = true;

            data.data = {};
            jQuery.each($options, function (i, el) {
                var $el = jQuery(el);
                var elType = this.type || this.tagName.toLowerCase();

                $el.parsley().validate();

                if ($el.parsley().isValid()) {
                    if ('checkbox' === elType) {
                        var name = el.name.replace('[]', '');
                        if (typeof data.data[name] !== 'undefined') {
                            return;
                        }
                        data.data[name] = jQuery.makeArray(data.data[name]);

                        jQuery('input[name="' + el.name + '"]:checked').each(function () {
                            data.data[name].push(this.value);
                        });
                    } else {
                        if ($el.hasClass('cpo-cart-item-option-multi')) {
                            data.data[el.name] = $el.val().split('|');
                        } else {
                            data.data[el.name] = $el.val();
                        }
                    }
                } else {
                    isValid = false;
                }
            });

            if (isValid) {
                jQuery.ajax({
                    type: 'POST',
                    url: woocommerce_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    beforeSend: function beforeSend() {
                        cpoCartObj.block($tr);
                    },
                    success: function success(r) {
                        //console.log(r);
                    },
                    complete: function complete() {
                        try {
                            cpoCartObj.unblock($tr);
                            cpoCartObj.getCartPage(data.key);
                        } catch (e) {
                            console.error(e);
                        }
                    }
                });
            }
        });
    },
    block: function block($node) {
        var cpoCartObj = this;
        if (!cpoCartObj.is_blocked($node)) {
            $node.addClass('processing').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        }
    },
    getCartPage: function getCartPage(key) {
        var cpoCartObj = this;
        var $form = jQuery('.woocommerce-cart-form');
        jQuery.ajax({
            type: 'GET',
            url: wc_add_to_cart_params.cart_url + '?cpo_edited_cart_item=1',
            dataType: 'html',
            beforeSend: function beforeSend() {
                cpoCartObj.block($form);
                cpoCartObj.block(jQuery('div.cart_totals'));
            },
            success: function success(r) {
                //console.log(key);
                cpoCartObj.update_wc_cart(r, false, key);
            },
            complete: function complete() {
                cpoCartObj.unblock($form);
                cpoCartObj.unblock(jQuery('div.cart_totals'));
            }
        });
    },
    is_blocked: function is_blocked($node) {
        return $node.is('.processing') || $node.parents('.processing').length;
    },
    position: function position(el) {
        var $parent = el;
        var $list = void 0;
        var id = void 0;

        if (el.hasClass('parsley-error')) {
            $list = el.closest('.cpo-cart-item-option-wrapper').find('.parsley-errors-list');
        } else {
            $parent = jQuery('' + el.data('parsley-class-handler'));
            id = el.data('parsley-multiple');
            $list = jQuery('[id="parsley-id-multiple-' + id + '"]');
        }

        var width = $parent.outerWidth();

        setTimeout(function () {
            $list.position({
                of: $parent,
                my: 'left top',
                at: 'left bottom',
                collision: 'none'
            });
            $list.css({
                'max-width': width,
                'opacity': 1
            });
        }, 300);
    },
    show_notice: function show_notice(html_element, $target) {
        if (!$target) {
            $target = jQuery('.woocommerce-cart-form');
        }
        $target.before(html_element);
    },
    unblock: function unblock($node) {
        $node.removeClass('processing').unblock();
    },
    update_wc_cart: function update_wc_cart(html_str, preserve_notices) {
        var key = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';

        var cpoCartObj = this;
        var $html = jQuery.parseHTML(html_str);
        var $new_form = jQuery('.woocommerce-cart-form', $html);
        var $new_totals = jQuery('.cart_totals', $html);
        var $notices = jQuery('.woocommerce-error, .woocommerce-message, .woocommerce-info', $html);

        // No form, cannot do this.
        if (jQuery('.woocommerce-cart-form').length === 0) {
            window.location.href = window.location.href;
            return;
        }

        // Remove errors
        if (!preserve_notices) {
            jQuery('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
        }

        if ($new_form.length === 0) {
            // If the checkout is also displayed on this page, trigger reload instead.
            if (jQuery('.woocommerce-checkout').length) {
                window.location.href = window.location.href;
                return;
            }

            // No items to display now! Replace all cart content.
            var $cart_html = jQuery('.cart-empty', $html).closest('.woocommerce');
            jQuery('.woocommerce-cart-form__contents').closest('.woocommerce').replaceWith($cart_html);

            // Display errors
            if ($notices.length > 0) {
                cpoCartObj.show_notice($notices, jQuery('.cart-empty').closest('.woocommerce'));
            }
        } else {
            // If the checkout is also displayed on this page, trigger update event.
            if (jQuery('.woocommerce-checkout').length) {
                jQuery(document.body).trigger('update_checkout');
            }

            jQuery('.woocommerce-cart-form').replaceWith($new_form);
            jQuery('.woocommerce-cart-form').find('input[name="update_cart"]').prop('disabled', true);

            if ($notices.length > 0) {
                cpoCartObj.show_notice($notices);
            }

            cpoCartObj.update_cart_totals_div($new_totals);
        }

        jQuery(document.body).trigger('updated_wc_div');
    },
    update_cart_totals_div: function update_cart_totals_div(html_str) {
        jQuery('.cart_totals').replaceWith(html_str);
        jQuery(document.body).trigger('updated_cart_totals');
    }
};

//
cpoMakeInstance = function cpoMakeInstance() {
    return Object.create(UniCpoCart);
};
window.UniCpoCart = cpoMakeInstance();

// init
window.UniCpoCart._init();

//
window.Parsley.on('field:error', function () {
    window.UniCpoCart.position(this.$element);
});