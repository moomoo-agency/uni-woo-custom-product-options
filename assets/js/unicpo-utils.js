/**
 *
 * Codestar WP Color Picker v1.1.0
 * This is plugin for WordPress Color Picker Alpha Channel
 *
 * Copyright 2015 Codestar <info@codestarlive.com>
 * GNU GENERAL PUBLIC LICENSE (http://www.gnu.org/licenses/gpl-2.0.txt)
 *
 */
;(function ( $, window, document, undefined ) {
    'use strict';

    // adding alpha support for Automattic Color.js toString function.
    if( typeof Color.fn.toString !== 'undefined' ) {

        Color.fn.toString = function () {

            // check for alpha
            if ( this._alpha < 1 ) {
                return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
            }

            var hex = parseInt( this._color, 10 ).toString( 16 );

            if ( this.error ) { return ''; }

            // maybe left pad it
            if ( hex.length < 6 ) {
                for (var i = 6 - hex.length - 1; i >= 0; i--) {
                    hex = '0' + hex;
                }
            }

            return '#' + hex;

        };

    }

    $.cs_ParseColorValue = function( val ) {

        var value = val.replace(/\s+/g, ''),
                alpha = ( value.indexOf('rgba') !== -1 ) ? parseFloat( value.replace(/^.*,(.+)\)/, '$1') * 100 ) : 100,
                rgba  = ( alpha < 100 ) ? true : false;

        return { value: value, alpha: alpha, rgba: rgba };

    };

    $.fn.cs_wpColorPicker = function() {

        return this.each(function() {

            var $this = $(this);

            // check for rgba enabled/disable
            if( $this.data('rgba') !== false ) {

                // parse value
                var picker = $.cs_ParseColorValue( $this.val() );

                // wpColorPicker core
                $this.wpColorPicker({

                    // wpColorPicker: clear
                    clear: function() {
                        $this.trigger('keyup');
                    },

                    // wpColorPicker: change
                    change: function( event, ui ) {

                        var ui_color_value  = ui.color.toString(),
                            $linked         = $('#uni-setting-border-linked'),
                            $pickers        = $('div[data-group="border"] input[name^="border_color"]').not($this),
                            name            = $this.attr('name');

                        $this.closest('.wp-picker-container').find('.cs-alpha-slider-offset').css('background-color', ui_color_value);
                        $this.val(ui_color_value).trigger('change');

                        if ( $linked.is(':checked') && name.match("^border_color") ) {
                            $pickers.each(function(){
                                $(this).val(ui_color_value).trigger('change');
                            });
                        }

                    },

                    // wpColorPicker: create
                    create: function() {

                        // set variables for alpha slider
                        var a8cIris       = $this.data('a8cIris'),
                                $container    = $this.closest('.wp-picker-container'),

                                // appending alpha wrapper
                                $alpha_wrap   = $('<div class="cs-alpha-wrap">' +
                                                                    '<div class="cs-alpha-slider"></div>' +
                                                                    '<div class="cs-alpha-slider-offset"></div>' +
                                                                    '<div class="cs-alpha-text"></div>' +
                                                                    '</div>').appendTo( $container.find('.wp-picker-holder') ),

                                $alpha_slider = $alpha_wrap.find('.cs-alpha-slider'),
                                $alpha_text   = $alpha_wrap.find('.cs-alpha-text'),
                                $alpha_offset = $alpha_wrap.find('.cs-alpha-slider-offset');

                        // alpha slider
                        $alpha_slider.slider({

                            // slider: slide
                            slide: function( event, ui ) {

                                var slide_value = parseFloat( ui.value / 100 );

                                // update iris data alpha && wpColorPicker color option && alpha text
                                a8cIris._color._alpha = slide_value;
                                $this.wpColorPicker( 'color', a8cIris._color.toString() );
                                $alpha_text.text( ( slide_value < 1 ? slide_value : '' ) );

                            },

                            // slider: create
                            create: function() {

                                var slide_value = parseFloat( picker.alpha / 100 ),
                                        alpha_text_value = slide_value < 1 ? slide_value : '';

                                // update alpha text && checkerboard background color
                                $alpha_text.text(alpha_text_value);
                                $alpha_offset.css('background-color', picker.value);

                                // wpColorPicker clear for update iris data alpha && alpha text && slider color option
                                $container.on('click', '.wp-picker-clear', function() {

                                    a8cIris._color._alpha = 1;
                                    $alpha_text.text('');
                                    $alpha_slider.slider('option', 'value', 100).trigger('slide');

                                });

                                // wpColorPicker default button for update iris data alpha && alpha text && slider color option
                                $container.on('click', '.wp-picker-default', function() {

                                    var default_picker = $.cs_ParseColorValue( $this.data('default-color') ),
                                            default_value  = parseFloat( default_picker.alpha / 100 ),
                                            default_text   = default_value < 1 ? default_value : '';

                                    a8cIris._color._alpha = default_value;
                                    $alpha_text.text(default_text);
                                    $alpha_slider.slider('option', 'value', default_picker.alpha).trigger('slide');

                                });

                                // show alpha wrapper on click color picker button
                                $container.on('click', '.wp-color-result', function() {
                                    $alpha_wrap.toggle();
                                });

                                // hide alpha wrapper on click body
                                $('body').on( 'click.wpcolorpicker', function() {
                                    $alpha_wrap.hide();
                                });

                            },

                            // slider: options
                            value: picker.alpha,
                            step: 1,
                            min: 1,
                            max: 100

                        });
                    },
                    // set  total width
                    width : 233,

                });

            } else {

                // wpColorPicker default picker
                $this.wpColorPicker({
                    clear: function() {
                        $this.trigger('keyup');
                    },
                    change: function( event, ui ) {
                        $this.val(ui.color.toString()).trigger('change');
                    }
                });

            }

        });

    };

})( jQuery, window, document );

