'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

/* UniCpo
----------------------------------------------------------*/

var UniCpo = void 0,
    cpoMakeInstance = void 0;

UniCpo = {
    addToCartAjax: unicpo.ajax_add_to_cart,
    addToCartBtnEl: {},
    addToCartBtnSelector: '.single_add_to_cart_button, button.product_type_simple',
    addedToCartMsg: jQuery('<div class="woocommerce-message"><a href="' + wc_add_to_cart_params.cart_url + '" class="button wc-forward">' + wc_add_to_cart_params.i18n_view_cart + '</a> ' + unicpo_i18n.added_to_cart + '</div>'),
    calc: unicpo.calc_on,
    calcBtn: unicpo.calc_btn_on,
    colorifyImagifyChangers: jQuery('.uni-cpo-colorify-imagify-changer').get(),
    cpo: unicpo.cpo_on,
    isTaxable: unicpo.taxable,
    fileUploadEl: {},
    flatpickrCfg: {},
    geocoder: null,
    isFlexContainer: jQuery('.flex-viewport').length > 0,
    isLayeredOn: unicpo.layered_on,
    isImagify: unicpo.imagify_on,
    isSilentValidationOn: unicpo.silent_validation_on,
    layeredImg: null,
    mainImageChangers: jQuery('.uni-cpo-image-changer').get().reverse(),
    mainImageDefData: {},
    mainImageEl: '',
    orderingDsblMsgEl: jQuery('.js-uni-cpo-ordering-disabled-notice'),
    priceTagEl: {},
    priceStartingEl: jQuery('<span class="js-cpo-price-starting"></span>'),
    priceCalculateEl: jQuery('<span class="js-cpo-calculating"></span>'),
    productFormEl: {},
    progressEl: {},
    priceSuffix: unicpo.price_vars.price_suffix,
    priceTaxSuffixStarting: unicpo.price_vars.price_tax_suffix,
    pricePostfix: unicpo.price_vars.price_postfix,
    priceStarting: unicpo.price_vars.starting_price,
    resetBtn: unicpo.reset_form_btn_on,
    taxPriceSuffixElClass: '.woocommerce-price-suffix',
    _pid: 0,
    _ajax_sent: false,
    _init: function _init() {
        try {
            if (this.cpo) {
                this.addToCartBtnEl = jQuery(this.addToCartBtnSelector);
                if (!this.addToCartBtnEl.length) {
                    console.info('Uni CPO @', 'Add to cart button is not found');
                }
                this.productFormEl = this.addToCartBtnEl.closest('form');
                if (!this.productFormEl.length) {
                    console.info('Uni CPO @', 'Product form is not found');
                } else {
                    this.productFormEl.attr('data-parsley-focus', 'none');
                }

                if (jQuery('.qty').length > 0) {
                    var el = jQuery('.qty')[0];
                    if (R.isEmpty(el.min)) {
                        jQuery('.qty').removeAttr('min');
                    }
                    if (R.isEmpty(el.max)) {
                        jQuery('.qty').removeAttr('max');
                    }
                    jQuery('.qty').attr('data-parsley-trigger', 'change focusout submit');
                }
                if (this.addToCartAjax && this.addToCartBtnEl.length > 0) {
                    this.addToCartBtnEl.attr('type', 'button');
                    this.addToCartBtnEl.addClass('uni_cpo_ajax_add_to_cart');
                }
                this.priceTagEl = jQuery(unicpo.price_selector);
                if (!this.priceTagEl.length) {
                    console.info('Uni CPO @', 'Price tag html element is not found');
                }
                this.priceStartingEl.html(this.getProperPrice());
                this.priceCalculateEl.html(unicpo_i18n.calc_text);

                if (!this.calc) {
                    this.setBtnState(true);
                }
                if (this.calc && this.calcBtn) {
                    this.setBtnState(true);
                    this.setPriceTo({ price: this.priceStartingEl });
                    this.bindOnCalcBtnClick();
                }

                this.bindOnAddToCartClick();
                this.initTooltip();

                if ((typeof google === 'undefined' ? 'undefined' : _typeof(google)) === 'object' && _typeof(google.maps) === 'object') {
                    this.geocoder = new google.maps.Geocoder();
                    this.initGoogleMap();
                } else {
                    console.info('Uni CPO @', 'Google API key is missing or invalid');
                }

                this.initRangeSlider();
                this.bindOnFileUploadClick();
                this.bindOnMatrixCellClick();

                // initial calculation
                var cpoObj = this;
                var interval = setInterval(function () {
                    if (document.readyState === 'complete') {
                        clearInterval(interval);
                        if (!cpoObj.calc || cpoObj.calc && !cpoObj.calcBtn) {
                            cpoObj.processFormData();
                        } else if (cpoObj.calc && cpoObj.calcBtn) {
                            cpoObj.collectData(true);
                        }
                        cpoObj.mainImageEl = cpoObj.getMainImageEl();
                        cpoObj.mainImageDefData = cpoObj.getMainImageDefData();
                        cpoObj.changeMainImage();
                        cpoObj.checkColorifyImagify();
                        jQuery(document.body).trigger('uni_cpo_frontend_is_ready');
                    }
                }, 100);

                
/* Premium Code Stripped by Freemius */


                this.bindOnRadioImageTap();
                this.bindOnRadioColourClick();
                this.bindOnRadioTextClick();
                this.bindOnOptionSelected();
                this.bindImageChangers();
            }
        } catch (e) {
            console.error(e);
        }
    },
    addToCart: function addToCart(fields) {
        var data = {
            action: 'uni_cpo_add_to_cart',
            security: unicpo.security,
            data: fields
        };
        this.ajaxCall(data);
    },
    ajaxCall: function ajaxCall(data) {
        var cpoObj = this;
        var form = cpoObj.productFormEl[0];
        var $wc = jQuery('div.woocommerce').not('.widget');

        jQuery.ajax({
            url: unicpo.ajax_url,
            data: data,
            dataType: 'json',
            method: 'POST',
            beforeSend: function beforeSend() {
                cpoObj._blockForm(form);

                if (cpoObj.calc) {
                    cpoObj.setPriceTo({ price: cpoObj.priceCalculateEl, tax: 'hide' });
                }
                if (cpoObj.addToCartAjax) {
                    $wc.find('.woocommerce-message').slideToggle(500, function () {
                        jQuery(this).remove();
                    });
                }
                // Triggers an event - on before send ajax request
                jQuery(document.body).trigger('uni_cpo_options_data_ajax_before_send', [data.data]);
            },
            error: function error() {
                cpoObj._unblockForm(form, 'error');
                cpoObj.setPriceTo({ price: cpoObj.priceStartingEl });
            },
            success: function success(r) {
                //console.log(r);
                if (data.action === 'uni_cpo_add_to_cart') {
                    var keys = Object.keys(cpoObj.fileUploadEl);
                    if (keys.length) {
                        keys.forEach(function (i) {
                            var slug = jQuery('#' + i).data('slug');
                            cpoObj.fileUploadEl[i].splice();
                            jQuery('#' + slug + '-files-list').empty();
                            jQuery('#' + slug + '-field').val('').trigger('change');
                        });
                    }
                }
                if (r.success) {
                    cpoObj._unblockForm(form, 'success');

                    if (typeof r.data.redirect !== 'undefined') {
                        window.location = r.data.redirect;
                        return;
                    }

                    //unicpo.formatted_vars = r.data.formatted_vars;
                    //unicpo.nice_names_vars = r.data.nice_names_vars;
                    unicpo.formatted_vars = Object.assign({}, r.data.formatted_vars, r.data.nice_names_vars);
                    jQuery.extend(unicpo.price_vars, r.data.price_vars);
                    jQuery.extend(unicpo.extra_data, r.data.extra_data);

                    if (typeof r.data.extra_data !== 'undefined' && typeof r.data.extra_data.order_product !== 'undefined' && r.data.extra_data.order_product === 'disabled') {
                        cpoObj.orderingDsblMsgEl.slideDown(300);
                        cpoObj.setBtnState(true);
                    } else {
                        cpoObj.orderingDsblMsgEl.hide();
                        cpoObj.setBtnState(false);
                    }

                    if (cpoObj.calc) {
                        var calcPrice = cpoObj.getProperPrice({
                            price: unicpo.price_vars.price,
                            doRemoveSuffix: true
                        });
                        if (unicpo.price_vars.raw_price) {
                            cpoObj.setPriceTo({ price: calcPrice, tax: 'show' });
                        } else {
                            cpoObj.setPriceTo({ price: cpoObj.priceStartingEl });
                            cpoObj.setBtnState(true);
                        }
                    }

                    if (typeof r.data.fragments !== 'undefined') {
                        // Redirect to cart option
                        if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
                            window.location = wc_add_to_cart_params.cart_url;
                            return;
                        }

                        $wc.html(cpoObj.addedToCartMsg);

                        jQuery.each(r.data.fragments, function (key, value) {
                            jQuery(key).replaceWith(value);
                        });

                        // Trigger event so themes can refresh other areas.
                        jQuery(document.body).trigger('uni_cpo_added_to_cart', [r.data]);
                    } else {
                        // Triggers an event - on successful ajax request
                        jQuery(document.body).trigger('uni_cpo_options_data_ajax_success', [data.data, r.data]);
                    }
                } else {
                    cpoObj._unblockForm(form, 'error');
                    if (cpoObj.calc) {
                        cpoObj.setPriceTo({ price: cpoObj.priceStartingEl });
                    }

                    if (r.data.product_url) {
                        window.location = r.data.product_url;
                        return;
                    }

                    // Triggers an event - on failure ajax request
                    jQuery(document.body).trigger('uni_cpo_options_data_ajax_fail', [data.data, r]);
                }
            }
        });
    },
    bindImageChangers: function bindImageChangers() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnAddToCartClick: function bindOnAddToCartClick() {
        var cpoObj = this;

        jQuery(document).on('click', '.storefront-sticky-add-to-cart__content-button', function (e) {
            cpoObj.formSubmission();
            e.preventDefault();
        });

        if (!this.addToCartAjax) {
            jQuery(document).on('click', this.addToCartBtnSelector, function (e) {
                e.preventDefault();
                cpoObj.formSubmission();
            });
        } else {
            jQuery(document).on('click', '.uni_cpo_ajax_add_to_cart', function (e) {
                e.preventDefault();
                cpoObj.formSubmission();
            });
        }
    },
    bindOnCalcBtnClick: function bindOnCalcBtnClick() {
        var cpoObj = this;
        jQuery(document).on('click', '.js-uni-cpo-calculate-btn', function () {
            if (cpoObj._ajax_sent) {
                return false;
            }
            cpoObj.processFormData();
        });
    },
    bindOnResetFormBtnClick: function bindOnResetFormBtnClick() {
        var cpoObj = this;
        jQuery(document).on('click', '.js-uni-cpo-reset-form-btn', function () {
            var $fieldsToProcess = jQuery(unicpo.options_selector);

            $fieldsToProcess.each(function () {
                if (!this.name) {
                    return;
                }

                var el = this;
                var $el = jQuery(el);
                var $module = $el.closest('.uni-module');
                var elType = el.type || el.tagName.toLowerCase();

                if ('checkbox' === elType || 'radio' === elType) {
                    $el.attr('checked', false);
                    cpoObj.layeredImg = '';
                } else if ('select-one' === elType || 'textarea' === elType) {
                    $el.val('');
                } else if ('number' === elType || 'text' === elType) {
                    if ($el.hasClass('js-uni-cpo-field-range_slider')) {
                        var slider = $el.data('ionRangeSlider');
                        var min = $el.attr('data-min');
                        var max = $el.attr('data-max');
                        var $input = $el.siblings(jQuery('.js-uni-cpo-field-range_slider-additional-field'));

                        slider.update({
                            from: min,
                            to: max
                        });
                        $input.val(min);
                        return;
                    } else if ($el.hasClass('flatpickr-input')) {
                        var fp = el._flatpickr;
                        fp.clear();
                    }

                    $el.val('');
                } else if ('hidden' === elType) {
                    if ($module.hasClass('uni-module-matrix')) {
                        $module.find('.uni-clicked').removeClass('uni-clicked');
                    }
                    if ($module.hasClass('uni-module-file_upload')) {
                        var $btn = $module.find('.uni-cpo-file-upload-files-item-remove');

                        $btn.each(function () {
                            jQuery(this).trigger('click');
                        });
                    }
                    $el.val('');
                } else {
                    $el.val('');
                }
            });
            cpoObj.processFormData();
        });
    },
    bindOnFileUploadClick: function bindOnFileUploadClick() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnOptionSelected: function bindOnOptionSelected() {
        var cpoObj = this;
        jQuery(document).on('change', unicpo.options_selector_change, function () {
            if (cpoObj._ajax_sent) {
                return false;
            }
            if (!cpoObj.calc || cpoObj.calc && !cpoObj.calcBtn) {
                cpoObj.processFormData();
            } else if (cpoObj.calc && cpoObj.calcBtn) {
                cpoObj.setBtnState(true);
                cpoObj.collectData(true);
            }
        });
        
/* Premium Code Stripped by Freemius */

    },
    bindOnResetRadioBtnClick: function bindOnResetRadioBtnClick() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnRadioColourClick: function bindOnRadioColourClick() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnRadioTextClick: function bindOnRadioTextClick() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnRadioImageTap: function bindOnRadioImageTap() {
        
/* Premium Code Stripped by Freemius */

    },
    bindOnMatrixCellClick: function bindOnMatrixCellClick() {
        
/* Premium Code Stripped by Freemius */

    },
    calculate: function calculate(fields) {
        var data = {
            action: 'uni_cpo_price_calc',
            security: unicpo.security,
            data: fields
        };

        this.ajaxCall(data);
    },
    changeMainImage: function changeMainImage() {
        
/* Premium Code Stripped by Freemius */

    },
    checkColorifyImagify: function checkColorifyImagify() {
        
/* Premium Code Stripped by Freemius */

    },
    collectData: function collectData(isForConditional) {
        var formFields = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

        var cpoObj = this;
        var fields = {};
        var $fieldsToProcess = jQuery(unicpo.options_selector).not('.uni-cpo-excluded-field');

        $fieldsToProcess.each(function () {
            if (!this.name) {
                return;
            }

            var el = this;
            var $el = jQuery(el);
            var elType = el.type || el.tagName.toLowerCase();

            if ('checkbox' === elType) {
                var name = el.name.replace('[]', '');
                if (typeof fields[name] !== 'undefined') {
                    return;
                }
                fields[name] = jQuery.makeArray(fields[name]);

                jQuery('input[name="' + el.name + '"]:checked').each(function () {
                    fields[name].push(this.value);
                });
                fields[name + '_count'] = fields[name].length;
            } else if ('radio' === elType) {
                if (jQuery('input[name="' + el.name + '"]:checked').length) {
                    if (true === $el.prop('checked')) {
                        fields[el.name] = $el.val();
                    }
                } else {
                    fields[el.name] = '';
                }
            } else if ('select-one' === elType) {
                fields[el.name] = $el.val();
            } else if ('number' === elType || 'text' === elType) {
                if ($el.hasClass('js-uni-cpo-field-datepicker')) {
                    var fp = document.getElementById(el.name + '-field')._flatpickr;
                    var mode = fp.config.mode;
                    var isDatepickerDisabled = fp.config.noCalendar;
                    var isTimepicker = fp.config.enableTime;
                    var isTime24hr = fp.config.time_24hr;

                    if (isDatepickerDisabled && isTimepicker) {
                        var startTime = moment(fp.selectedDates[0]);
                        if (isTime24hr) {
                            fields[el.name] = startTime.format('H:mm');
                            fields[el.name + '_start'] = startTime.format('H:mm');
                        } else {
                            fields[el.name] = startTime.format('h:mm a');
                            fields[el.name + '_start'] = startTime.format('h:mm a');
                        }
                    } else {
                        var startDate = moment(fp.selectedDates[0]);
                        if (mode === 'single') {
                            if (isTimepicker) {
                                if (isTime24hr) {
                                    fields[el.name] = startDate.format('Y-MM-DD H:mm');
                                    fields[el.name + '_start'] = startDate.format('Y-MM-DD H:mm');
                                } else {
                                    fields[el.name] = startDate.format('Y-MM-DD h:mm a');
                                    fields[el.name + '_start'] = startDate.format('Y-MM-DD h:mm a');
                                }
                            } else {
                                fields[el.name] = startDate.format('Y-MM-DD');
                                fields[el.name + '_start'] = startDate.format('Y-MM-DD');
                            }
                            fields[el.name + '_duration'] = 1;
                        } else if (mode === 'range') {
                            var endDate = moment(fp.selectedDates[1]);
                            if (fp.selectedDates.length) {
                                if (isTimepicker) {
                                    if (isTime24hr) {
                                        fields[el.name] = startDate.format('Y-MM-DD H:mm') + ' - ' + endDate.format('Y-MM-DD H:mm');
                                        fields[el.name + '_start'] = startDate.format('Y-MM-DD H:mm');
                                        fields[el.name + '_end'] = endDate.format('Y-MM-DD H:mm');
                                    } else {
                                        fields[el.name] = startDate.format('Y-MM-DD h:mm a') + ' - ' + endDate.format('Y-MM-DD h:mm a');
                                        fields[el.name + '_start'] = startDate.format('Y-MM-DD h:mm a');
                                        fields[el.name + '_end'] = endDate.format('Y-MM-DD h:mm a');
                                    }
                                } else {
                                    fields[el.name] = startDate.format('Y-MM-DD') + ' - ' + endDate.format('Y-MM-DD');
                                    fields[el.name + '_start'] = startDate.format('Y-MM-DD');
                                    fields[el.name + '_end'] = endDate.format('Y-MM-DD');
                                }
                                fields[el.name + '_duration'] = endDate.diff(startDate, 'days');
                                if ($el.hasClass('js-datepicker-day-night-mode-days')) {
                                    fields[el.name + '_duration'] = fields[el.name + '_duration'] + 1;
                                }
                            }
                        } else if (mode === 'multiple') {
                            if (fp.selectedDates.length) {
                                fields[el.name] = $el.val();
                                fields[el.name + '_duration'] = fp.selectedDates.length;
                                fields[el.name + '_days'] = [];

                                fp.selectedDates.forEach(function (item, i) {
                                    var day = void 0;
                                    if (isTimepicker) {
                                        if (isTime24hr) {
                                            day = moment(item).format('Y-MM-DD H:mm');
                                        } else {
                                            day = moment(item).format('Y-MM-DD h:mm a');
                                        }
                                    } else {
                                        day = moment(item).format('Y-MM-DD');
                                    }
                                    fields[el.name + '_days'].push(day);
                                });
                            }
                        }
                    }
                } else if ($el.hasClass('js-uni-cpo-field-range_slider')) {
                    var slider = $el.data('ionRangeSlider');
                    if ('double' === slider.options.type) {
                        var values = $el.val().split('-');
                        fields[el.name] = $el.val();
                        fields[el.name + '_from'] = values[0];
                        fields[el.name + '_to'] = values[1];
                    } else {
                        fields[el.name] = $el.val();
                        fields[el.name + '_from'] = $el.val();
                        fields[el.name + '_to'] = $el.val();
                    }
                } else if ($el.hasClass('js-uni-cpo-field-google_map')) {
                    var $latlng = $el.prev();

                    fields[el.name] = $el.val();
                    fields[el.name + '_latlng'] = $latlng.val();
                } else {
                    if (cpoObj.isNumber($el.val())) {
                        var val = $el.val().replace(/,/, '.');
                        $el.val(val);
                        fields[el.name] = $el.val();
                    } else {
                        fields[el.name] = $el.val();
                    }
                    fields[el.name + '_count_spaces'] = fields[el.name].length;
                    var withoutSpaces = $el.val().replace(/ /g, '');
                    fields[el.name + '_count'] = withoutSpaces.length;
                }
            } else if ('textarea' === elType) {
                fields[el.name] = $el.val();
                fields[el.name + '_count_spaces'] = fields[el.name].length;
                var _withoutSpaces = $el.val().replace(/ /g, '');
                fields[el.name + '_count'] = _withoutSpaces.length;
            } else if ('hidden' === elType) {
                if ($el.hasClass('js-uni-cpo-field-file_upload')) {
                    var data = $el.data();
                    fields[el.name] = $el.val();
                    if (typeof data.imageWidth !== 'undefined') {
                        fields[el.name + '_width'] = parseInt(data.imageWidth);
                    }
                    if (typeof data.imageHeight !== 'undefined') {
                        fields[el.name + '_height'] = parseInt(data.imageHeight);
                    }
                } else {
                    fields[el.name] = $el.val();
                }
            } else {
                fields[el.name] = $el.val();
            }

            // Triggers an event - for each field
            if (isForConditional) {
                jQuery(document.body).trigger('uni_cpo_option_data_for_conditional', [fields, $el]);
            } else {
                jQuery(document.body).trigger('uni_cpo_option_data_before_validate', [fields, $el]);
            }
        });

        
/* Premium Code Stripped by Freemius */


        if (isForConditional) {
            var cpoFields = jQuery(document.body).triggerHandler('uni_cpo_options_data_for_conditional', [fields]);
            if (typeof cpoFields !== 'undefined') {
                fields = cpoFields;
            }
            if (!_.isEqual(formFields, fields)) {
                return cpoObj.collectData(true, fields);
            }
        } else {
            var _cpoFields = jQuery(document.body).triggerHandler('uni_cpo_options_data_before_validate', [fields]);
            if (typeof _cpoFields !== 'undefined') {
                fields = _cpoFields;
            }
        }

        return fields;
    },
    colorify: function colorify(optionName, hex) {
        
/* Premium Code Stripped by Freemius */

    },
    combineImg: function combineImg() {
        
/* Premium Code Stripped by Freemius */

    },
    loadImage: function loadImage(imagePath) {
        
/* Premium Code Stripped by Freemius */

    },
    imagify: function imagify(optionName, image) {
        
/* Premium Code Stripped by Freemius */

    },
    formSubmission: function formSubmission() {
        var cpoObj = this;
        var excluded = '[disabled], .uni-cpo-excluded-field';

        // validates the form
        cpoObj.productFormEl.parsley({
            excluded: excluded
        }).validate();

        if (cpoObj.productFormEl.parsley().isValid()) {
            var $excludeFromFormSubmission = jQuery('.uni-cpo-excluded-field');
            $excludeFromFormSubmission.each(function () {
                jQuery(this).prop('disabled', true);
            });

            jQuery('input[name="cpo_product_layered_image"]').val(cpoObj.layeredImg);

            // regular form submission or via ajax
            if (!cpoObj.addToCartAjax) {
                cpoObj.productFormEl.submit();
            } else {
                var data = {};
                jQuery.each(cpoObj.productFormEl.serializeArray(), function (index, item) {
                    if (item.name.indexOf('[]') !== -1) {
                        item.name = item.name.replace('[]', '');
                        data[item.name] = jQuery.makeArray(data[item.name]);
                        data[item.name].push(item.value);
                    } else {
                        data[item.name] = item.value;
                    }
                });
                //console.log(data, cpoObj.productFormEl.serializeArray());

                cpoObj.addToCart(data);
            }

            $excludeFromFormSubmission.each(function () {
                jQuery(this).prop('disabled', false);
            });
        }
    },
    getMainImageDefData: function getMainImageDefData() {
        
/* Premium Code Stripped by Freemius */

    },
    getMainImageEl: function getMainImageEl() {
        var $image = jQuery(unicpo.image_selector).find('.woocommerce-product-gallery__image');
        return $image.length > 0 ? $image.not('.clone').first() : '';
    },
    getFormattedFormData: function getFormattedFormData() {
        var pid = jQuery('.js-cpo-pid').val();
        var $prodQtyInput = this.productFormEl.find('.input-text.qty');
        var prodQty = $prodQtyInput.val() ? $prodQtyInput.val() : 1;
        var fields = {};
        fields['product_id'] = pid;
        fields['quantity'] = parseInt(prodQty);
        fields = jQuery.extend(fields, this.collectData(false));
        return fields;
    },
    handlePluploadInit: function handlePluploadInit(uploader) {
        
/* Premium Code Stripped by Freemius */

    },
    handlePluploadError: function handlePluploadError(uploader, error) {
        
/* Premium Code Stripped by Freemius */

    },
    handlePluploadFileFiltered: function handlePluploadFileFiltered(uploader, file) {},
    handlePluploadFilesAdded: function handlePluploadFilesAdded(uploader, files) {
        
/* Premium Code Stripped by Freemius */

    },
    handlePluploadBeforeUpload: function handlePluploadBeforeUpload(uploader, file) {
        
/* Premium Code Stripped by Freemius */

    },
    handlePluploadUploadProgress: function handlePluploadUploadProgress(uploader, file) {
        
/* Premium Code Stripped by Freemius */

    },
    handlePluploadChunkUploaded: function handlePluploadChunkUploaded(uploader, file, r) {
        var parsedResponse = JSON.parse(r.response);
        if (!parsedResponse.success) {
            var slug = uploader.settings.multipart_params.slug;
            var $option = jQuery('#' + slug + '-field');
            uploader.stop();
            $option.parsley().addError('file-upload', { message: parsedResponse.data.error });
            window.UniCpo.position($option, 0);
        }
    },
    handlePluploadFileUploaded: function handlePluploadFileUploaded(uploader, file, r) {
        
/* Premium Code Stripped by Freemius */

    },
    hexToRgb: function hexToRgb(hex) {
        var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
        hex = hex.replace(shorthandRegex, function (m, r, g, b) {
            return r + r + g + g + b + b;
        });

        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    },
    gMapMarkers: {},
    addGoogleMarker: function addGoogleMarker(location, map, mapId) {
        
/* Premium Code Stripped by Freemius */

    },
    initGoogleMap: function initGoogleMap() {
        
/* Premium Code Stripped by Freemius */

    },
    initRangeSlider: function initRangeSlider() {
        
/* Premium Code Stripped by Freemius */

    },
    isMobile: function isMobile() {
        var Uagent = navigator.userAgent || navigator.vendor || window.opera;
        return (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(Uagent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(Uagent.substr(0, 4))
        );
    },
    initTooltip: function initTooltip() {
        if (this.isMobile()) {
            return;
        }
        jQuery('.uni-builderius-container').tooltip({
            items: '[data-tip]',
            show: {
                effect: 'show',
                duration: 0
            },
            hide: {
                effect: 'fad',
                duration: 0
            },
            close: function close() {
                jQuery('.ui-helper-hidden-accessible').remove();
            },
            position: {
                my: 'center bottom',
                at: 'center top-10',
                collision: 'none',
                using: function using(position, feedback) {
                    jQuery(this).css(position);
                    jQuery(this).addClass('vertical-bottom horizontal-center');
                }
            },
            content: function content() {
                return jQuery(this).attr('data-tip');
            }
        });
    },
    isNumber: function isNumber(val) {
        return !isNaN(parseFloat(val)) && isFinite(val);
    },
    position: function position(el, delay) {
        var time = void 0;
        var $parent = el;
        var $list = void 0;

        if (typeof delay !== 'undefined') {
            time = delay;
        } else {
            time = 300;
        }

        if (el.hasClass('uni-module')) {
            $list = el.find('.parsley-errors-list');
        } else {
            $list = jQuery('#parsley-id-' + $parent.data('parsley-id'));
            if ($parent.attr('type') === 'hidden') {
                $parent = jQuery('' + el.data('parsley-class-handler'));
            }
        }

        if (!el.hasClass('parsley-error') && typeof el.data('parsley-errors-container') !== 'undefined' && typeof el.data('parsley-class-handler') !== 'undefined' && el.attr('type') !== 'hidden') {

            $parent = jQuery('' + el.data('parsley-class-handler'));
            $list = jQuery('[id="parsley-id-multiple-' + $parent.attr('id') + '"]');
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
        }, time);
    },
    processFormData: function processFormData() {
        if (!this.addToCartBtnEl.length) {
            return false;
        }

        var cpoObj = this;
        var formValid = false;

        cpoObj.collectData(true);
        if (this.calc) {
            cpoObj.setPriceTo({ price: cpoObj.priceStartingEl });
        }

        // Triggers an event - on form data process has been started
        jQuery(document.body).trigger('uni_cpo_form_data_process_start', [cpoObj]);

        // validates
        cpoObj.productFormEl.parsley({ excluded: '[disabled], .qty, .uni-cpo-excluded-field' }).validate();
        if (cpoObj.productFormEl.parsley().isValid()) {
            formValid = true;
        }

        var fields = cpoObj.getFormattedFormData(false);

        // validates again
        cpoObj.productFormEl.parsley({ excluded: '[disabled], .qty, .uni-cpo-excluded-field' }).validate();
        if (cpoObj.productFormEl.parsley().isValid()) {
            formValid = true;
        }

        if (formValid && fields['product_id']) {
            jQuery(document.body).trigger('uni_cpo_options_data_after_validate_event', [fields]);
            if (!cpoObj.calc) {
                cpoObj.setBtnState(false);
            }
            if (cpoObj.calc && !cpoObj.calcBtn) {
                cpoObj.setBtnState(true);
            }
            if (cpoObj.calc) {
                cpoObj.calculate(fields);
            }
        } else {
            cpoObj.setBtnState(true);
            jQuery(document.body).trigger('uni_cpo_options_data_not_valid_event', [fields]);
        }
    },
    replaceMainImageData: function replaceMainImageData(data) {
        
/* Premium Code Stripped by Freemius */

    },
    parsleyRemoveError: function parsleyRemoveError(el) {
        el.parsley().removeError('file-limit');
        el.parsley().removeError('file-type');
        el.parsley().removeError('file-size');
        el.parsley().removeError('file-custom');
        el.parsley().removeError('file-upload');
    },
    setBtnState: function setBtnState(state) {
        this.addToCartBtnEl.prop('disabled', state);
        jQuery(document.body).trigger('uni_cpo_set_btn_state_event', [state]);
    },
    setPriceTo: function setPriceTo(_ref) {
        var price = _ref.price,
            _ref$tax = _ref.tax,
            tax = _ref$tax === undefined ? 'starting' : _ref$tax;

        var cpoObj = this;
        var $el = cpoObj.priceTagEl;
        $el.each(function (i, elInList) {
            jQuery(elInList).parent().each(function (k, elInListParent) {
                if (jQuery(elInListParent)[0].tagName === 'DEL') {
                    jQuery(elInListParent).find(elInList).show();
                } else {
                    jQuery(elInListParent).find(elInList).each(function () {
                        var cloned = price;
                        if ((typeof price === 'undefined' ? 'undefined' : _typeof(price)) === 'object') {
                            cloned = price.clone();
                        }
                        jQuery(this).html(cloned).show();
                        jQuery(document.body).trigger('uni_cpo_set_price_event', [cloned]);
                    });
                    if (cpoObj.isTaxable && tax !== 'hide') {
                        var $taxSuffixEl = jQuery(elInListParent).find(cpoObj.taxPriceSuffixElClass);
                        var $newTaxSuffixEl = jQuery(cpoObj.getProperTaxSuffix(tax));
                        $taxSuffixEl.replaceWith($newTaxSuffixEl);
                        $newTaxSuffixEl.show();
                        $newTaxSuffixEl.find('span').show();
                    } else {
                        jQuery(elInListParent).find(cpoObj.taxPriceSuffixElClass).hide();
                    }
                }
            });
        });
    },
    showFirstThumbOnImageChange: function showFirstThumbOnImageChange() {
        
/* Premium Code Stripped by Freemius */

    },
    showLastThumb: function showLastThumb() {
        
/* Premium Code Stripped by Freemius */

    },
    getProperPrice: function getProperPrice() {
        var _ref2 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
            _ref2$price = _ref2.price,
            price = _ref2$price === undefined ? '' : _ref2$price,
            _ref2$doRemoveSuffix = _ref2.doRemoveSuffix,
            doRemoveSuffix = _ref2$doRemoveSuffix === undefined ? false : _ref2$doRemoveSuffix;

        var initPrice = this.priceStarting && !price ? this.priceStarting : price ? price : unicpo.price_vars.price;
        
/* Premium Code Stripped by Freemius */


        return initPrice;
    },
    getProperTaxSuffix: function getProperTaxSuffix(tax) {
        if (tax === 'starting') {
            return this.priceTaxSuffixStarting;
        } else if (tax === 'show') {
            return unicpo.price_vars.price_tax_suffix;
        }
    },
    template: _.memoize(function (id) {
        var compiled = void 0;
        var options = {
            evaluate: /\{#([\s\S]+?)#\}/g,
            interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
            escape: /\{\{([^\}]+?)\}\}(?!\})/g,
            variable: 'data'
        };

        return function (data) {
            compiled = compiled || _.template(jQuery('#cpo-tmpl-' + id).html(), options);
            return compiled(data);
        };
    }),
    _blockForm: function _blockForm(el) {
        this._ajax_sent = true;
        jQuery(el).block({
            message: '<div data-loader="circle"></div>',
            css: {
                width: 'auto',
                border: '0px'
            },
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    },
    _unblockForm: function _unblockForm(el, type) {
        this._ajax_sent = false;
        jQuery(el).unblock();
    },
    isProp: function isProp(obj, prop) {
        return obj[prop] != null && typeof obj[prop] !== 'undefined';
    },
    isDateBetween: function isDateBetween(start, end, target) {
        var d1 = Date.parse(start);
        var d2 = Date.parse(end);
        var dTarget = Date.parse(target);
        return dTarget >= d1 && dTarget <= d2;
    }
};

cpoMakeInstance = function cpoMakeInstance() {
    return Object.create(UniCpo);
};
window.UniCpo = cpoMakeInstance();

// init
window.UniCpo._init();

/* Custom ParsleyJS validators
----------------------------------------------------------*/

window.Parsley.addValidator('maxFileSize', {
    validateString: function validateString(_value, maxSize, parsleyInstance) {
        if (!window.FormData) {
            console.log('The browser does not support this feature');
            return true;
        }
        if (0 === maxSize) {
            maxSize = unicpo.max_file_size;
        }
        var files = parsleyInstance.$element[0].files;
        return files.length !== 1 || files[0].size <= maxSize * 1024;
    },
    requirementType: 'integer',
    messages: {
        en: unicpo_i18n.max_file_size
    }
});

window.Parsley.addValidator('mimeType', {
    validateString: function validateString(value, requirement, parsleyInstance) {

        var files = parsleyInstance.$element[0].files;
        if (0 === files.length) {
            return true;
        }

        if ('' === requirement) {
            requirement = unicpo.mime_type;
        }
        var allowedMimeTypes = requirement.replace(/\s/g, '').split(',');
        return allowedMimeTypes.indexOf(files[0].type) !== -1;
    },
    requirementType: 'string',
    messages: {
        en: unicpo_i18n.mime_type
    }
});

window.Parsley.addValidator('greaterorequalthan', {
    requirementType: 'string',

    validateNumber: function validateNumber(value, requirement) {
        var comp_value = jQuery(requirement).val();
        return value >= comp_value;
    },


    messages: {
        en: 'This value should be greater or equal than %s'
    }
});

window.Parsley.addValidator('greaterthan', {
    requirementType: 'string',

    validateNumber: function validateNumber(value, requirement) {
        var comp_value = jQuery(requirement).val();
        return value > comp_value;
    },


    messages: {
        en: 'This value should be greater than %s'
    }
});

window.Parsley.addValidator('lessorequalthan', {
    requirementType: 'string',

    validateNumber: function validateNumber(value, requirement) {
        var comp_value = jQuery(requirement).val();
        return value <= comp_value;
    },


    messages: {
        en: 'This value should be less or equal than %s'
    }
});

window.Parsley.addValidator('lessthan', {
    requirementType: 'string',

    validateNumber: function validateNumber(value, requirement) {
        var comp_value = jQuery(requirement).val();
        return value < comp_value;
    },


    messages: {
        en: 'This value should be less than %s'
    }
});

window.Parsley.on('field:error', function () {
    if (window.UniCpo.isSilentValidationOn) {
        this.removeError('required');
    }
    window.UniCpo.position(this.$element);
});

/* Custom JS functions-helpers
----------------------------------------------------------*/

/* Custom JS functions-helpers to be used in Dynamic Notice
----------------------------------------------------------*/
var uniData = function uniData(data) {
    return {
        data: data,
        init: function init() {
            if (typeof data !== 'undefined') {
                Object.assign(this, data);
            }
        },
        checkExistance: function checkExistance(prop) {
            if (typeof this[prop] !== 'undefined') {
                return true;
            } else {
                return false;
            }
        },
        getLabel: function getLabel(prop) {
            if (typeof unicpoAllOptions[prop] !== 'undefined' && typeof unicpoAllOptions[prop]['label'] !== 'undefined') {
                return unicpoAllOptions[prop]['label'];
            }

            return '';
        },
        getSuboptionLabel: function getSuboptionLabel(prop) {
            if (typeof unicpoAllOptions[prop] !== 'undefined' && typeof unicpoAllOptions[prop].suboptions[this[prop]] !== 'undefined') {
                return unicpoAllOptions[prop].suboptions[this[prop]]['label'];
            } else if (typeof unicpoAllOptions[prop] !== 'undefined' && Array.isArray(this[prop])) {
                var label = '';

                this[prop].forEach(function (item) {
                    label += ', ' + unicpoAllOptions[prop].suboptions[item]['label'];
                });

                return label.substr(2);
            }

            return '';
        },
        asPrice: function asPrice(prop) {
            var exists = this.checkExistance(prop);
            return exists && typeof this[prop] !== 'undefined' ? parseFloat(this[prop]).toFixed(2) : parseFloat(0).toFixed(2);
        },
        asNumber: function asNumber(prop) {
            var precision = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 2;

            var exists = this.checkExistance(prop);
            return exists && typeof this[prop] !== 'undefined' ? parseFloat(this[prop]).toFixed(precision) : parseFloat(0).toFixed(precision);
        }
    };
};