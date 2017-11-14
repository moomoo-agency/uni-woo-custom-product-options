/* UniCpo
----------------------------------------------------------*/

let UniCpo, cpoMakeInstance, UniCpoInstance;

UniCpo = {
    cpo:                  unicpo.cpo_on,
    calc:                 unicpo.calc_on,
    calcBtn:              unicpo.calc_btn_on,
    _ajax_sent:           false,
    mainImageEl:          {},
    flexContainer:        {},
    addToCartBtnSelector: '.single_add_to_cart_button, button.product_type_simple',
    addToCartBtnEl:       {},
    priceTagEl:           {},
    priceZeroEl:          jQuery('<span class="js-cpo-text-zero-pice"></span>'),
    priceCalculateEl:     jQuery('<span class="js-cpo-calculating"></span>'),
    _init:                function () {

        this.flexContainer  = jQuery('.flex-viewport');
        this.mainImageEl    = jQuery(unicpo.image_selector);
        this.addToCartBtnEl = jQuery(this.addToCartBtnSelector);
        if (!this.addToCartBtnEl.length) {
            console.info('Add to cart button is not found');
        }
        this.priceTagEl = jQuery(unicpo.price_selector);
        if (!this.priceTagEl.length) {
            console.info('Price tag html element is not found');
        }
        this.priceZeroEl.html(unicpo.price_vars.price);
        this.priceCalculateEl.html(unicpo_i18n.calc_text);

        if (this.calc && !this.calcBtn) {
            if (this._request_sent) {
                return false;
            }
            // initial calculation
            this.processFormData();
            this.bindOnOptionSelected();
            this.bindOnAddToCartClick();
        } else if (this.calc && this.calcBtn) {
            this.addToCartBtnEl.prop('disabled', true);
            this.setPriceTo(this.priceZeroEl);
            this.bindOnCalcBtnClick();
            this.bindOnAddToCartClick();
        }
        this._initTooltip();
    },
    bindOnOptionSelected: function () {
        const cpoObj = this;
        jQuery(document).on('change', unicpo.options_selector, function () {
            if (cpoObj._ajax_sent) {
                return false;
            }
            cpoObj.processFormData();
        });
    },
    bindOnCalcBtnClick:   function () {
        const cpoObj = this;
        jQuery(document).on('change', '#js-uni-cpo-calculate-btn', function () {
            if (cpoObj._ajax_sent) {
                return false;
            }
            cpoObj.processFormData();
        });
    },
    bindOnAddToCartClick: function () {
        const $addToCartBtn = this.addToCartBtnEl;
        const $form         = $addToCartBtn.closest('form');

        jQuery(document).on('click', this.addToCartBtnSelector, function (e) {
            e.preventDefault();

            // validates the form
            $form.parsley({
                excluded: '[disabled], .qty, .uni-cpo-excluded-field'
            }).validate();

            if ($form.parsley().isValid()) {
                //$addToCartBtn.attr('name', '');
                const $excludeFromFormSubmission = jQuery('.uni-cpo-excluded-field');
                $excludeFromFormSubmission.each(function(){
                    jQuery(this).prop('disabled', true);
                });
                $form.submit();
                $excludeFromFormSubmission.each(function(){
                    jQuery(this).prop('disabled', false);
                });
            } else {
                //$addToCartBtn.attr('name', 'add-to-cart');
            }
        });
    },
    calculate:            function (fields) {
        const cpoObj = this;
        const $form  = cpoObj.addToCartBtnEl.closest('form');
        const form   = $form[0];

        const data = {
            action:   'uni_cpo_price_calc',
            security: unicpo.security,
            data:     fields
        };

        jQuery.ajax({
            url:        unicpo.ajax_url,
            data,
            dataType:   'json',
            method:     'POST',
            beforeSend: function () {
                cpoObj._blockForm(form);
                cpoObj.setPriceTo(cpoObj.priceCalculateEl);
                // Triggers an event - on before send ajax request
                jQuery(document.body).trigger('cpo_options_data_ajax_before_send', [fields]);
            },
            error:      function () {
                cpoObj._unblockForm(form, 'error');
                cpoObj.setPriceTo(cpoObj.priceZeroEl);
            },
            success:    function (r) {
                if (r.success) {
                    cpoObj._unblockForm(form, 'success');

                    unicpo.formatted_vars = r.data.formatted_vars;
                    jQuery.extend(unicpo.price_vars, r.data.price_vars);
                    jQuery.extend(unicpo.extra_data, r.data.extra_data);

                    cpoObj.priceTagEl.html(unicpo.price_vars.price);
                    cpoObj.addToCartBtnEl.prop('disabled', false);

                    // Triggers an event - on successful ajax request
                    jQuery(document.body).trigger('cpo_calc_data_ajax_success', [fields, r.data]);
                } else {
                    cpoObj._unblockForm(form, 'error');
                    cpoObj.setPriceTo(cpoObj.priceZeroEl);

                    // Triggers an event - on failure ajax request
                    jQuery(document.body).trigger('cpo_calc_data_ajax_fail', [fields, r]);
                }
            }
        });
    },
    collectData:          function (isForConditional, formFields = {}) {
        const cpoObj           = this;
        let fields             = {};
        const $fieldsToProcess = jQuery(unicpo.options_selector).not('.uni-cpo-excluded-field');

        $fieldsToProcess.each(function () {
            if (!this.name) {
                return;
            }

            const $el    = jQuery(this);
            const elType = this.type || this.tagName.toLowerCase();

            if ('checkbox' === elType) {

            } else if ('radio' === elType) {
                if (jQuery('input[name="' + this.name + '"]:checked').length) {
                    if (true === $el.prop('checked')) {
                        fields[this.name] = $el.val();
                    }
                } else {
                    fields[this.name] = '';
                }
            } else if ('select-one' === elType) {
                fields[this.name] = $el.val();
            } else if ('number' === elType || 'text' === elType) {
                if (!cpoObj.isNumber($el.val())) {
                    const val = $el.val().replace(/,/, '.');
                    $el.val(val);
                    fields[this.name] = $el.val();
                } else {
                    fields[this.name] = $el.val();
                }
                fields[this.name + '_count_spaces'] = fields[this.name].length;
                const withoutSpaces                 = $el.val().replace(/ /g, '');
                fields[this.name + '_count']        = withoutSpaces.length;
            } else if ('textarea' === elType) {
                fields[this.name] = $el.val();
                fields[this.name + '_count_spaces'] = fields[this.name].length;
                const withoutSpaces                 = $el.val().replace(/ /g, '');
                fields[this.name + '_count']        = withoutSpaces.length;
            } else {
                fields[this.name] = $el.val();
            }

            // Triggers an event - for each field
            if (isForConditional) {
                jQuery(document.body).trigger('cpo_option_data_for_conditional', [fields, $el]);
            } else {
                jQuery(document.body).trigger('cpo_option_data_before_validate', [fields, $el]);
            }
        });

        if (isForConditional) {
            const cpoFields = jQuery(document.body).triggerHandler('cpo_options_data_for_conditional', [fields]);
            if (typeof cpoFields !== 'undefined') {
                fields = cpoFields;
            }
            if (! _.isEqual(formFields, fields)) {
                return cpoObj.collectData(true, fields);
            }
        } else {
            const cpoFields = jQuery(document.body).triggerHandler('cpo_options_data_before_validate', [fields]);
            if (typeof cpoFields !== 'undefined') {
                fields = cpoFields;
            }
        }

        return fields;
    },
    getFormattedFormData: function () {
        const pid            = jQuery('.js-cpo-pid').val();
        const $form          = this.addToCartBtnEl.closest('form');
        const $prodQtyInput  = $form.find('.input-text.qty');
        const prodQty        = ( $prodQtyInput.val() ) ? $prodQtyInput.val() : 1;
        let fields           = {};
        fields['product_id'] = pid;
        fields['quantity']   = prodQty;
        fields               = jQuery.extend(fields, this.collectData(false));
        return fields;
    },
    isNumber:             (val) => !isNaN(parseFloat(val)) && isFinite(val),
    processFormData:      function () {
        if (!this.addToCartBtnEl.length) {
            return false;
        }

        const cpoObj  = this;
        const $form   = cpoObj.addToCartBtnEl.closest('form');
        let formValid = false;

        cpoObj.collectData(true);
        cpoObj.setPriceTo(cpoObj.priceZeroEl);

        // Triggers an event - on form data process has been started
        jQuery(document.body).trigger('cpo_form_data_process_start', [cpoObj]);

        // validates
        $form.parsley({excluded: '[disabled], .qty, .uni-cpo-excluded-field'}).validate();
        if ($form.parsley().isValid()) {
            formValid = true;
        }

        let fields = cpoObj.getFormattedFormData(false);

        // validates again
        $form.parsley({excluded: '[disabled], .qty, .uni-cpo-excluded-field'}).validate();
        if ($form.parsley().isValid()) {
            formValid = true;
        }

        if (formValid && fields['product_id']) {
            jQuery(document.body).trigger('cpo_options_data_after_validate_event', [fields]);
            if (this.calc && !this.calcBtn) {
                this.addToCartBtnEl.prop('disabled', true);
            }
            this.calculate(fields);
        } else {
            this.addToCartBtnEl.prop('disabled', true);
            jQuery(document.body).trigger('cpo_options_data_not_valid_event', [fields]);
        }

    },
    setPriceTo:           function (data) {
        this.priceTagEl.html(data);
    },
    _blockForm:           function (el) {
        this._ajax_sent = true;
        jQuery(el).block({
            message:    '<div data-loader="circle"></div>',
            css:        {
                width:  'auto',
                border: '0px'
            },
            overlayCSS: {
                background: '#fff',
                opacity:    0.6
            }
        });
    },
    _unblockForm:         function (el, type) {
        this._ajax_sent = false;
        jQuery(el).unblock();
    },
    _initTooltip: function () {
        jQuery('.uni-builderius-container').tooltip({
            items: '[data-tip]',
            show: {
                effect: "show",
                duration: 0
            },
            hide: {
                effect: 'fad',
                duration: 0
            },
            close: function() {
                jQuery('.ui-helper-hidden-accessible').remove();
            },
            position: {
                my: 'center bottom',
                at: 'center top-10',
                using: function(position, feedback) {
                    jQuery(this).css(position);
                    jQuery(this).addClass('vertical-' + feedback.vertical).addClass('horizontal-' + feedback.horizontal);
                }
            },
            content:  function () {
                return jQuery(this).attr('data-tip');
            }
        });
    }
};

cpoMakeInstance = () => Object.create(UniCpo);
UniCpoInstance     = cpoMakeInstance();

// init
UniCpoInstance._init();