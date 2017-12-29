'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* UniCpo
----------------------------------------------------------*/

var UniCpo = void 0,
    cpoMakeInstance = void 0;

UniCpo = {
    addToCartAjax: 'on' === unicpo.ajax_add_to_cart,
    addToCartBtnEl: {},
    addToCartBtnSelector: '.single_add_to_cart_button, button.product_type_simple',
    addedToCartMsg: jQuery('<div class="woocommerce-message"><a href="' + wc_add_to_cart_params.cart_url + '" class="button wc-forward">' + wc_add_to_cart_params.i18n_view_cart + '</a> ' + unicpo_i18n.added_to_cart + '</div>'),
    calc: unicpo.calc_on,
    calcBtn: unicpo.calc_btn_on,
    cpo: unicpo.cpo_on,
    fileUploadEl: {},
    flatpickrCfg: {},
    isFlexContainer: jQuery('.flex-viewport').length > 0,
    mainImageChangers: jQuery('.uni-cpo-image-changer').get().reverse(),
    mainImageDefData: {},
    mainImageEl: '',
    priceTagEl: {},
    priceZeroEl: jQuery('<span class="js-cpo-text-zero-pice"></span>'),
    priceCalculateEl: jQuery('<span class="js-cpo-calculating"></span>'),
    productFormEl: {},
    progressEl: {},
    _pid: 0,
    _ajax_sent: false,
    _init: function _init() {
        try {
            if (this.cpo) {
                this.addToCartBtnEl = jQuery(this.addToCartBtnSelector);
                if (!this.addToCartBtnEl.length) {
                    console.info('Add to cart button is not found');
                }
                this.productFormEl = this.addToCartBtnEl.closest('form');
                if (!this.productFormEl.length) {
                    console.info('Product form is not found');
                }
                if (this.addToCartAjax && this.addToCartBtnEl.length > 0) {
                    this.addToCartBtnEl.attr('type', 'button');
                    this.addToCartBtnEl.addClass('cpo_ajax_add_to_cart');
                }
                this.priceTagEl = jQuery(unicpo.price_selector);
                if (!this.priceTagEl.length) {
                    console.info('Price tag html element is not found');
                }
                this.priceZeroEl.html(unicpo.price_vars.price);
                this.priceCalculateEl.html(unicpo_i18n.calc_text);

                if (!this.calc) {
                    this.setBtnState(true);
                }
                if (this.calc && this.calcBtn) {
                    this.setBtnState(true);
                    this.setPriceTo(this.priceZeroEl);
                    this.bindOnCalcBtnClick();
                }
                this.bindOnAddToCartClick();
                this.initTooltip();
                this.bindOnFileUploadClick();

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
                    }
                }, 100);

                var _unicpo_i18n$flatpick = unicpo_i18n.flatpickr,
                    weekdays = _unicpo_i18n$flatpick.weekdays,
                    months = _unicpo_i18n$flatpick.months,
                    scrollTitle = _unicpo_i18n$flatpick.scrollTitle,
                    toggleTitle = _unicpo_i18n$flatpick.toggleTitle;

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

                this.bindOnOptionSelected();
                this.bindMainImageChange();
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
                cpoObj.setPriceTo(cpoObj.priceCalculateEl);
                if (cpoObj.addToCartAjax) {
                    $wc.find('.woocommerce-message').slideToggle(500, function () {
                        jQuery(this).remove();
                    });
                }
                // Triggers an event - on before send ajax request
                jQuery(document.body).trigger('cpo_options_data_ajax_before_send', [data.data]);
            },
            error: function error() {
                cpoObj._unblockForm(form, 'error');
                cpoObj.setPriceTo(cpoObj.priceZeroEl);
            },
            success: function success(r) {
                //console.log(r);
                if (r.success) {
                    cpoObj._unblockForm(form, 'success');

                    unicpo.formatted_vars = r.data.formatted_vars;
                    jQuery.extend(unicpo.price_vars, r.data.price_vars);
                    jQuery.extend(unicpo.extra_data, r.data.extra_data);

                    cpoObj.setPriceTo(unicpo.price_vars.price);
                    cpoObj.setBtnState(false);

                    // Triggers an event - on successful ajax request
                    jQuery(document.body).trigger('cpo_options_data_ajax_success', [data.data, r.data]);

                    if (typeof r.data.fragments !== 'undefined') {
                        // Redirect to cart option
                        if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
                            window.location = wc_add_to_cart_params.cart_url;
                            return;
                        }

                        $wc.html(cpoObj.addedToCartMsg);

                        // Trigger event so themes can refresh other areas.
                        jQuery(document.body).trigger('cpo_added_to_cart', [r.fragments, r.cart_hash]);
                    }
                } else {
                    cpoObj._unblockForm(form, 'error');
                    cpoObj.setPriceTo(cpoObj.priceZeroEl);

                    if (r.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    // Triggers an event - on failure ajax request
                    jQuery(document.body).trigger('cpo_options_data_ajax_fail', [data.data, r]);
                }
            }
        });
    },
    bindOnAddToCartClick: function bindOnAddToCartClick() {
        var cpoObj = this;

        if (!this.addToCartAjax) {
            jQuery(document).on('click', this.addToCartBtnSelector, function (e) {
                e.preventDefault();
                cpoObj.formSubmission();
            });
        } else {
            jQuery(document).on('click', '.cpo_ajax_add_to_cart', function (e) {
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
    bindOnFileUploadClick: function bindOnFileUploadClick() {
        var cpoObj = this;
        var $fileUploadFields = jQuery('.js-uni-cpo-field-file_upload-el');

        if ($fileUploadFields.length) {
            $fileUploadFields.each(function () {
                var $el = jQuery(this);
                var id = $el.attr('id');
                var listId = $el.siblings('.js-uni-cpo-file-upload-files').attr('id');

                var _$el$data = $el.data(),
                    postId = _$el$data.postId,
                    slug = _$el$data.slug,
                    maxFilesize = _$el$data.maxFilesize,
                    mimeTypes = _$el$data.mimeTypes;

                cpoObj.fileUploadEl[id] = new plupload.Uploader({
                    max_files: 1,
                    multi_selection: false,
                    runtimes: 'html5',
                    url: unicpo.ajax_url,
                    browse_button: id,
                    chunk_size: '1mb',
                    filters: {
                        max_file_size: maxFilesize ? maxFilesize + 'mb' : unicpo.max_file_size + 'mb',
                        mime_types: [{
                            title: 'Allowed formats',
                            extensions: mimeTypes ? mimeTypes : unicpo.mime_types
                        }]
                    },
                    listId: listId,
                    uploadMode: 'single',
                    multipart_params: {
                        action: 'uni_cpo_upload_file',
                        postId: postId,
                        slug: slug,
                        security: unicpo.security
                    }
                });

                cpoObj.fileUploadEl[id].bind('PostInit', cpoObj.handlePluploadInit);
                cpoObj.fileUploadEl[id].bind('Error', cpoObj.handlePluploadError);
                cpoObj.fileUploadEl[id].bind('FileFiltered', cpoObj.handlePluploadFileFiltered);
                cpoObj.fileUploadEl[id].bind('FilesAdded', cpoObj.handlePluploadFilesAdded);
                cpoObj.fileUploadEl[id].bind('BeforeUpload', cpoObj.handlePluploadBeforeUpload);
                cpoObj.fileUploadEl[id].bind('UploadProgress', cpoObj.handlePluploadUploadProgress);
                cpoObj.fileUploadEl[id].bind('ChunkUploaded', cpoObj.handlePluploadChunkUploaded);
                cpoObj.fileUploadEl[id].bind('FileUploaded', cpoObj.handlePluploadFileUploaded);
                cpoObj.fileUploadEl[id].init();
            });
        }
    },
    bindMainImageChange: function bindMainImageChange() {
        var cpoObj = this;

        jQuery(document).on('change', cpoObj.mainImageChangers, function () {
            cpoObj.changeMainImage();
        });
        jQuery(document).on('click', '.flex-control-thumbs li:first-of-type img', function () {
            cpoObj.replaceMainImageData(cpoObj.mainImageDefData);
        });
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
        if (!this.mainImageEl || !this.mainImageChangers.length) {
            return;
        }

        var $firstChosenEl = void 0;
        this.mainImageChangers.forEach(function (el) {
            if (typeof $firstChosenEl === 'undefined') {
                var $el = jQuery(el);
                var type = $el.attr('type');

                if (!$el.hasClass('uni-cpo-excluded-field')) {
                    if ('radio' === type || 'checkbox' === type) {
                        if (true === $el.prop('checked')) {
                            $firstChosenEl = $el;
                        }
                    } else {
                        $firstChosenEl = $el;
                    }
                }
            }
        });

        var data = typeof $firstChosenEl !== 'undefined' ? $firstChosenEl.data() : this.mainImageDefData;

        this.replaceMainImageData(data);
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

            var $el = jQuery(this);
            var elType = this.type || this.tagName.toLowerCase();

            if ('checkbox' === elType) {
                var checkboxes = [];
                var name = this.name.slice(0, -2);

                if (typeof fields[name] !== 'undefined') {
                    return;
                }

                jQuery('input[name="' + this.name + '"]:checked').each(function () {
                    checkboxes.push(jQuery(this).val());
                });
                fields[name] = checkboxes;
                fields[name + '_count'] = fields[name].length;
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
                if ($el.hasClass('js-uni-cpo-field-datepicker')) {
                    var fp = document.getElementById(this.name + '-field')._flatpickr;
                    if (fp.selectedDates.length) {
                        var startDate = moment(fp.selectedDates[0]);
                        fields[this.name] = startDate.format('Y-MM-DD');
                        fields[this.name + '_start'] = startDate.format('Y-MM-DD');
                        if (fp.selectedDates[1]) {
                            var endDate = moment(fp.selectedDates[1]);
                            fields[this.name] = startDate.format('Y-MM-DD') + ' - ' + endDate.format('Y-MM-DD');
                            fields[this.name + '_end'] = endDate.format('Y-MM-DD');
                            fields[this.name + '_duration'] = endDate.diff(startDate, 'days');
                            if ($el.hasClass('js-datepicker-mode-days')) {
                                fields[this.name + '_duration'] = fields[this.name + '_duration'] + 1;
                            }
                        }
                    }
                } else {
                    if (!cpoObj.isNumber($el.val())) {
                        var val = $el.val().replace(/,/, '.');
                        $el.val(val);
                        fields[this.name] = $el.val();
                    } else {
                        fields[this.name] = $el.val();
                    }
                    fields[this.name + '_count_spaces'] = fields[this.name].length;
                    var withoutSpaces = $el.val().replace(/ /g, '');
                    fields[this.name + '_count'] = withoutSpaces.length;
                }
            } else if ('textarea' === elType) {
                fields[this.name] = $el.val();
                fields[this.name + '_count_spaces'] = fields[this.name].length;
                var _withoutSpaces = $el.val().replace(/ /g, '');
                fields[this.name + '_count'] = _withoutSpaces.length;
            } else if ('hidden' === elType) {
                if ($el.hasClass('js-uni-cpo-field-file_upload')) {
                    var data = $el.data();
                    fields[this.name] = $el.val();
                    if (typeof data.imageWidth !== 'undefined') {
                        fields[this.name + '_width'] = parseInt(data.imageWidth);
                    }
                    if (typeof data.imageHeight !== 'undefined') {
                        fields[this.name + '_height'] = parseInt(data.imageHeight);
                    }
                } else {
                    fields[this.name] = $el.val();
                }
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
            var cpoFields = jQuery(document.body).triggerHandler('cpo_options_data_for_conditional', [fields]);
            if (typeof cpoFields !== 'undefined') {
                fields = cpoFields;
            }
            if (!_.isEqual(formFields, fields)) {
                return cpoObj.collectData(true, fields);
            }
        } else {
            var _cpoFields = jQuery(document.body).triggerHandler('cpo_options_data_before_validate', [fields]);
            if (typeof _cpoFields !== 'undefined') {
                fields = _cpoFields;
            }
        }

        return fields;
    },
    formSubmission: function formSubmission() {
        var cpoObj = this;

        // validates the form
        cpoObj.productFormEl.parsley({
            excluded: '[disabled], .qty, .uni-cpo-excluded-field'
        }).validate();

        if (cpoObj.productFormEl.parsley().isValid()) {
            var $excludeFromFormSubmission = jQuery('.uni-cpo-excluded-field');
            $excludeFromFormSubmission.each(function () {
                jQuery(this).prop('disabled', true);
            });

            // regular form submission or via ajax
            if (!cpoObj.addToCartAjax) {
                cpoObj.productFormEl.submit();
            } else {
                var formDataArray = cpoObj.productFormEl.serializeArray();
                var formData = {};
                for (var i = 0; i < formDataArray.length; i++) {
                    formData[formDataArray[i]['name']] = formDataArray[i]['value'];
                }
                cpoObj.addToCart(formData);
            }

            $excludeFromFormSubmission.each(function () {
                jQuery(this).prop('disabled', false);
            });
        }
    },
    getMainImageDefData: function getMainImageDefData() {
        if (!this.mainImageEl) {
            console.info('UniCPO: No main image found.');
            return;
        }
        var $a = this.mainImageEl.find('a');
        var $img = $a.find('img');

        return {
            imgId: unicpo.product_image_id,
            imgFullUri: $a.attr('href'),
            imgTitle: $img.attr('title'),
            imgAlt: $img.attr('alt'),
            imgUri: $img.attr('src'),
            imgSrcset: $img.attr('srcset'),
            imgSrc: $img.attr('data-src'),
            imgLarge_image: $img.attr('data-large_image'),
            imgLarge_image_width: $img.attr('data-large_image_width'),
            imgLarge_image_height: $img.attr('data-large_image_height')
        };
    },
    getMainImageEl: function getMainImageEl() {
        var $image = jQuery(unicpo.image_selector).find('div.woocommerce-product-gallery__image');
        return $image.length > 0 ? $image.first() : '';
    },
    getFormattedFormData: function getFormattedFormData() {
        var pid = jQuery('.js-cpo-pid').val();
        var $prodQtyInput = this.productFormEl.find('.input-text.qty');
        var prodQty = $prodQtyInput.val() ? $prodQtyInput.val() : 1;
        var fields = {};
        fields['product_id'] = pid;
        fields['quantity'] = prodQty;
        fields = jQuery.extend(fields, this.collectData(false));
        return fields;
    },
    handlePluploadInit: function handlePluploadInit(uploader) {
        var cpoObj = window.UniCpo;
        var list = uploader.settings.listId;

        jQuery(document).on('click', '#' + list + ' .uni-cpo-file-upload-files-item-upload', function (e) {
            e.preventDefault();
            uploader.start();
        });

        jQuery(document).on('click', '#' + list + ' .uni-cpo-file-upload-files-item-remove', function (e) {
            e.preventDefault();
            var $el = jQuery(e.target);
            var fileId = $el.data('file-id');
            var attach_id = $el.data('attach_id');
            var $li = $el.closest('li');
            var $list = $el.closest('.js-uni-cpo-file-upload-files');
            var $option = jQuery('#' + uploader.settings.multipart_params.slug + '-field');
            window.UniCpo.parsleyRemoveError($option);

            if (typeof attach_id !== 'undefined') {
                var data = {
                    action: 'uni_cpo_remove_file',
                    security: unicpo.security,
                    attach_id: attach_id
                };

                jQuery.ajax({
                    url: unicpo.ajax_url,
                    data: data,
                    dataType: 'json',
                    method: 'POST',
                    beforeSend: function beforeSend() {
                        cpoObj._blockForm($list);
                    },
                    error: function error() {
                        cpoObj._unblockForm($list, 'error');
                    },
                    success: function success(r) {
                        if (r.success) {
                            cpoObj._unblockForm($list, 'success');
                            uploader.removeFile(fileId);
                            $li.remove();
                            $option.val('').trigger('change');
                        } else {
                            cpoObj._unblockForm(form, 'error');
                        }
                    }
                });
            } else {
                var files = uploader.files.filter(function (o) {
                    return o.id === fileId;
                });
                if (files.length) {
                    uploader.removeFile(files[0]);
                }
                $li.remove();
                $option.trigger('change');
            }
        });
    },
    handlePluploadError: function handlePluploadError(uploader, error) {
        var $option = jQuery('#' + uploader.settings.multipart_params.slug + '-field');
        window.UniCpo.parsleyRemoveError($option);

        if (-601 === error.code) {
            $option.parsley().addError('file-type', { message: 'This type of files cannot be uploaded.' });
        } else if (-600 === error.code) {
            $option.parsley().addError('file-size', { message: 'The file is too big' });
        } else {
            $option.parsley().addError('file-custom', { message: error.message });
        }
        window.UniCpo.position($option, 0);
    },
    handlePluploadFileFiltered: function handlePluploadFileFiltered(uploader, file) {},
    handlePluploadFilesAdded: function handlePluploadFilesAdded(uploader, files) {
        var $wrap = jQuery('#' + uploader.settings.listId).closest('.uni-module');
        var el = $wrap.find('input[type="hidden"]');
        window.UniCpo.parsleyRemoveError(el);

        if ('single' === uploader.settings.uploadMode && uploader.files.length > 1) {
            el.parsley().addError('file-limit', { message: 'Cannot send more than 1 file.' });
            window.UniCpo.position(el, 0);
            uploader.removeFile(files[0]);
            return false;
        }

        for (var i = 0; i < files.length; i++) {
            var item = '<li class="uni-cpo-file-upload-files-item">' + '<i class="uni-cpo-file-upload-files-item-icon"></i>' + '<span class="uni-cpo-file-upload-files-item-title">' + files[i].name + '</span>' + '<span class="uni-cpo-file-upload-files-item-uploaded"></span>' + '<button class="uni-cpo-file-upload-files-item-upload"></button>' + '<button data-file-id="' + files[i].id + '" class="uni-cpo-file-upload-files-item-remove"></button>' + '<span class="uni-cpo-file-upload-files-item-progress"><span></span></span>' + '</li>';
            jQuery('#' + uploader.settings.listId).append(item);
        }
        el.trigger('change');
    },
    handlePluploadBeforeUpload: function handlePluploadBeforeUpload(uploader, file) {
        var list = uploader.settings.listId;
        uploader.settings.multipart_params.file_name = file.name;
        jQuery('#' + list + ' .uni-cpo-file-upload-files-item-upload').attr('disabled', true);
        jQuery('#' + list + ' .uni-cpo-file-upload-files-item-remove').attr('disabled', true);
    },
    handlePluploadUploadProgress: function handlePluploadUploadProgress(uploader, file) {
        var list = uploader.settings.listId;
        jQuery('#' + list + ' .uni-cpo-file-upload-files-item-progress span').css({ 'width': file.percent + '%' });
    },
    handlePluploadChunkUploaded: function handlePluploadChunkUploaded(uploader, file, r) {
        //console.log(file);
        //console.log(r);
    },
    handlePluploadFileUploaded: function handlePluploadFileUploaded(uploader, file, r) {
        var id = uploader.settings.listId;
        var $option = jQuery('#' + uploader.settings.multipart_params.slug + '-field');
        var resp = JSON.parse(r.response.replace(/\\/g, ''));
        window.UniCpo.parsleyRemoveError($option);

        if (resp.success) {
            var _resp$data$file = resp.data.file,
                attach_id = _resp$data$file.id,
                width = _resp$data$file.width,
                height = _resp$data$file.height;

            jQuery('#' + id + ' .uni-cpo-file-upload-files-item-progress').remove();
            jQuery('#' + id + ' .uni-cpo-file-upload-files-item-upload').remove();
            jQuery('#' + id + ' .uni-cpo-file-upload-files-item-uploaded').show();
            jQuery('#' + id + ' .uni-cpo-file-upload-files-item-remove').attr('disabled', false);
            jQuery('[data-file-id="' + file.id + '"]').data({
                attach_id: attach_id
            });
            $option.val(attach_id);
            $option.data({
                width: width,
                height: height
            });
            //uploader.removeFile(file); // remove from queue
            $option.trigger('change');
        } else {
            $option.parsley().addError('file-upload', { message: resp.data.message });
            window.UniCpo.position($option, 0);
        }
    },
    initTooltip: function initTooltip() {
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
            cpoObj.setPriceTo(cpoObj.priceZeroEl);
        }

        // Triggers an event - on form data process has been started
        jQuery(document.body).trigger('cpo_form_data_process_start', [cpoObj]);

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
            jQuery(document.body).trigger('cpo_options_data_after_validate_event', [fields]);
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
            jQuery(document.body).trigger('cpo_options_data_not_valid_event', [fields]);
        }
    },
    replaceMainImageData: function replaceMainImageData(data) {
        var $a = this.mainImageEl.find('a');
        var $img = $a.find('img');
        var $zoom = $a.next('img.zoomImg').length > 0 ? $a.next('img.zoomImg') : '';

        var imgId = data.imgId,
            imgFullUri = data.imgFullUri,
            imgTitle = data.imgTitle,
            imgAlt = data.imgAlt,
            imgUri = data.imgUri,
            imgSrcset = data.imgSrcset,
            imgSizes = data.imgSizes,
            imgSrc = data.imgSrc,
            imgLarge_image = data.imgLarge_image,
            imgLarge_image_width = data.imgLarge_image_width,
            imgLarge_image_height = data.imgLarge_image_height;


        $img.parent().attr('href', imgFullUri);
        $img.parent().attr('title', imgTitle);

        $img.attr('title', imgTitle);
        $img.attr('alt', imgAlt);
        $img.attr('src', imgUri);
        $img.attr('data-src', imgSrc);
        $img.attr('data-large_image', imgLarge_image);
        $img.attr('data-large_image_width', imgLarge_image_width);
        $img.attr('data-large_image_height', imgLarge_image_height);
        $img.attr('srcset', imgSrcset);
        $img.attr('sizes', imgSizes);

        if ($zoom) {
            $zoom.attr('src', imgFullUri);
        }

        this.showFirstThumbOnImageChange();

        this.productFormEl.find('.js-cpo-product-image').val(imgId);

        // Triggers an event
        jQuery(document.body).trigger('cpo_options_product_image_replaced_event', [data, $img]);
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
        jQuery(document.body).trigger('cpo_set_btn_state_event', [state]);
    },
    setPriceTo: function setPriceTo(data) {
        this.priceTagEl.html(data);
        var cloned = data;
        if ((typeof data === 'undefined' ? 'undefined' : _typeof(data)) === 'object') {
            cloned = data.clone();
        }
        jQuery(document.body).trigger('cpo_set_price_event', [cloned]);
    },
    showFirstThumbOnImageChange: function showFirstThumbOnImageChange() {
        var slider_data = jQuery('.woocommerce-product-gallery').data('flexslider');
        if (typeof slider_data !== 'undefined') {
            slider_data.flexslider(0);
        }
    },
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

window.Parsley.on('field:error', function () {
    window.UniCpo.position(this.$element);
});