'use strict';

/* UniCpoCart
----------------------------------------------------------*/

var UniCpoCart = void 0,
    cpoMakeInstance = void 0;

UniCpoCart = {
    flatpickrCfg: {},
    _init: function _init() {
        try {
            
/* Premium Code Stripped by Freemius */


            this.bindOnCartItemDuplicate();
            this.bindOnCartItemEdit();
            this.bindOnCartItemEditInline();
            this.bindOnCartItemSaveAfterInlineEdit();
        } catch (e) {
            console.error(e);
        }
    },
    bindOnCartItemDuplicate: function bindOnCartItemDuplicate() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnCartItemEdit: function bindOnCartItemEdit() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnCartItemEditInline: function bindOnCartItemEditInline() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnCartItemSaveAfterInlineEdit: function bindOnCartItemSaveAfterInlineEdit() {
        
/* Premium Code Stripped by Freemius */

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
            url: unicpo_cart.cart_url + '?cpo_edited_cart_item=1',
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