jQuery.fn.uniConvertToSlug = function () {
    const $el = this;
    let st = $el.val().toLowerCase();
    st = st.replace(/[\u00C0-\u00C5]/ig,'a');
    st = st.replace(/[\u00C8-\u00CB]/ig,'e');
    st = st.replace(/[\u00CC-\u00CF]/ig,'i');
    st = st.replace(/[\u00D2-\u00D6]/ig,'o');
    st = st.replace(/[\u00D9-\u00DC]/ig,'u');
    st = st.replace(/[\u00D1]/ig,'n');
    st = st.trim();
    st = st.replace(/ /g,'_');
    st = st.replace(/-/g, '_');
    st = st.replace(/[^\w-]+/g, '');
    $el.val(st);
};

// mini plugin - inserts some text at place of caret
jQuery.fn.insertAtCaret = function (myValue) {
    return this.each(function () {
        //IE support
        if (document.selection) {
            this.focus();
            const sel = document.selection.createRange();
            sel.text = myValue;
            this.focus();
        }
        //MOZILLA / NETSCAPE support
        else if (this.selectionStart || this.selectionStart == '0') {
            const startPos = this.selectionStart;
            const endPos = this.selectionEnd;
            const scrollTop = this.scrollTop;
            this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
            this.focus();
            this.selectionStart = startPos + myValue.length;
            this.selectionEnd = startPos + myValue.length;
            this.scrollTop = scrollTop;
        } else {
            this.value += myValue;
            this.focus();
        }
    });
};

function update_everything(container, settingsTmpl) {
    //
    const allFormulaRules = jQuery(container)
        .children('.uni-cpo-non-option-vars-options-row, .uni-formula-conditional-rules-options-row, .uni-select-option-options-row')
        .filter(function() {
            return !jQuery(this).hasClass(settingsTmpl.replace('.', ''));
        });
    let positionCounter = 0;

    allFormulaRules.each(function(i, el){
        const $el = jQuery(el);

        const $allFields = $el.find('input, textarea');
        $allFields.each(function(index, el){
            const $el = jQuery(el);
            const matches = el.name.match(/[(\d+)]/);
            if ( matches ) {
                $el.attr('name', el.name.replace(matches[0], positionCounter));
            }
            const matchesId = el.id.match(/[(\d+)]/);
            if ( matchesId ) {
                $el.attr('id', el.id.replace(matchesId[0], positionCounter));
            }
            let data = $el.attr('data-related-slug');
            if (data) {
                const matchesData = data.match(/[(\d+)]/);
                if (matchesData) {
                    $el.attr('data-related-slug', data.replace(matchesData[0], positionCounter));
                    $el.data('related-slug', data.replace(matchesData[0], positionCounter));
                }
            }
        });
        const $allLabels = $el.find('label');
        $allLabels.each(function(index, el){
            const $el = jQuery(el);
            const forAttr = $el.attr('for');
            if (typeof forAttr !== 'undefined') {
                const matches = forAttr.match(/[(\d+)]/);
                if (matches) {
                    $el.attr('for', forAttr.replace(matches[0], positionCounter));
                }
            }
        });
        const $allMatrixSelects = $el.find('.uni-cpo-matrix-options-row > select');
        $allMatrixSelects.each(function(index, el){
            const $el = jQuery(el);
            const matches = el.name.match(/[(\d+)]/);
            if ( matches ) {
                $el.attr('name', el.name.replace(matches[0], positionCounter));
            }
            const matchesId = el.id.match(/[(\d+)]/);
            if ( matchesId ) {
                $el.attr('id', el.id.replace(matchesId[0], positionCounter));
            }
        });
        const $allConstrained = $el.find('[data-uni-constrained]');
        $allConstrained.each(function(index, el){
            const $div = jQuery(el);
            let data = $div.attr('data-uni-constrained');
            const matches = data.match(/[(\d+)]/);
            if ( matches ) {
                $div.attr('data-uni-constrained', data.replace(matches[0], positionCounter));
                $div.data('uni-constrained', data.replace(matches[0], positionCounter));
            }
        });
        const $allImportEls = $el.find('.uni-matrix-table-container');
        $allImportEls.each(function(index, el){
            const $el = jQuery(el);
            $el.data('row', positionCounter);
            $el.attr('data-row', positionCounter);
        });

        positionCounter++;
    });
}
