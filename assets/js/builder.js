(function ($) {

    /* Underscore.js config
    ----------------------------------------------------------*/

    _.templateSettings = {
        evaluate:    /\{\{(.+?)\}\}/g,
        interpolate: /\{\{=(.+?)\}\}/g,
        escape:      /\{\{-(.+?)\}\}/g
    };

    /* Builderius main object
    ----------------------------------------------------------*/

    var Builderius;

    window.Builderius = window.Builderius || {};

    Builderius = window.Builderius = {

        /* Properties
        ----------------------------------------------------------*/

        /**
         * Models
         *
         * @since 4.0.0
         */
        Models: {},

        /**
         * Collections
         *
         * @since 4.0.0
         */
        Collections: {},

        /**
         * Views
         *
         * @since 4.0.0
         */
        Views: {},

        /**
         * Whether any ajax request is currently being processed
         *
         * @since 4.0.0
         * @access private
         * @property {Boolean} _ajax_sent
         */
        _ajax_sent: false,

        /**
         * The ID of the builder container
         *
         * @since 4.0.0
         * @access private
         * @property {String} _builderId
         */
        _builderId: '#' + builderiusCfg.builderId,

        /**
         * The CSS class of the main content wrapper
         *
         * @since 4.0.0
         * @access private
         * @property {String} _contentClass
         */
        _contentClass: false,

        /**
         * Whether an element is currently being dragged or not.
         *
         * @since 4.0.0
         * @access private
         * @property {Boolean} _dragging
         */
        _dragging: false,

        /**
         * Whether a modal window is shown.
         *
         * @since 4.0.0
         * @access private
         * @property {Boolean} _modal_on
         */
        _modal_on: false,

        /**
         * Whether a panel is shown.
         *
         * @since 4.0.0
         * @access private
         * @property {Boolean} _panel_on
         */
        _panel_on: false,

        /* CPO related properties
        ----------------------------------------------------------*/

        /**
         *
         */
        _optionVars: {},

        /**
         *
         */
        _queryBuilderFilter: [],

        /**
         *
         */
        scroll_table: {},

        /* Initialization
        ----------------------------------------------------------*/

        /**
         * Initializes the builder interface.
         *
         * @since 4.0.0
         * @access private
         * @method _init
         */
        _init: function () {
            if (null === Builderius.getElById(Builderius._builderId.substring(1))) {
                return false;
            }

            Builderius._initClasses();
            Builderius._resetListOfVars();

            const builderMod      = new Builderius.Models.BuilderModel({
                autosaveData: Builderius._autosaveGetData(),
                formulaData:  builderiusCfg.product.formula_data,
                id:           builderiusCfg.product.id,
                novData:      builderiusCfg.product.nov_data,
                settingsData: builderiusCfg.product.settings_data,
                weightData:   builderiusCfg.product.weight_data,
                uri:          builderiusCfg.product.uri,
            });
            Builderius.builderCol = new Builderius.Collections.BuilderCollection();
            Builderius.builderCol.add(builderMod);
            Builderius.Panel = new Builderius.Views.Panel({
                collection: Builderius.builderCol,
                model:      builderMod
            }).render();

            Builderius.rowsCol     = new Builderius.Collections.Rows();
            Builderius.BuilderView = new Builderius.Views.Builder().render();

            if (!_.isEmpty(builderiusCfg.product.content)) {
                Builderius.rowsCol.add(builderiusCfg.product.content, {parse: true});
                Builderius._updateListOfVars();
            } else {
                Builderius.rowsCol.add([], {parse: true});
            }

            Builderius._initSortables();
            Builderius._initTooltip( $('#uni-builder-panel'), 'left bottom', 'left top-10' );
            Builderius._showRevHistory();
        },

        /**
         * Initializes body class
         *
         * @since 4.0.0
         * @access private
         * @method _initClasses
         */
        _initClasses: function () {
            $('body').addClass('builderius');
            Builderius._contentClass = 'builderius-content-' + builderiusCfg.product.id;
            $(Builderius._builderId).addClass(Builderius._contentClass);
        },

        /**
         * Initializes Sortable for drag and drop.
         *
         * @since 4.0.0
         * @access private
         * @method _initSortables
         */
        _initSortables: function () {
            const defaults = {
                ghostClass:  'builderius-drop-zone',
                chosenClass: 'uni-sortable-item-chosen',
                onStart:     Builderius._itemDragStart
            };
            Sortable.create(Builderius.getElById('uni-builder-panel-module'), $.extend({}, defaults, {
                group:     {
                    name: 'panelRowsGroup',
                    pull: 'clone',
                    put:  false
                },
                sort:      false,
                draggable: '.js-panel-block-type-row',
                onEnd:     Builderius._itemDragStop
            }));
            Sortable.create(Builderius.getElById('uni-builder-panel-module'), $.extend({}, defaults, {
                group:     {
                    name: 'panelColumnsGroup',
                    pull: 'clone',
                    put:  false
                },
                sort:      false,
                draggable: '.js-panel-block-type-column',
                onEnd:     Builderius._itemDragStop
            }));
            Sortable.create(Builderius.getElById('uni-builder-panel-module'), $.extend({}, defaults, {
                group:     {
                    name: 'panelModulesGroup',
                    pull: 'clone',
                    put:  false
                },
                sort:      false,
                draggable: '.js-panel-block-type-module',
                onEnd:     Builderius._itemDragStop
            }));
            Sortable.create(Builderius.getElById('uni-builder-panel-option'), $.extend({}, defaults, {
                group:     {
                    name: 'panelOptionsGroup',
                    pull: 'clone',
                    put:  false
                },
                sort:      false,
                draggable: '.js-panel-block-type-option',
                onEnd:     Builderius._itemDragStop
            }));

            // Main wrapper
            Sortable.create(Builderius.getElById(Builderius._builderId.substring(1)), $.extend({}, defaults, {
                group:  {
                    name: 'rowsGroup',
                    pull: true,
                    put:  ['panelRowsGroup']
                },
                handle: '.js-uni-row-move',
                onEnd:  Builderius._itemDragStop
            }));
            [].forEach.call(Builderius.getElById(Builderius._builderId.substring(1)).getElementsByClassName('uni-row-content'), (el) => {
                Sortable.create(el, $.extend({}, defaults, {
                    group:  {
                        name: 'colsGroup',
                        pull: true,
                        put:  ['panelColumnsGroup', 'colsGroup']
                    },
                    handle: '.js-uni-col-move',
                    onEnd:  Builderius._itemDragStop
                }));
            });
            [].forEach.call(Builderius.getElById(Builderius._builderId.substring(1)).getElementsByClassName('uni-col-content'), (el) => {
                Sortable.create(el, $.extend({}, defaults, {
                    group:  {
                        name: 'modulesGroup',
                        pull: true,
                        put:  ['panelModulesGroup', 'panelOptionsGroup', 'modulesGroup']
                    },
                    handle: '.js-uni-module-move',
                    onEnd:  Builderius._itemDragStop
                }));
            });
        },

        /**
         * Initializes jQuery tooltip.
         *
         * @since 1.0
         * @access private
         * @method _initSortables
         */
        _initTooltip: function (wrap, my, at) {
            let position = {
                using: function(position, feedback) {
                    $(this).css(position);
                    $(this).addClass('vertical-' + feedback.vertical).addClass('horizontal-' + feedback.horizontal);
                }
            };
            if ( my !== undefined && at !== undefined ) {
                position['my'] = my;
                position['at'] = at;
            } else {
                position['my'] = 'center bottom';
                position['at'] = 'center top-10';
            }

            wrap.tooltip({
                items: '[data-tip]',
                show: {
                    effect: "show",
                    duration: 0
                },
                hide: {
                    effect: 'hide',
                    duration: 0
                },
                close: function() {
                    $('.ui-helper-hidden-accessible').remove();
                },
                position: position,
                content:  function () {
                    return $(this).attr('data-tip');
                }
            });
        },

        /* Drag and Drop functionality
        ----------------------------------------------------------*/

        /**
         * Callback that fires when dragging starts.
         *
         * @since 4.0.0
         * @access private
         * @method _itemDragStart
         * @param {Object} e The event object.
         */
        _itemDragStart: function (e) {
            Builderius._dragging = true;
            $(Builderius._builderId).addClass('drag_started');

            const type = e.item.getAttribute('data-type');

            switch (type) {
                case 'row':
                    $(Builderius._builderId).addClass('row_drag_started');
                    break;
                case 'column':
                    $(Builderius._builderId).addClass('column_drag_started');
                    break;
                default:
                    $(Builderius._builderId).addClass('module_drag_started');
                    break;
            }
            $('.uni-row-highlight').each(function () {
                $(this).removeClass('uni-row-highlight');
            });

            Builderius.closePanel();
        },

        /**
         * Callback that fires when dragging stops.
         *
         * @since 4.0.0
         * @access private
         * @method _itemDragStop
         * @param {Object} e The event object.
         */
        _itemDragStop: function (e) {

            Builderius._dragging = false;
            $(Builderius._builderId).removeClass('drag_started row_drag_started column_drag_started module_drag_started');

            const rows     = Array.prototype.slice.call(e.to.children);
            const id       = e.item.getAttribute('data-node');
            const type     = e.item.getAttribute('data-type');
            const obj_type = e.item.getAttribute('data-obj_type');
            const args     = {
                id,
                order: rows.indexOf(e.item),
                obj_type,
                type
            };

            if (e.to.classList.contains('uni-builder-panel-items')) {
                // An element was dropped back into the panel.
                return;
            } else {
                switch (type) {
                    case 'row':
                        if (id === null) {
                            Builderius.BuilderView.createRowAndCol(args);
                            e.item.remove();
                        } else {
                            Builderius._setRowsOrder();
                        }
                        break;
                    case 'column':
                        if (id === null) {
                            args.parentRowId = e.to.id.substring(13);
                            Builderius.BuilderView.createCol(args);
                            e.item.remove();
                        } else if (id !== null) {
                            // 'target' is the row which we move the column from
                            // 'to' is the row which we move the column to
                            const rowId    = e.target.id.substring(13);
                            const newRowId = e.to.id.substring(13);
                            if (rowId !== newRowId) { // means that the column was moved to the new row
                                args.parentRowId    = rowId;
                                args.newParentRowId = newRowId;
                                Builderius.BuilderView.moveCol(args);
                                e.item.remove();
                            } else {
                                Builderius._setColsOrder(newRowId);
                            }
                        }
                        break;
                    default:
                        if (id === null) {
                            args.parentRowId    = $('#' + e.to.id).closest('.uni-row').data('node');
                            args.parentColumnId = e.to.id.substring(13);
                            Builderius.BuilderView.createModule(args);
                            e.item.remove();
                        } else {
                            // 'target' is the col which we move the module from
                            // 'to' is the col which we move the module to
                            args.parentRowId    = $('#' + e.target.id).closest('.uni-row').data('node');
                            const newRowId      = $('#' + e.to.id).closest('.uni-row').data('node');
                            args.newParentRowId = newRowId;

                            const colId    = e.target.id.substring(13);
                            const newColId = e.to.id.substring(13);

                            if (colId !== newColId) {
                                args.parentColumnId    = colId;
                                args.newParentColumnId = newColId;
                                Builderius.BuilderView.moveModule(args);
                                e.item.remove();
                            } else {
                                Builderius._setModulesOrder(newRowId, newColId);
                            }
                        }
                        break;
                }

            }
            Builderius._initSortables();
            Builderius._ifEmptyItem();
        },

        /**
         *
         */
        _ifEmptyItem: function () {
            Builderius.rowsCol.each(function (row) {
                const $row = $(Builderius._builderId).find('div[data-node="' + row.id + '"]');
                if (row.columns.length === 0) {
                    $row.removeClass('uni-row-overlay-muted').addClass('uni-row-highlight');
                } else {
                    $row.removeClass('uni-row-highlight');
                }
                row.columns.each(function (column) {
                    const $column = $(Builderius._builderId).find('div[data-node="' + column.id + '"]');
                    if (column.modules.length === 0) {
                        $column.removeClass('uni-col-overlay-muted').addClass('uni-col-highlight');
                    } else {
                        $column.removeClass('uni-col-highlight');
                    }
                });
            });
        },

        /**
         *
         */
        _setRowsOrder: function () {
            let i = 0;
            $(Builderius._builderId).find('.uni-row').each(function () {
                const id = $(this).data('node');
                Builderius.rowsCol.get(id).set({'order': i});
                i++;
            });
            Builderius.rowsCol.sort();
        },

        /**
         *
         */
        _setColsOrder: function (rowId) {
            if (Builderius.rowsCol.get(rowId).columns.length === 0) {
                return;
            }
            let i = 0;
            $(Builderius._builderId)
                .find('div[data-node="' + rowId + '"]')
                .find('.uni-col')
                .each(function () {
                    const id = $(this).data('node');
                    if (typeof Builderius.rowsCol.get(rowId).columns.get(id) !== 'undefined') {
                        Builderius.rowsCol.get(rowId).columns.get(id).set({'order': i});
                        i++;
                    }
                });
            Builderius.rowsCol.sort();
        },

        /**
         *
         */
        _setModulesOrder: function (rowId, columnId) {
            if (Builderius.rowsCol.get(rowId).columns.length === 0
                || Builderius.rowsCol.get(rowId).columns.get(columnId).modules.length === 0) {
                return;
            }
            let i = 0;
            $(Builderius._builderId)
                .find('div[data-node="' + rowId + '"]')
                .find('div[data-node="' + columnId + '"]')
                .find('.uni-module')
                .each(function () {
                    const id = $(this).data('node');
                    if (typeof Builderius.rowsCol.get(rowId).columns.get(id) !== 'undefined'
                        && typeof Builderius.rowsCol.get(rowId).columns.get(columnId).modules.get(id) !== 'undefined') {
                        Builderius.rowsCol.get(rowId).columns.get(columnId).modules.get(id).set({'order': i});
                        i++;
                    }
                });
            Builderius.rowsCol.get(rowId).columns.get(columnId).modules.sort();
        },

        /* Panel
        ----------------------------------------------------------*/

        /**
         *
         */
        closePanel: function () {
            Builderius._panel_on = false;
            $('body.builderius-panel-on').removeClass('builderius-panel-on');
            $('.uni-builder-panel-switch.uni-active').removeClass('uni-active');
        },

        /**
         *
         */
        showPanel: function () {
            Builderius._panel_on = true;
            $('body').addClass('builderius-panel-on');
            $('.uni-builder-panel-switch').addClass('uni-active');
        },

        /**
         *
         */
        _destroyScroll: function (wrap) {
            const $wrap = $('#' + wrap);
            const row = $wrap.data('row');

            if (typeof Builderius.scroll_table[row] !== 'undefined') {
                Builderius.scroll_table[row].destroy();
            }
        },

        /**
         *
         */
        _initScroll: function (wrap) {
            const $wrap = $('#' + wrap);
            const row = $wrap.data('row');
            const $wrapper = $wrap.closest('.uni-matrix-table-wrapper');

            Builderius.scroll_table[row] = $wrapper.jScrollPane({
                mouseWheelSpeed: 40,
                autoReinitialise: true
            }).data().jsp;
        },

        /**
         *
         */
        _showRevHistory: function () {
            const btn   = 'js-revision-history-switch';
            const $btn  = $('#' + btn);
            const wrap  = 'uni-revision-history-wrap';
            const $wrap = $('.' + wrap);

            $btn.on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('uni-clicked')) {
                    $(this).removeClass('uni-clicked');
                    $wrap.slideUp(300, function () {
                        Builderius.scroll_history.destroy();
                    });
                } else {
                    $(this).addClass('uni-clicked');
                    $wrap.slideDown(300, function () {
                        Builderius.scroll_history = $('.uni-revision-items').jScrollPane({
                            mouseWheelSpeed: 40
                        }).data().jsp;
                    });
                }
            });
            $(document).on('click', function (e) {
                if (!$(e.target).hasClass(wrap)
                    && !$(e.target).parents().hasClass(wrap)
                    && !( $(e.target).attr('id') === btn )
                    && $btn.hasClass('uni-clicked')
                ) {
                    $wrap.slideUp(300, function () {
                        Builderius.scroll_history.destroy();
                    });
                    $btn.removeClass('uni-clicked');
                }
            });
        },

        /* Utils
        ----------------------------------------------------------*/

        /**
         * A mixin for collections/models
         */
        adminAjaxSyncableMixin: {
            url:  builderiusCfg.ajax_url,
            sync: function (method, object, options) {
                if (typeof options.data === 'undefined') {
                    options.data = {};
                }
                options.data.security = builderiusCfg.security;
                if (typeof options.data.action === 'undefined' && typeof options.action !== 'undefined') {
                    options.data.action = options.action;
                }

                this.trigger('request', object, options);

                if ('read' === method) {
                    return Backbone.sync(method, object, options);
                }

                const json          = this.toJSON();
                const formattedJSON = {};

                if (json instanceof Array) {
                    formattedJSON.models = json;
                } else {
                    formattedJSON.model = json;
                }
                _.extend(options.data, formattedJSON);
                options.emulateJSON = true;
                return Backbone.sync.call(this, 'create', object, options);
            }
        },

        /**
         *  auto saves to localStorage
         */
        _autosave: function () {
            const timestamp = moment().unix();
            const content   = JSON.stringify(Builderius.rowsCol.toJSON());
            const productId = builderiusCfg.product.id;

            if (window.localStorage) {
                localStorage.setItem('_cpo_autosave_time_' + productId, timestamp);
                localStorage.setItem('_cpo_autosave_data_' + productId, content);
                const builderMod = Builderius.builderCol.get(builderiusCfg.product.id);
                builderMod.set({autosaveData: {timestamp, content}});
            } else {
                console.log('Your browser does not support LocalStorage');
            }
        },

        /**
         *  gets autosaved data from localStorage
         */
        _autosaveGetData: function () {
            if (window.localStorage) {
                const productId = builderiusCfg.product.id;
                const timestamp = localStorage.getItem('_cpo_autosave_time_' + productId);
                const content   = localStorage.getItem('_cpo_autosave_data_' + productId);
                return {
                    timestamp,
                    content
                };
            } else {
                console.log('Your browser does not support LocalStorage');
                return {};
            }
        },

        /**
         *
         */
        _blockForm: function (el) {
            Builderius._ajax_sent = true;
            $(el).block({
                message:    '<div data-loader="circle"></div>',
                css:        {
                    width:      'auto',
                    border:     '0px',
                    background: 'transparent',
                },
                overlayCSS: {
                    background: '#fff',
                    opacity:    0.7
                }
            });
        },

        /**
         * conditionally display/hide sets of options
         */
        _conditionalFields: function (e = '') {
            const $constrained = $('[data-uni-constrained]');
            const cached       = {};

            $constrained.each(function (i, el) {
                const $el                 = $(el);
                const constrainerSelector = $el.data('uni-constrained');
                let constrainedValue      = $el.data('uni-constvalue');
                const $constrainer        = $(constrainerSelector);

                if (typeof constrainerSelector !== 'undefined' && $constrainer.length) {
                    let constrainerValue;
                    if (typeof cached[constrainerSelector] !== 'undefined') {
                        constrainerValue = cached[constrainerSelector];
                    } else {
                        if ($constrainer.length > 1) { // radio inputs or checkboxes array?
                            if ('radio' === $constrainer[0].type) {
                                $.each($constrainer, function (i, obj) {
                                    const $obj = $(obj);
                                    if ($obj.is(':checked')) {
                                        constrainerValue = $obj.val();
                                    }
                                });
                                if (typeof constrainerValue === 'undefined') {
                                    constrainerValue = ''; // 'uni-empty'
                                }
                            } else if ('checkbox' === $constrainer[0].type) {
                                constrainerValue = [];
                                $.each($constrainer, function (i, obj) {
                                    const $obj = $(obj);
                                    if ($obj.is(':checked')) {
                                        constrainerValue.push($obj.val());
                                    }
                                });
                                if (typeof constrainerValue === 'undefined') {
                                    constrainerValue = []; // 'uni-empty'
                                }
                            }
                        } else {
                            if ('checkbox' === $constrainer[0].type) {
                                if ($constrainer.hasClass('builderius-single-checkbox')) {
                                    constrainerValue = ($constrainer.is(':checked')) ? 'on' : 'off';
                                } else {
                                    constrainerValue = ($constrainer.is(':checked')) ? $constrainer.val() : '';
                                }
                            } else {
                                constrainerValue = $constrainer.val();
                            }
                        }
                        cached[constrainerSelector] = constrainerValue;
                    }

                    const $fields = $el.find('input, select, textarea').not('.wp-picker-clear');
                    const constrainedValueMatch = constrainedValue.match(/\|/);
                    if (null !== constrainedValueMatch) {
                        constrainedValue = constrainedValue.split('|');
                    }

                    if (Builderius._isEqual(constrainedValue, constrainerValue)) {
                        if ($fields.length > 0) {
                            $.each($fields, function () {
                                const $formEl = $(this);
                                if (!$formEl.hasClass('builderius-setting-excluded')) {
                                    $formEl.addClass('builderius-setting-field');
                                }
                            });
                        }
                        if ($el.is(':input') && !$el.hasClass('builderius-setting-excluded')) {
                            $el.addClass('builderius-setting-field');
                        }
                        $el.show();
                    } else {
                        if ($fields.length > 0) {
                            $.each($fields, function () {
                                const $formEl = $(this);
                                $formEl.removeClass('builderius-setting-field');
                            });
                        }
                        if ($el.is(':input')) {
                            $el.removeClass('builderius-setting-field');
                        }
                        $el.hide();
                    }
                }
            });
        },

        _isEqual: function (needle, haystack) {
            if (_.isString(needle) && _.isString(haystack)) {
                return needle === haystack;
            } else if (_.isString(needle) && _.isArray(haystack)) {
                return _.indexOf(haystack, needle) !== -1;
            } else if (_.isArray(needle) && _.isString(haystack)) {
                return _.indexOf(needle, haystack) !== -1;
            }
        },

        /**
         *
         */
        _convertHex: function (hex, opacity) {
            hex     = hex.replace('#', '');
            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);
            return 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')';
        },

        /**
         *
         */
        _convertRgba: function (rgb) {
            const match = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
            return (match && match.length === 4) ? '#' + ('0' + parseInt(rgb[1], 10).toString(16)).slice(-2) + ('0' + parseInt(rgb[2], 10).toString(16)).slice(-2) + ('0' + parseInt(rgb[3], 10).toString(16)).slice(-2) : '';
        },

        /**
         *
         */
        _customSelectEvents: function () {
            const $wrap = $('div[data-group="border"]');
            const $linked = $('input[id*="uni-setting-border"][id*="width-linked"]');
            const $lists = $wrap.find('.uni-custom-select-list');
            const $heads = $wrap.find('.uni-custom-select-head');
            const $selects = $wrap.find('.uni-custom-select-wrap select');

            $lists.find('li').on('click', function () {
                const $list = $(this).closest('.uni-custom-select-list');
                const $head = $(this).closest('.uni-custom-select-wrap').find('.uni-custom-select-head');
                const $select = $(this).closest('.uni-custom-select-wrap').find('select');
                const val = $(this).data('value');

                if ($linked.is(':checked')) {

                    const html = $(this).html();
                        $selects.val(val);
                        $lists.slideUp(200);
                        $lists.find('li[data-selected="selected"]').removeAttr('data-selected');
                        $lists.find('li[data-value="' + val + '"]').attr('data-selected', 'selected');
                        $heads.removeClass('clicked').html(html);

                } else {

                    if ($(this).data('selected') == 'selected') {
                        $list.slideUp(200);
                        $head.removeClass('clicked');
                    } else {
                        const html = $(this).html();
                        $list.find('li[data-selected="selected"]').removeAttr('data-selected');
                        $(this).attr('data-selected', 'selected');
                        $select.val(val);
                        $head.removeClass('clicked').html(html);
                        $list.slideUp(200);
                    }
                }
            });
        },

        /**
         *
         */
        _generateCustomSelectHtml: function (elements) {
            elements.each(function () {
                const $wrap = $(this);
                const $select = $wrap.find('select');
                const value = $select.val();
                const $options = $select.find('option');

                $('<div class="uni-custom-select-head"></div><ul class="uni-custom-select-list"></ul>').insertAfter($select);
                $select.hide();

                const $list = $wrap.find('.uni-custom-select-list');
                const $head = $wrap.find('.uni-custom-select-head');

                if (value === 'none') {
                    $head.html('None');
                } else {
                    $head.html('<img src="' + builderiusCfg.cpo_data.border_images[value] + '" alt="Border type ' + value + ' image">');
                }

                $options.each(function () {
                    const val = $(this).val();
                    let text = '';
                    let data = '';
                    let img = '';

                    if (val === 'none') {
                        text = val;
                    } else {
                        img = '<img src="' + builderiusCfg.cpo_data.border_images[val] + '" alt="Border type ' + val + ' image">';
                    }
                    if (value === val) {
                        data = 'data-selected="selected"';
                    }

                    $list.append('<li ' + data + ' data-value="' + val + '">' + text + '' + img + '</li>');
                });

            });

            const wrap = '.uni-custom-select-wrap';
            const list = '.uni-custom-select-list';
            const clk = 'clicked';

            $('.uni-custom-select-head').on('click', function () {
                const $items = $('[data-group="border"]').find('.uni-custom-select-head.clicked');

                if ( $(this).hasClass(clk) ) {
                    $(this).removeClass(clk).closest(wrap).find(list).slideUp(200);
                } else {
                    if ( $items.length > 0 ) {
                        $items.each(function(){
                            $(this).removeClass(clk).closest(wrap).find(list).slideUp(200);
                        });
                    }
                    $(this).addClass(clk).closest(wrap).find(list).slideDown(200);
                }
            });
            $(document).on('click', function (e) {
                if ( !$(e.target).parents().hasClass('uni-custom-select-wrap') ) {
                    const $items = $('[data-group="border"]').find('.uni-custom-select-head.clicked');
                    if ( $items.length > 0 ) {
                        $items.each(function(){
                            $(this).removeClass(clk).closest(wrap).find(list).slideUp(200);
                        });
                    }
                }
            });
            Builderius._customSelectEvents();
        },

        /**
         *
         */
        getUniqueId: () => Math.random().toString(16).substr(2, 8),

        /**
         *
         */
        getElById: (id) => document.getElementById(id),

        /**
         *
         */
        _linkedFields: function () {
            const $checkboxs = $('[data-linked-checkbox]');

            $checkboxs.each(function () {
                const $checkbox = $(this);
                const data      = $checkbox.data('linked-checkbox').split('|');
                let field       = 'input[type="text"]';

                data.forEach(function (item, i) {
                    field += '[name*=' + item + ']';
                });
                const $fields = $('' + field);

                $fields.on('keyup', function () {
                    if ($checkbox.length && $checkbox.is(':checked')) {
                        const val = $(this).val();
                        $fields.not($(this)).val(val);
                    }
                });
            });
        },

        /**
         *
         */
        _parseNameWithBrackets: function (elName) {
            const matchesAllBrackets = elName.match(/\[(.*)\]/);
            let matches              = [];
            const pattern            = /\[(.*?)\]/g;
            let match;

            if (matchesAllBrackets !== null) {
                while ((match = pattern.exec(elName)) !== null) {
                    matches.push(match[1]);
                }
                elName = elName.replace(matchesAllBrackets[0], '');
                return {
                    name: elName,
                    matches
                };
            } else {
                return {name: elName};
            }
        },

        /**
         *
         */
        _readSettingsModal: function (view) {
            const $fields          = view.$el.find('.builderius-setting-field').not('.builder-setting-excluded');
            const modalFieldValues = {
                data:  {},
                valid: true
            };

            $fields.each(function () {
                const type                 = this.type || this.tagName.toLowerCase();
                const $el                  = $(this);
                const fieldParsleyInstance = $el.parsley();
                let elName                 = $el.attr('name');

                fieldParsleyInstance.validate();

                if (fieldParsleyInstance.isValid()) {
                    const parsedData = Builderius._parseNameWithBrackets(elName);
                    if (parsedData.matches) {
                        if (typeof modalFieldValues.data[parsedData.name] === 'undefined') {
                            modalFieldValues.data[parsedData.name] = {};
                        }
                        if (typeof modalFieldValues.data[parsedData.name][parsedData.matches[0]] === 'undefined'
                            && typeof parsedData.matches[0] !== 'undefined') {
                            modalFieldValues.data[parsedData.name][parsedData.matches[0]] = {};
                        }
                        if (typeof modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]] === 'undefined'
                            && typeof parsedData.matches[1] !== 'undefined') {
                            modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = {};
                        }
                        if (typeof modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]][parsedData.matches[2]] === 'undefined'
                            && typeof parsedData.matches[2] !== 'undefined') {
                            modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]][parsedData.matches[2]] = {};
                        }
                    }
                    if ('checkbox' === type) {
                        if ($el.hasClass('builderius-single-checkbox')) { // exception
                            if ($el.is(':checked')) {
                                if (parsedData.matches) {
                                    if (typeof parsedData.matches[2] !== 'undefined' && !_.isEmpty(parsedData.matches[2])) {
                                        modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]][parsedData.matches[2]] = $el.val();
                                    } else if (typeof parsedData.matches[1] !== 'undefined' && !_.isEmpty(parsedData.matches[1])) {
                                        modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = $el.val();
                                    } else if (!_.isEmpty(parsedData.matches[0])) {
                                        modalFieldValues.data[parsedData.name][parsedData.matches[0]] = $el.val();
                                    } else {
                                        modalFieldValues.data[parsedData.name] = $el.val();
                                    }
                                } else {
                                    modalFieldValues.data[elName] = $el.val();
                                }
                            } else {
                                if (parsedData.matches) {
                                    if (typeof parsedData.matches[2] !== 'undefined' && !_.isEmpty(parsedData.matches[2])) {
                                        modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]][parsedData.matches[2]] = 'off';
                                    } else if (typeof parsedData.matches[1] !== 'undefined' && !_.isEmpty(parsedData.matches[1])) {
                                        modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = 'off';
                                    } else if (!_.isEmpty(parsedData.matches[0])) {
                                        modalFieldValues.data[parsedData.name][parsedData.matches[0]] = 'off';
                                    } else {
                                        modalFieldValues.data[parsedData.name] = 'off';
                                    }
                                } else {
                                    modalFieldValues.data[elName] = 'off';
                                }
                            }
                        } else {
                            const checkboxes = [];
                            $('input[name="' + this.name + '"]:checked').each(function () {
                                checkboxes.push($(this).val());
                            });

                            if (typeof parsedData.matches[2] !== 'undefined' && !_.isEmpty(parsedData.matches[2])) {
                                modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]][parsedData.matches[2]] = checkboxes;
                            } else if (typeof parsedData.matches[1] !== 'undefined' && !_.isEmpty(parsedData.matches[1])) {
                                modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = checkboxes;
                            } else if (!_.isEmpty(parsedData.matches[0])) {
                                modalFieldValues.data[parsedData.name][parsedData.matches[0]] = checkboxes;
                            } else {
                                modalFieldValues.data[parsedData.name] = checkboxes;
                            }
                        }
                    } else if ('radio' === type) {
                        if ($el.attr('checked')) {
                            modalFieldValues.data[elName] = $el.val();
                        }
                    } else {
                        if (parsedData.matches) {
                            if (typeof parsedData.matches[2] !== 'undefined') {
                                modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]][parsedData.matches[2]] = $el.val();
                            } else if (typeof parsedData.matches[1] !== 'undefined') {
                                modalFieldValues.data[parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = $el.val();
                            } else {
                                modalFieldValues.data[parsedData.name][parsedData.matches[0]] = $el.val();
                            }
                        } else {
                            modalFieldValues.data[elName] = $el.val();
                        }
                    }
                } else {
                    modalFieldValues.valid = false;
                }
            });

            //console.log(modalFieldValues);
            return modalFieldValues;
        },

        /**
         *
         */
        _unblockForm: function (el, type) {
            Builderius._ajax_sent = false;
            $(document).find('.blockMsg > div[data-loader="circle"]').remove();
            $(document).find('.blockMsg').prepend('<div class="uni-ajax-' + type + '"><div>');
            $('.uni-ajax-' + type + '').fadeIn(500);
            setTimeout(function () {
                $(el).unblock();
            }, 1500);
        },

        /* CPO related utils
        ----------------------------------------------------------*/

        /**
         *
         */
        _getQueryBuilderFilter: function (mod = '') {
            let filter   = Builderius._queryBuilderFilter;
            const prefix = builderiusCfg.cpo_data.var_slug;
            if (mod) {
                const type        = mod.get('type');
                const modSettings = builderiusModules.option[type];

                if (typeof modSettings === 'undefined') {
                    return;
                }

                filter = _.reject(filter, function (obj) {
                    return obj.id === prefix + mod.get('settings').cpo_general.main.cpo_slug;
                });
                if (!_.isEmpty(modSettings.special_vars)) {
                    modSettings.special_vars.forEach((slug) => {
                        filter = _.reject(filter, function (obj) {
                            return obj.id === prefix + mod.get('settings').cpo_general.main.cpo_slug + '_' + slug;
                        });
                    });
                }
                return filter;
            } else {
                return filter;
            }
        },

        /**
         *
         */
        _getSuboptionsFormatted (suboptions) {
            let result = {};
            _.each(suboptions, function (option) {
                result[option.slug] = option.label;
            });
            return result;
        },

        /**
         *
         */
        _resetListOfVars: function () {
            Builderius._optionVars         = {
                builtin: ['uni_cpo_price'],
                regular: [],
                special: [],
                nov:     []
            };
            Builderius._queryBuilderFilter = [];
        },

        /**
         *
         */
        _updateListOfVars: function () {
            Builderius._resetListOfVars();
            const prefix    = builderiusCfg.cpo_data.var_slug;
            const prefixNov = builderiusCfg.cpo_data.nov_slug;

            Builderius.rowsCol.each(function (row) {
                row.columns.each(function (col) {
                    col.modules.map((mod) => {
                        const objType     = mod.get('obj_type');
                        const type        = mod.get('type');
                        const modCfgSettings = builderiusModules[objType][type];
                        const modSettings = mod.get('settings');

                        if ('option' === objType) {
                            if (typeof modSettings.cpo_general !== 'undefined'
                                && !_.isEmpty(modSettings.cpo_general.main.cpo_slug)) {

                                const varName = prefix + modSettings.cpo_general.main.cpo_slug;
                                Builderius._optionVars.regular.push(varName);
                                Builderius._optionVars.regular = _.uniq(Builderius._optionVars.regular);
                                const typeForFilter            = typeof modSettings.cpo_general.main.cpo_type !== 'undefined'
                                    ? modSettings.cpo_general.main.cpo_type
                                    : 'string';
                                const typeForInput             = modCfgSettings.filter_data.input;
                                const filter                   = {
                                    id:        varName,
                                    label:     `{${varName}}`,
                                    type:      typeForFilter,
                                    input:     typeForInput,
                                    operators: modCfgSettings.filter_data.operators
                                };
                                if (_.indexOf(['select', 'radio'], typeForInput) !== -1
                                    && modSettings.cpo_suboptions
                                    && typeof modSettings.cpo_suboptions.data !== 'undefined')
                                {
                                    const suboptions = modSettings.cpo_suboptions.data;
                                    let values       = {};
                                    if (typeof suboptions.cpo_radio_options !== 'undefined') {
                                        values = Builderius._getSuboptionsFormatted(suboptions.cpo_radio_options);
                                    }
                                    if (typeof suboptions.cpo_select_options !== 'undefined') {
                                        values = Builderius._getSuboptionsFormatted(suboptions.cpo_select_options);
                                    }
                                    filter.values = values;
                                }
                                if (_.isEmpty(Builderius._queryBuilderFilter)
                                    || typeof _.findWhere(Builderius._queryBuilderFilter, filter) === 'undefined') {
                                    Builderius._queryBuilderFilter.push(filter);
                                }

                                if (!_.isEmpty(modCfgSettings.special_vars)
                                    && !_.isEmpty(modCfgSettings.filter_data.special_vars)) {

                                    modCfgSettings.special_vars.forEach((slug) => {
                                        const varFullName = `${varName}_${slug}`;
                                        Builderius._optionVars.special.push(varFullName);
                                        const filter = {
                                            id:        varFullName,
                                            label:     `{${varFullName}}`,
                                            type:      modCfgSettings.filter_data.special_vars[slug].type,
                                            input:     modCfgSettings.filter_data.special_vars[slug].input,
                                            operators: modCfgSettings.filter_data.special_vars[slug].operators
                                        };
                                        if (typeof _.findWhere(Builderius._queryBuilderFilter, filter) === 'undefined') {
                                            Builderius._queryBuilderFilter.push(filter);
                                        }
                                    });

                                }

                            }
                        }

                    });
                });
            });
            Builderius._optionVars.regular = _.uniq(Builderius._optionVars.regular);
            Builderius._optionVars.special = _.uniq(Builderius._optionVars.special);
            const novData                  = Builderius.builderCol.at(0).get('novData');

            if ('on' === novData.nov_enable && null !== novData.nov) {
                _.each(novData.nov, function (item) {
                    Builderius._optionVars.nov.push(`${prefixNov}${item.slug}`);
                    const varFullName = `${prefixNov}${item.slug}`;
                    const filter      = {
                        id:        varFullName,
                        label:     `{${varFullName}}`,
                        type:      'double',
                        input:     'text',
                        operators: ['less',
                            'less_or_equal',
                            'equal',
                            'not_equal',
                            'greater_or_equal',
                            'greater',
                            'is_empty',
                            'is_not_empty']
                    };
                    if (typeof _.findWhere(Builderius._queryBuilderFilter, filter) === 'undefined') {
                        Builderius._queryBuilderFilter.push(filter);
                    }
                });
            }
        }
    };

    /* Models
    ----------------------------------------------------------*/

    /**
     * Builderius builder model
     *
     * @class
     * @augments Backbone.Model
     */
    Builderius.Models.BuilderModel = Backbone.Model.extend({
        defaults:   {
            autosaveData: {},
            formulaData:  {},
            id:           null,
            novData:      {},
            settingsData: {},
            weightData:   {},
            uri:          null,
        },
        url:        Builderius.adminAjaxSyncableMixin.url,
        sync:       Builderius.adminAjaxSyncableMixin.sync,
        initialize: function () {},
        toJSON:     function () {
            const json = _.clone(this.attributes);
            return json;
        }
    });

    /**
     * Builderius row model
     *
     * @class
     * @augments Backbone.Model
     */
    Builderius.Models.Row = Backbone.Model.extend({
        defaults:   {
            id:       null,
            order:    null,
            obj_type: null,
            pid:      null,
            settings: {},
            title:    null,
            type:     null
        },
        url:        Builderius.adminAjaxSyncableMixin.url,
        sync:       Builderius.adminAjaxSyncableMixin.sync,
        initialize: function () {
            if (null === this.get('id')) {
                const uid = Builderius.getUniqueId();
                this.set({'id': uid});
            }
            // because initialize is called after parse
            _.defaults(this, {
                columns: new Builderius.Collections.Columns
            });
        },
        duplicate:  function () {
            const newAttrs = _.clone(this.attributes);
            newAttrs.id    = Builderius.getUniqueId();
            return new this.constructor(newAttrs);
        },
        parse:      function (response) {
            if (_.has(response, 'columns')) {
                this.columns = new Builderius.Collections.Columns(response.columns, {
                    parse: true
                });
                delete response.columns;
            }
            return response;
        },
        toJSON:     function () {
            const json   = _.clone(this.attributes);
            json.columns = this.columns.toJSON();
            return json;
        }
    });

    /**
     * Builderius column model
     *
     * @class
     * @augments Backbone.Model
     */
    Builderius.Models.Column = Backbone.Model.extend({
        defaults:   {
            id:          null,
            order:       null,
            obj_type:    null,
            parentRowId: null,
            pid:         null,
            settings:    {},
            title:       null,
            type:        null
        },
        url:        Builderius.adminAjaxSyncableMixin.url,
        sync:       Builderius.adminAjaxSyncableMixin.sync,
        initialize: function () {
            if (!this.get('id')) {
                const uid = Builderius.getUniqueId();
                this.set({'id': uid});
            }
            // because initialize is called after parse
            _.defaults(this, {
                modules: new Builderius.Collections.Modules
            });
        },
        duplicate:  function () {
            const newAttrs = _.clone(this.attributes);
            newAttrs.id    = Builderius.getUniqueId();
            return new this.constructor(newAttrs);
        },
        parse:      function (response) {
            if (_.has(response, 'modules')) {
                this.modules = new Builderius.Collections.Modules(response.modules, {
                    parse: true
                });
                delete response.modules;
            }
            return response;
        },
        toJSON:     function () {
            const json   = _.clone(this.attributes);
            json.modules = this.modules.toJSON();
            return json;
        }
    });

    /**
     * Builderius module model
     *
     * @class
     * @augments Backbone.Model
     */
    Builderius.Models.Module = Backbone.Model.extend({
        defaults:   {
            id:             null,
            order:          null,
            obj_type:       null,
            parentColumnId: null,
            parentRowId:    null,
            pid:            null,
            settings:       {},
            title:          null,
            type:           null
        },
        url:        Builderius.adminAjaxSyncableMixin.url,
        sync:       Builderius.adminAjaxSyncableMixin.sync,
        initialize: function () {
            if (!this.get('id')) {
                const uid = Builderius.getUniqueId();
                this.set({'id': uid});
            }
        },
        duplicate:  function () {
            const newAttrs = _.clone(this.attributes);
            newAttrs.id    = Builderius.getUniqueId();
            return new this.constructor(newAttrs);
        }
    });

    /* Collections
    ----------------------------------------------------------*/

    /**
     * Builderius builder collection
     *
     * @class
     * @augments Backbone.Collection
     */
    Builderius.Collections.BuilderCollection = Backbone.Collection.extend({
        model: Builderius.Models.BuilderModel,
        sync:  Builderius.adminAjaxSyncableMixin.sync,
        url:   Builderius.adminAjaxSyncableMixin.url
    });

    /**
     * Builderius rows collection
     *
     * @class
     * @augments Backbone.Collection
     */
    Builderius.Collections.Rows = Backbone.Collection.extend({
        comparator: 'order',
        model:      Builderius.Models.Row,
        sync:       Builderius.adminAjaxSyncableMixin.sync,
        url:        Builderius.adminAjaxSyncableMixin.url
    });

    /**
     * Builderius columns collection
     *
     * @class
     * @augments Backbone.Collection
     */
    Builderius.Collections.Columns = Backbone.Collection.extend({
        comparator: 'order',
        model:      Builderius.Models.Column
    });

    /**
     * Builderius modules collection
     *
     * @class
     * @augments Backbone.Collection
     */
    Builderius.Collections.Modules = Backbone.Collection.extend({
        comparator: 'order',
        model:      Builderius.Models.Module
    });

    /* Views
    ----------------------------------------------------------*/

    /**
     * Panel view
     *
     * @class
     * @augments Backbone.View
     */
    Builderius.Views.Panel = Backbone.View.extend({
        el:                  'body',
        template:            _.template($('#js-builderius-panel-tmpl').html()),
        autosaveTmpl:        _.template($('#js-builderius-panel-autosave-item-tmpl').html()),
        confirmTmpl:         _.template($('#js-builderius-confirm-action-tmpl').html()),
        events:              {
            'click .uni-builder-panel-switch':               'togglePanel',
            'click .uni-builder-panel-blocks-section-title': 'togglePanelBlocks',
            'click #js-panel-style-switch':                  'styleSwitch',
            'click #js-panel-save-changes':                  'saveContent',
            'click #js-panel-delete-changes':                'deleteContent',
            'click #js-restore-autosaved':                   'autosaveRestore',
            'click #js-panel-general-settings':              'renderSettingsModal',
            'click #js-panel-cpo-nov':                       'renderNovModal',
            'click #js-panel-cpo-formula':                   'renderFormulaModal'
        },
        autosaveRestore:     function () {
            const data = Builderius._autosaveGetData();
            if (data) {
                // deletes
                Builderius.rowsCol.reset();
                $(Builderius._builderId).empty();

                // restores
                Builderius.rowsCol.add(JSON.parse(data.content), {parse: true});
                Builderius._updateListOfVars();
                Builderius._initSortables();
                Builderius._ifEmptyItem();
            }
        },
        deleteContent:       function () {
            const confirmData = {
                type:    'error',
                message: 'Are you sure?'
            };
            const html        = this.confirmTmpl({data: confirmData});
            this.$el.append(html);
            const $wrap = $('#js-confirm-action-wrapper');

            $(document).on('click', '#js-modal-cancel-btn', function () {
                $wrap.remove();
            });
            $(document).on('click', '#js-modal-delete-btn', function () {
                const data = {
                    action:     'uni_cpo_save_content',
                    security:   builderiusCfg.security,
                    product_id: builderiusCfg.product.id
                };

                $.ajax({
                    url:        builderiusCfg.ajax_url,
                    data,
                    dataType:   'json',
                    method:     'POST',
                    beforeSend: function () {},
                    error:      function () {
                        $wrap.find('p').html('Something went wrong. <br> Please try again later!');
                        setTimeout(function () {
                            $wrap.remove();
                        }, 3000);
                    },
                    success:    function () {
                        Builderius.rowsCol.reset();
                        $(Builderius._builderId).empty();
                        Builderius._updateListOfVars();
                        Builderius._initSortables();
                        Builderius._ifEmptyItem();
                        $wrap.find('.uni-modal-btns-wrap').remove();
                        $wrap
                            .removeClass('uni-confirm-action-wrapper__error')
                            .addClass('uni-confirm-action-wrapper__success')
                            .find('p')
                            .text('All content deleted!');
                        setTimeout(function () {
                            $wrap.remove();
                        }, 2500);
                    }
                });
            });
        },
        initialize:          function () {
            this.listenTo(this.collection, 'change:autosaveData', this.renderAutosaveHtml);
        },
        render:              function () {
            const html = this.template({
                autosaveData:     this.model.get('autosaveData'),
                autosaveItemTmpl: this.autosaveTmpl,
                modules:          builderiusModules,
                uri:              this.model.get('uri')
            });
            this.$el.append(html);
        },
        renderAutosaveHtml:  function () {
            const $autosaveItemOld = this.$el.find('#js-autosave-item');
            const autosaveData     = this.model.get('autosaveData');
            const autosaveItemNew  = this.autosaveTmpl({data: autosaveData});
            $autosaveItemOld.replaceWith(autosaveItemNew);
        },
        renderFormulaModal:  function () {
            const formulaModalView = new Builderius.Views.MainFormulaModal({model: this.model});
            formulaModalView.render();
        },
        renderNovModal:      function () {
            const novModalView = new Builderius.Views.NovModal({model: this.model});
            novModalView.render();
        },
        renderSettingsModal: function () {
            const settingsModalView = new Builderius.Views.GeneralSettingsModal({model: this.model});
            settingsModalView.render();
        },
        saveContent:         function () {
            const collection     = Builderius.rowsCol;
            const builderContent = JSON.stringify(collection.toJSON());

            const data = {
                action:     'uni_cpo_save_content',
                security:   builderiusCfg.security,
                product_id: builderiusCfg.product.id,
                data:       builderContent
            };

            $.ajax({
                url:        builderiusCfg.ajax_url,
                data,
                dataType:   'json',
                method:     'POST',
                beforeSend: function () {
                    Builderius._blockForm('.uni-builderius-container');
                },
                error:      function () {
                    Builderius._unblockForm('.uni-builderius-container', 'error');
                    console.log('Something went wrong. Please try again later!');
                },
                success:    function () {
                    Builderius._unblockForm('.uni-builderius-container', 'success');
                }
            });
        },
        styleSwitch:         function () {
            const $panel = $('#uni-builder-panel');
            if ($panel.hasClass('uni-panel-light')) {
                $panel.removeClass('uni-panel-light').addClass('uni-panel-dark');
            } else {
                $panel.removeClass('uni-panel-dark').addClass('uni-panel-light');
            }
        },
        togglePanel:         function () {
            if (true === Builderius._panel_on) {
                Builderius.closePanel();
            } else {
                Builderius.showPanel();
            }
        },
        togglePanelBlocks:   function (e) {
            if ($(e.target)
                    .closest('.uni-builder-panel-blocks-section')
                    .hasClass('active')) {
                $(e.target)
                    .closest('.uni-builder-panel-blocks-section.active')
                    .removeClass('active')
                    .find('.fa-chevron-up')
                    .removeClass('fa-chevron-up')
                    .addClass('fa-chevron-down');
            } else {
                $(e.target)
                    .closest('.uni-builder-panel-blocks-section')
                    .addClass('active')
                    .find('.fa-chevron-down')
                    .removeClass('fa-chevron-down')
                    .addClass('fa-chevron-up');
            }
        }
    });

    /**
     * Builder view
     *
     * @class
     * @augments Backbone.View
     */
    Builderius.Views.Builder = Backbone.View.extend({
        el:              Builderius._builderId,
        events:            {
            'mouseenter .uni-row, .uni-col, .uni-module': 'showOverlay',
            'mouseleave .uni-row, .uni-col, .uni-module': 'hideOverlay',
            'mouseenter .uni-block-overlay-header': 'showActionsBtns',
            'mouseleave .uni-block-overlay-header': 'hideActionsBtns'
        },
        row_overlay_tmpl: _.template($('#js-builderius-row-overlay-tmpl').html()),
        col_overlay_tmpl: _.template($('#js-builderius-col-overlay-tmpl').html()),
        module_overlay_tmpl: _.template($('#js-builderius-module-overlay-tmpl').html()),
        createCol:       function (args) {
            const {order, obj_type, parentRowId, pid, type} = args;
            const moduleSettingsData                        = builderiusModules.module[type];
            const row                                       = this.collection.get(parentRowId);
            const column                                    = {
                order,
                obj_type,
                parentRowId,
                pid,
                settings: window[moduleSettingsData.cfg].settings,
                title:    moduleSettingsData.title,
                type
            };
            const newCol                                    = new Builderius.Models.Column(column, {parse: true});
            row.columns.add(newCol, {at: order});
            this.updateOrder(row.columns);
            Builderius._autosave();

            //console.log('---- collection', this.collection.toJSON())
        },
        createModule:    function (args) {
            const {order, obj_type, parentColumnId, parentRowId, pid, type} = args;

            const moduleSettingsData = builderiusModules[obj_type][type];
            const module             = {
                order,
                obj_type,
                parentColumnId,
                parentRowId,
                pid,
                settings: window[moduleSettingsData.cfg].settings,
                title:    moduleSettingsData.title,
                type
            };
            const newModule = new Builderius.Models.Module(module, {parse: true});
            const column    = this.collection.get(parentRowId).columns.get(parentColumnId);
            column.modules.add(newModule, {at: order});
            this.updateOrder(column.modules);
            Builderius._autosave();

            //console.log('---- collection', this.collection.toJSON())
        },
        createRowAndCol: function (args) {
            const {order, obj_type, pid, type} = args;
            const moduleSettingsData           = builderiusModules.module[type];

            const row    = {
                order,
                obj_type,
                pid,
                settings: window[moduleSettingsData.cfg].settings,
                title:    moduleSettingsData.title,
                type
            };
            const newRow = new Builderius.Models.Row(row, {parse: true});

            const columnSettingsData = builderiusModules.module['column'];
            const column             = {
                order:       0,
                obj_type,
                parentRowId: newRow.get('id'),
                pid:         0,
                settings:    window[columnSettingsData.cfg].settings,
                title:       columnSettingsData.title,
                type:        'column'
            };
            const newCol             = new Builderius.Models.Column(column, {parse: true});

            //
            newRow.columns.add(newCol);
            this.collection.add(newRow, {at: order});
            this.updateOrder(this.collection);
            Builderius._autosave();

            //console.log('---- collection', this.collection.toJSON())
        },
        hideActionsBtns: function (e) {
            if (false === Builderius._dragging) {
                const view = this;
                const $target = $(e.currentTarget);
                if ( $target.parent().hasClass('uni-row-overlay') ) {
                    $target.parent().css({'z-index': 999990});
                } else if ( $target.parent().hasClass('uni-col-overlay') ) {
                    $target.parent().css({'z-index': 999991});
                }

            }
        },
        hideOverlay: function (e) {
            if (false === Builderius._dragging) {
                const $target = $(e.currentTarget);
                const type = $target.attr('data-type');

                if ( type === 'row' ) {
                    $target.removeClass('uni-row-overlay-active uni-row-small uni-row-overlay-muted').find('.uni-row-overlay').remove();
                } else if ( type === 'column' ) {
                    $target.removeClass('uni-col-overlay-active uni-column-small uni-column-has-modules').find('.uni-col-overlay').remove();
                    $target.closest('.uni-row').removeClass('uni-row-overlay-muted');
                } else {
                    $target.removeClass('uni-module-overlay-active uni-module-small').find('.uni-module-overlay').remove();
                    $target.closest('.uni-col').removeClass('uni-col-overlay-muted');
                }
            }
        },
        initialize:      function () {
            this.collection = Builderius.rowsCol;
            this.rowsView   = new Builderius.Views.Rows({collection: this.collection});
        },
        moveCol:         function (args) {
            const {id, newParentRowId, order, parentRowId} = args;
            // get the old and the new rows
            const oldRow                                   = this.collection.get(parentRowId);
            const newRow                                   = this.collection.get(newParentRowId);

            // duplicate the column and all its modules
            const oldCol             = oldRow.columns.get(id);
            const duplicatedColumn   = oldCol.duplicate();
            const duplicatedColumnId = duplicatedColumn.get('id');

            duplicatedColumn.set({
                order,
                parentRowId: newParentRowId,
            }, {silent: true});

            // duplicate all the original modules
            oldCol.modules.each(function (module) {
                const duplicatedModule = module.duplicate();

                duplicatedModule.set({
                    parentRowId:    newParentRowId,
                    parentColumnId: duplicatedColumnId
                }, {silent: true});

                duplicatedColumn.modules.add(duplicatedModule);

            });
            newRow.columns.add(duplicatedColumn, {at: order});
            this.updateOrder(newRow.columns);

            // remove the old col from the old row
            oldRow.columns.remove(id);
            this.updateOrder(oldRow.columns);
            Builderius._autosave();

            //console.log('---- collection', this.collection.toJSON())
        },
        moveModule:      function (args) {
            const {id, newParentColumnId, newParentRowId, order, parentColumnId, parentRowId} = args;

            // get the old and the new rows, cols
            const oldRow = this.collection.get(parentRowId);
            const newRow = this.collection.get(newParentRowId);
            const oldCol = oldRow.columns.get(parentColumnId);
            const newCol = newRow.columns.get(newParentColumnId);

            // duplicate the module and add to the new column
            const oldModule        = oldCol.modules.get(id);
            const duplicatedModule = oldModule.duplicate();

            duplicatedModule.set({
                order,
                parentColumnId: newParentColumnId,
                parentRowId:    newParentRowId
            }, {silent: true});

            newCol.modules.add(duplicatedModule, {at: order});
            this.updateOrder(newCol.modules);

            // remove the old module from the old column
            oldCol.modules.remove(id);
            this.updateOrder(oldCol.modules);
            Builderius._autosave();

            //console.log('---- collection', this.collection.toJSON())
        },
        render:          function () {
            this.$el.html(this.rowsView.render().$el);
            return this;
        },
        renderColOverlay: function (target) {
            const view = this;
            const $target = target;
            if ( !$target.hasClass('uni-col-overlay-active') ) {
                const width = $target.width();
                if (width < 194) {
                    $target.addClass('uni-column-small');
                }
                if ( $target.find('.uni-module').length > 0) {
                    $target.addClass('uni-column-has-modules');
                }
                const overlay = view.col_overlay_tmpl();
                $target.append(overlay).addClass('uni-col-overlay-active');
                $target.closest('.uni-row').addClass('uni-row-overlay-muted');
            }
        },
        renderModuleOverlay: function (target) {
            const view = this;
            const $target = target;
            if ( !$target.hasClass('uni-module-overlay-active') ) {
                const width = $target.width();
                if (width < 194) {
                    $target.addClass('uni-module-small');
                }
                const overlay = view.module_overlay_tmpl();
                $target.append(overlay).addClass('uni-module-overlay-active');
                $target.closest('.uni-col').addClass('uni-col-overlay-muted');
            }
        },
        renderRowOverlay: function (target) {
            const view = this;
            const $target = target;
            if ( !$target.hasClass('uni-row-overlay-active') ) {
                const width = $target.width();
                if (width < 174) {
                    $target.addClass('uni-row-small');
                }
                const overlay = view.row_overlay_tmpl();
                $target.append(overlay).addClass('uni-row-overlay-active');
            }
        },
        showActionsBtns: function (e) {
            if (false === Builderius._dragging) {
                const view = this;
                const $target = $(e.currentTarget);
                if ( $target.parent().hasClass('uni-row-overlay') ) {
                    $target.parent().css({'z-index': 999993});
                } else if ( $target.parent().hasClass('uni-col-overlay') ) {
                    $target.parent().css({'z-index': 999994});
                }

            }
        },
        showOverlay: function (e) {
            e.stopPropagation();
            if (false === Builderius._dragging) {
                const view = this;
                const $target = $(e.currentTarget);
                const type = $target.attr('data-type');

                if ( type === 'row' ) {
                    view.renderRowOverlay($target);
                } else if ( type === 'column' ) {
                    const $row = $target.closest('.uni-row');
                    view.renderColOverlay($target);
                    view.renderRowOverlay($row);
                } else {
                    const $col = $target.closest('.uni-col');
                    const $row = $target.closest('.uni-row');
                    view.renderModuleOverlay($target);
                    view.renderColOverlay($col);
                    view.renderRowOverlay($row);
                }
            }
        },
        updateOrder:     function (collection) {
            let i = 0;
            collection.each((m) => {
                m.set({'order': i});
                i++;
            });
        }
    });

    /**
     * Rows view
     *
     * @class
     * @augments Backbone.View
     */

    Builderius.Views.Rows = Backbone.View.extend({
        destroyed:   function () {
            this.updateOrder(this.collection);
        },
        initialize:  function () {
            this.listenTo(this.collection, 'add', this.insertRow);
            this.listenTo(this.collection, 'destroy', this.destroyed);
            this.listenTo(this.collection, 'remove', this.removedRow);
            this.listenTo(this.collection, 'sort', this.sorted);
            this.listenTo(this.collection, 'update', this.render);
            this.listenTo(this.collection, 'reset', this.render);
            this.listenTo(this.collection, 'change:settings', this.render);
        },
        insertRow:   function (row) {
            const rowView = new Builderius.Views.Row({model: row});
            const order   = parseInt(row.get('order'));
            if (order === 0) {
                this.$el.prepend(rowView.render().$el);
            } else if (order > 0) {
                rowView.render().$el.insertAfter($(Builderius._builderId + ' .uni-row:eq(' + (order - 1) + ')'));
            }
        },
        removedRow:  function (row) {
            //console.log(row.get('id'),'row removed');
        },
        render:      function () {
            if (this.collection.models.length > 0) {
                $(Builderius._builderId).removeClass('uni-empty-container');
            } else {
                $(Builderius._builderId).addClass('uni-empty-container');
            }
            this.collection.sort().each(this.renderRow, this);
            this.setElement($(Builderius._builderId));
            Builderius._initSortables();
            Builderius._ifEmptyItem();
            return this;
        },
        renderRow:   function (row) {
            const rowView     = new Builderius.Views.Row({model: row});
            const $existedRow = this.$el.find('[data-node=' + row.get('id') + ']');

            if ($existedRow.length > 0) {
                $existedRow.replaceWith(rowView.render().$el);
            } else {
                this.$el.append(rowView.render().$el);
            }
        },
        sorted:      function () {
            //console.log('rows sorted');
        },
        updateOrder: function (collection) {
            let i = 0;
            collection.each((m) => {
                m.set({'order': i});
                i++;
            });
        }
    });

    /**
     * Row view
     *
     * @class
     * @augments Backbone.View
     */
    Builderius.Views.Row = Backbone.View.extend({
        events:            {
            'click .js-uni-row-settings':     'openSettingsModal',
            'click .js-uni-row-clone':        'duplicateItem',
            'click .js-uni-row-remove':       'removeItem',
            'click .uni-block-overlay-title': 'showActionsBtns'
        },
        template:          _.template($('#js-builderius-row-tmpl').html()),
        duplicateItem:     function () {
            const highestOrderNoRow = this.model.collection.max((row) => row.get('order'));
            const order             = highestOrderNoRow.get('order') + 1;
            // duplicate row
            const duplicatedRow     = this.model.duplicate();
            const duplicatedRowId   = duplicatedRow.get('id');

            // set a proper order for the new row
            duplicatedRow.set({
                order,
            }, {silent: true});

            // duplicate all the original columns
            this.model.columns.each(function (column) {
                const duplicatedColumn = column.duplicate();

                duplicatedColumn.set({
                    parentRowId: duplicatedRowId
                }, {silent: true});

                // duplicate all the original modules
                column.modules.each(function (module) {
                    const duplicatedModule = module.duplicate();

                    duplicatedModule.set({
                        parentRowId:    duplicatedRowId,
                        parentColumnId: duplicatedColumn.get('id'),
                        pid:            null,
                    }, {silent: true});

                    duplicatedColumn.modules.add(duplicatedModule);
                });

                duplicatedRow.columns.add(duplicatedColumn);
            });

            this.model.collection.add(duplicatedRow, {at: order});
            this.updateOrder(this.model.collection);
            Builderius._initSortables();
            Builderius._ifEmptyItem();

            //console.log('---- collection', this.model.collection.toJSON());
        },
        initialize:        function () {
            //this.listenTo(this.model, 'change:settings', this.render);
        },
        openSettingsModal: function () {
            const moduleEditView = new Builderius.Views.Modal({model: this.model});
            moduleEditView.render();
        },
        removeItem:        function () {
            const row = this.model;
            if (!_.isEmpty(row.columns.models)) {
                _.each(row.columns.models, function (column) {
                    if (!_.isEmpty(column.modules.models)) {
                        column.modules.remove(column.modules.models);
                    }
                });
                row.columns.remove(row.columns.models);
            }
            row.destroy();
            this.remove();
            Builderius._updateListOfVars();
            Builderius._initSortables();
            Builderius._ifEmptyItem();
        },
        render:            function () {
            const element = this.template(this.model.toJSON());
            const columns = this.model.columns;

            this.setElement(element);
            this.columnsView = new Builderius.Views.Columns({collection: columns});
            this.columnsView.setElement(this.$('#js-row-group-' + this.model.get('id'))).render();

            return this;
        },
        showActionsBtns:   function (e) {
            const $btn    = $(e.target);
            const clicked = 'uni-clicked';

            if ($btn.hasClass(clicked)) {
                $btn.removeClass(clicked);
            } else {
                $btn.addClass(clicked);
            }
        },
        updateOrder:       function (collection) {
            let i = 0;
            collection.each((m) => {
                m.set({'order': i});
                i++;
            });
        }
    });

    /**
     * Columns view
     *
     * @class
     * @augments Backbone.View
     */
    Builderius.Views.Columns = Backbone.View.extend({
        destroyed:    function () {
            this.updateOrder(this.collection);
        },
        initialize:   function () {
            this.listenTo(this.collection, 'add', this.insertColumn);
            this.listenTo(this.collection, 'destroy', this.destroyed);
            this.listenTo(this.collection, 'remove', this.removedCol);
            this.listenTo(this.collection, 'update', this.render);
            this.listenTo(this.collection, 'reset', this.render);
            this.listenTo(this.collection, 'change:settings', this.render);
        },
        insertColumn: function (column) {
            const columnView = new Builderius.Views.Column({model: column});
            const order      = parseInt(column.get('order'));

            if (order === 0) {
                this.$el.prepend(columnView.render().$el);
            } else if (order > 0) {
                columnView.render().$el.insertAfter(this.$el.find('.uni-col:eq(' + (order - 1) + ')'));
            }
        },
        removedCol:   function (column) {
            //console.log(column.get('id'), 'column removed');
        },
        render:       function () {
            this.collection.sort().each(this.renderColumn, this);
            Builderius._initSortables();
            Builderius._ifEmptyItem();
            return this;
        },
        renderColumn: function (column) {
            const columnView  = new Builderius.Views.Column({model: column});
            const $existedCol = this.$el.find('[data-node=' + column.get('id') + ']');

            if ($existedCol.length > 0) {
                $existedCol.replaceWith(columnView.render().$el);
            } else {
                this.$el.append(columnView.render().$el);
            }
        },
        updateOrder:  function (collection) {
            let i = 0;
            collection.each((m) => {
                m.set({'order': i});
                i++;
            });
        }
    });

    /**
     * Column view
     *
     * @class
     * @augments Backbone.View
     */
    Builderius.Views.Column = Backbone.View.extend({
        events:            {
            'click .js-uni-column-settings': 'openSettingsModal',
            'click .js-uni-column-clone':    'duplicateItem',
            'click .js-uni-column-remove':   'removeItem'
        },
        template:          _.template($('#js-builderius-col-tmpl').html()),
        duplicateItem:     function () {
            const highestOrderNoColumn = this.model.collection.max((column) => column.get('order'));
            const order                = highestOrderNoColumn.get('order') + 1;

            // duplicate column
            const duplicatedColumn   = this.model.duplicate();
            const duplicatedColumnId = duplicatedColumn.get('id');

            // set a proper order for the new column
            duplicatedColumn.set({
                order,
            }, {silent: true});

            const parentRowId = this.model.get('parentRowId');
            const parentRow   = Builderius.rowsCol.get(parentRowId);

            // duplicate all the original modules
            this.model.modules.each(function (module) {
                const duplicatedModule = module.duplicate();

                duplicatedModule.set({
                    parentRowId:    parentRowId,
                    parentColumnId: duplicatedColumnId,
                    pid:            null,
                }, {silent: true});

                duplicatedColumn.modules.add(duplicatedModule);

            });

            parentRow.columns.add(duplicatedColumn, {at: order});
            this.updateOrder(this.model.collection);
            Builderius._initSortables();
            Builderius._ifEmptyItem();

            //console.log('---- collection', Builderius.rowsCol.toJSON());
        },
        initialize:        function () {},
        openSettingsModal: function () {
            const moduleEditView = new Builderius.Views.Modal({model: this.model});
            moduleEditView.render();
        },
        removeItem:        function () {
            const column = this.model;
            if (!_.isEmpty(column.modules)) {
                column.modules.remove(column.modules.models);
            }
            column.destroy();
            this.remove();
            Builderius._updateListOfVars();
            Builderius._initSortables();
            Builderius._ifEmptyItem();
        },
        render:            function () {
            const element = this.template(this.model.toJSON());
            const modules = this.model.modules;

            this.setElement(element);
            this.modulesView = new Builderius.Views.Modules({collection: modules});
            this.modulesView.setElement(this.$('#js-col-group-' + this.model.get('id'))).render();

            return this;
        },
        updateOrder:       function (collection) {
            let i = 0;
            collection.each((m) => {
                m.set({'order': i});
                i++;
            });
        }
    });

    // ModulesView
    Builderius.Views.Modules = Backbone.View.extend({
        destroyed:     function () {
            this.updateOrder(this.collection);
        },
        initialize:    function () {
            this.listenTo(this.collection, 'add', this.insertModule);
            this.listenTo(this.collection, 'change:settings', this.render);
            this.listenTo(this.collection, 'destroy', this.destroyed);
            this.listenTo(this.collection, 'reset', this.render);
            this.listenTo(this.collection, 'remove', this.removedModule);
        },
        insertModule:  function (module) {
            const moduleView = new Builderius.Views.Module({model: module});
            const order      = parseInt(module.get('order'));

            if (order === 0) {
                this.$el.prepend(moduleView.render().$el);
            } else if (order > 0) {
                moduleView.render().$el.insertAfter(this.$el.find('.uni-module:eq(' + (order - 1) + ')'));
            }
        },
        removedModule: function (module) {
            //console.log(module.get('id'), 'module removed');
        },
        render:        function () {
            this.collection.sort().each(this.renderModule, this);
            return this;
        },
        renderModule:  function (module) {
            const moduleView     = new Builderius.Views.Module({model: module});
            const $existedModule = this.$el.find('[data-node=' + module.get('id') + ']');

            if ($existedModule.length > 0) {
                $existedModule.replaceWith(moduleView.render().$el);
            } else {
                this.$el.append(moduleView.render().$el);
            }
        },
        updateOrder:   function (collection) {
            let i = 0;
            collection.each((m) => {
                m.set({'order': i});
                i++;
            });
        }
    });

    // ModuleView
    Builderius.Views.Module = Backbone.View.extend({
        events:            {
            'click .js-uni-module-settings': 'openSettingsModal',
            'click .js-uni-module-clone':    'duplicateItem',
            'click .js-uni-module-remove':   'removeItem'
        },
        duplicateItem:     function () {
            const highestOrderNoModule = this.model.collection.max((module) => module.get('order'));
            const order                = highestOrderNoModule.get('order') + 1;

            // duplicate column
            const duplicatedModule = this.model.duplicate();
            const parentColumnId   = this.model.get('parentColumnId');
            const parentRowId      = this.model.get('parentRowId');

            // set a proper order for the new column
            // as well as parent row and parent col ids
            duplicatedModule.set({
                order,
                pid: null,
            }, {silent: true});

            Builderius.rowsCol.get(parentRowId)
                .columns.get(parentColumnId)
                .modules.add(duplicatedModule, {at: order});
            this.updateOrder(this.model.collection);
            Builderius._initSortables();
            Builderius._ifEmptyItem();

            //console.log('---- collection', Builderius.rowsCol.toJSON());
        },
        initialize:        function () {
            const tmplId  = '#js-builderius-module-' + this.model.get('type') + '-tmpl';
            this.template = _.template($(tmplId).html(), {variable: 'data'});
        },
        openSettingsModal: function () {
            const moduleEditView = new Builderius.Views.Modal({model: this.model});
            moduleEditView.render();
        },
        removeItem:        function () {
            this.model.destroy();
            this.remove();
            Builderius._updateListOfVars();
            Builderius._initSortables();
            Builderius._ifEmptyItem();
        },
        render:            function () {
            const element = this.template(this.model.toJSON());
            const view    = this;
            this.setElement(element);
            return this;
        },
        updateOrder:       function (collection) {
            let i = 0;
            collection.each((m) => {
                m.set({'order': i});
                i++;
            });
        }
    });

    // ModalView
    Builderius.Views.Modal = Backbone.View.extend({
        el:                       'body',
        events:                   {
            'click .uni-close-modal':              'closeModal',
            'click #js-modal-cancel-btn':          'closeModal',
            'click #js-modal-save-btn':            'saveSettings',
            'change [data-uni-constrainer="yes"]': 'updateConstrained',
            'click #js-fetch-similar-modules':     'fetchModules',
            'change .js-sync-methods':             'settingSyncMethodChanged',
            'change .js-sync-posts':               'settingSyncPostChanged',
            'click #js-sync-module-btn':           'syncWithModule',
            'click #js-unsync-module-btn':         'unSyncModule'
        },
        template:                 _.template($('#js-builderius-modal-tmpl').html()),
        template_tab_list:        _.template($('#js-builderius-modal-tab-list-tmpl').html()),
        template_tab_open:        _.template($('#js-builderius-modal-tab-open-tmpl').html()),
        template_tab_close:       _.template($('#js-builderius-modal-tab-close-tmpl').html()),
        template_group_open:      _.template($('#js-builderius-modal-group-open-tmpl').html()),
        template_group_close:     _.template($('#js-builderius-modal-group-close-tmpl').html()),
        ajaxError:                function (r, s) {
            //console.log(r);
            console.log(s);
            // restore original settings
            view.model.set({settings: view.originalModelSettings});
            Builderius._unblockForm('#js-block-settings-modal', 'error');
        },
        ajaxSent:                 function (model, xhr, options) {
            Builderius._blockForm('#js-block-settings-modal');
            const $slugField = $('#js-cpo-field-slug-wrapper');
            $slugField.removeClass('uni-error-label');
            $slugField.parent().find('.uni-slug-suggestion').remove();
        },
        ajaxSynced:               function (r) {
            const view = this;
            if (r.success) {
                view.model.set(r.data);
                Builderius._updateListOfVars();
                Builderius._autosave();
                $('#js-modal-save-btn').removeClass('uni-active');
                Builderius._unblockForm('#js-block-settings-modal', 'success');
            } else {
                // restore original settings
                view.model.set({settings: view.originalModelSettings});
                // this is about cpo slug error handling and displaying suggestion
                if (typeof r.data.error.unique !== 'undefined' && !r.data.error.unique && r.data.error.slug) {
                    view.displaySuggestion(r.data.error.slug);
                } else {
                    const $slugField = $('#js-cpo-field-slug-wrapper');
                    $slugField.addClass('uni-error-label');
                    if (typeof r.data.error !== 'undefined') {
                        console.log(r.data.error);
                    }
                }
                Builderius._unblockForm('#js-block-settings-modal', 'error');
            }
        },
        closeModal:               function () {
            this.$el.find('#uni-modal-wrapper').remove();
            this.undelegateEvents(); // tip: use undelegateEvents() if 'setElement' was used before
            this.stopListening();
            Builderius._modal_on = false;
        },
        displaySuggestion:        function (slug) {
            if ($('.uni-slug-suggestion').length) {
                $('.uni-slug-suggestion').remove();
            }
            const $slugWrap      = $('.uni-modal-row-slug-wrap');
            const $slugField     = $('#js-cpo-field-slug-wrapper');
            const $suggestionDiv = $('<div></div>');
            $slugField.addClass('uni-error-label');
            $slugWrap
                .prepend($suggestionDiv);
            $suggestionDiv
                .addClass('uni-slug-suggestion')
                .html(builderius_i18n.modal.suggestion + ':<span>' + slug + '</span>');
        },
        fetchModules:             function (e) {
            const view      = this;
            const $dropdown = $('.js-sync-posts');
            const $syncBtn  = $('#js-sync-module-btn');
            const data      = {
                action:   'uni_cpo_fetch_similar_modules',
                security: builderiusCfg.security,
                pid:      view.model.get('pid'),
                type:     view.model.get('type'),
                obj_type: view.model.get('obj_type'),
            };

            $.ajax({
                url:        builderiusCfg.ajax_url,
                data,
                dataType:   'json',
                method:     'POST',
                beforeSend: function () {
                    Builderius._blockForm('#js-block-settings-modal');
                    const $option = $('<option value="0">- None -</option>');
                    $dropdown.empty().append($option);
                    $('.uni-fetch-wrap').removeClass('uni-active');
                    $syncBtn.hide();
                },
                error:      function () {
                    Builderius._unblockForm('#js-block-settings-modal', 'error');
                    console.log('error');
                },
                success:    function (r) {
                    if (r.success) {
                        $dropdown.empty();
                        _.each(r.data, function (v, k) {
                            const $option = $(`<option value="${k}">${v}</option>`);
                            $dropdown.append($option);
                        });
                        if (view.settingSyncMethodState !== 'none') {
                            $('.uni-fetch-wrap').addClass('uni-active');
                            $syncBtn.show();
                        }
                        Builderius._unblockForm('#js-block-settings-modal', 'success');
                    } else {
                        $('.uni-fetch-wrap').removeClass('uni-active');
                        $syncBtn.hide();
                        Builderius._unblockForm('#js-block-settings-modal', 'error');
                    }
                }
            });
        },
        initialize:               function () {
            const view                  = this;
            view.settingTmpls           = {};
            view.originalModelSettings  = {};
            view.settingSyncMethodState = 'none';

            $.each(builderiusSettings, function (i, setting) {
                const $tmpl = $('#js-builderius-setting-' + setting + '-tmpl');
                if ($tmpl.length > 0) {
                    view.settingTmpls[setting] = _.template($tmpl.html(), {variable: 'data'});

                }
            });

            this.listenTo(this.model, 'request', this.ajaxSent);
        },
        readSettings:             function () {
            //console.log(this.model.toJSON());
            const modalSections    = this.$el.find('.uni-tab-content');
            const modalFieldValues = {
                data:  {},
                valid: true
            };

            modalSections.each(function () {
                const $section       = $(this);
                const sectionName    = $section.data('section');
                const $sectionGroups = $section.find('> div');

                modalFieldValues.data[sectionName] = {};

                // proceeds with groups
                $sectionGroups.each(function () {
                    const $group                                  = $(this);
                    const groupName                               = $group.data('group');
                    const $groupFields                            = $group.find('.builderius-setting-field');
                    modalFieldValues.data[sectionName][groupName] = {};

                    // ends with fields
                    $groupFields.each(function () {
                        const type                 = this.type || this.tagName.toLowerCase();
                        const $el                  = $(this);
                        const fieldParsleyInstance = $el.parsley();
                        let elName                 = $el.attr('name');

                        fieldParsleyInstance.validate();

                        if (fieldParsleyInstance.isValid()) {
                            const parsedData = Builderius._parseNameWithBrackets(elName);

                            if (parsedData.matches) {
                                if (typeof modalFieldValues.data[sectionName][groupName][parsedData.name] === 'undefined') {
                                    modalFieldValues.data[sectionName][groupName][parsedData.name] = {};
                                }
                                if (typeof modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] === 'undefined'
                                    && typeof parsedData.matches[0] !== 'undefined') {
                                    modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] = {};
                                }
                                if (typeof modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] === 'undefined'
                                    && typeof parsedData.matches[1] !== 'undefined') {
                                    modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = {};
                                }
                            }

                            if ('checkbox' === type) {
                                if ($el.hasClass('builderius-single-checkbox')) { // exception
                                    if (parsedData.matches) {
                                        if (typeof parsedData.matches[0] !== 'undefined'
                                            && typeof parsedData.matches[1] !== 'undefined') {
                                            if ($el.is(':checked')) {
                                                modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = $el.val();
                                            } else {
                                                modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = 'off';
                                            }
                                        } else if (typeof parsedData.matches[0] !== 'undefined'
                                            && typeof parsedData.matches[1] === 'undefined') {
                                            if ($el.is(':checked')) {
                                                modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] = $el.val();
                                            } else {
                                                modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] = 'off';
                                            }
                                        }
                                    } else {
                                        if ($el.is(':checked')) {
                                            modalFieldValues.data[sectionName][groupName][elName] = $el.val();
                                        } else {
                                            modalFieldValues.data[sectionName][groupName][elName] = 'off';
                                        }
                                    }
                                } else {
                                    if (parsedData.matches) {
                                        const checkboxes = [];
                                        if (typeof parsedData.matches[0] !== 'undefined'
                                            && typeof parsedData.matches[1] !== 'undefined') {
                                            $('input[name="' + this.name + '"]:checked').each(function () {
                                                checkboxes.push($(this).val());
                                            });
                                            modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = checkboxes;
                                        } else if (typeof parsedData.matches[0] !== 'undefined'
                                            && typeof parsedData.matches[1] === 'undefined') {
                                            $('input[name="' + this.name + '"]:checked').each(function () {
                                                checkboxes.push($(this).val());
                                            });
                                            modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] = checkboxes;
                                        }
                                    } else {
                                        const checkboxes      = [];
                                        const namesCheckboxes = this.name.slice(0, -2);
                                        $('input[name="' + this.name + '"]:checked').each(function () {
                                            checkboxes.push($(this).val());
                                        });
                                        modalFieldValues.data[sectionName][groupName][namesCheckboxes] = checkboxes;
                                    }
                                }
                            } else if ('radio' === type) {
                                if (parsedData.matches) {
                                    if (typeof parsedData.matches[0] !== 'undefined'
                                        && typeof parsedData.matches[1] !== 'undefined') {
                                        if ($el.is(':checked')) {
                                            modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = $el.val();
                                        } else {
                                            if ($el.hasClass('uni-cpo-deselectable-input')) {
                                                modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = '';
                                            }
                                        }
                                    } else if (typeof parsedData.matches[0] !== 'undefined'
                                        && typeof parsedData.matches[1] === 'undefined') {
                                        if ($el.is(':checked')) {
                                            modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] = $el.val();
                                        } else {
                                            if ($el.hasClass('uni-cpo-deselectable-input')) {
                                                modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] = '';
                                            }
                                        }
                                    }
                                } else {
                                    if ($el.is(':checked')) {
                                        modalFieldValues.data[sectionName][groupName][elName] = $el.val();
                                    } else {
                                        if ($el.hasClass('uni-cpo-deselectable-input')) {
                                            modalFieldValues.data[sectionName][groupName][elName] = '';
                                        }
                                    }
                                }
                            } else {
                                if (parsedData.matches) {
                                    if (typeof parsedData.matches[1] !== 'undefined') {
                                        modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]][parsedData.matches[1]] = $el.val();
                                    } else {
                                        modalFieldValues.data[sectionName][groupName][parsedData.name][parsedData.matches[0]] = $el.val();
                                    }
                                } else {
                                    modalFieldValues.data[sectionName][groupName][elName] = $el.val();
                                }
                            }
                        } else {
                            modalFieldValues.valid = false;
                        }
                    });
                });
            });

            //console.log(modalFieldValues);
            return modalFieldValues;
        },
        render:                   function () {
            Builderius._modal_on = true;
            const view           = this;

            const modal = view.template({
                model_data:           view.model.toJSON(),
                template_tab_list:    view.template_tab_list,
                template_tab_open:    view.template_tab_open,
                template_tab_close:   view.template_tab_close,
                template_group_open:  view.template_group_open,
                template_group_close: view.template_group_close,
                settingTmpls:         view.settingTmpls
            });
            view.$el.append(modal);

            // init constrained fields
            this.updateConstrained();
            // init linked fields
            Builderius._linkedFields();
            // init select with images
            const arrSelects = view.$el.find('.uni-custom-select-wrap');
            Builderius._generateCustomSelectHtml(arrSelects);

            // trigger
            $(document.body).trigger('builderius_module_settings_modal_opening', [view]);

            return this;
        },
        saveSettings:             function () {
            const view             = this;
            const type             = view.model.get('type');
            const obj_type         = view.model.get('obj_type');
            const originalSettings = JSON.parse(JSON.stringify(this.model.get('settings')));
            const modalFieldValues = view.readSettings();
            const shouldSync       = $('#js-save-to-db').is(':checked');

            if (modalFieldValues.valid && !Builderius._ajax_sent) {
                const finalSettings      = $.extend(true, {}, originalSettings, modalFieldValues.data);
                const moduleSettingsData = builderiusModules[obj_type][type];
                _.each(view.model.get('settings'), function (v, k) {
                    if (( _.isEmpty(modalFieldValues.data[k])
                            || (
                                typeof modalFieldValues.data[k].data !== 'undefined'
                                && _.isEmpty(modalFieldValues.data[k].data)
                            )
                        ) && !_.isEmpty(window[moduleSettingsData.cfg].settings[k])) {
                        finalSettings[k] = window[moduleSettingsData.cfg].settings[k];
                    }
                });
                finalSettings.cpo_suboptions = modalFieldValues.data.cpo_suboptions;
                if (shouldSync) {
                    const data = {
                        action:     'uni_cpo_save_model',
                        security:   builderiusCfg.security,
                        product_id: builderiusCfg.product.id,
                        success:    function (r) {
                            view.ajaxSynced(r);
                        },
                        error:      function (r, s) {
                            view.ajaxError(r, s);
                        },
                    };

                    view.originalModelSettings = originalSettings;
                    view.model.set({settings: finalSettings});
                    view.model.sync('create', view.model, data);
                } else {
                    view.model.set({settings: finalSettings});
                    Builderius._autosave();
                }
                //console.log(view.model.toJSON());
                $('#js-modal-save-btn').removeClass('uni-active');
            }
        },
        settingSyncMethodChanged: function () {
            const view      = this;
            const $el       = $('.js-sync-methods');
            const $dropdown = $('.js-sync-posts');
            const $syncBtn  = $('#js-sync-module-btn');
            $el.each(function () {
                const $input = $(this);
                if ($input.is(':checked')) {
                    view.settingSyncMethodState = $input.val();
                }
            });
            if (view.settingSyncMethodState !== 'none' && $dropdown.val()) {
                $('.uni-fetch-wrap').addClass('uni-active');
                $syncBtn.show();
            } else {
                $('.uni-fetch-wrap').removeClass('uni-active');
                $syncBtn.hide();
            }
        },
        settingSyncPostChanged:   function (e) {
            const view     = this;
            const $el      = $(e.target);
            const $syncBtn = $('#js-sync-module-btn');
            if (view.settingSyncMethodState !== 'none' && $el.val()) {
                $('.uni-fetch-wrap').addClass('uni-active');
                $syncBtn.show();

            } else {
                $('.uni-fetch-wrap').removeClass('uni-active');
                $syncBtn.hide();
            }
        },
        syncWithModule:           function () {
            const view      = this;
            const $dropdown = $('.js-sync-posts');

            const data = {
                action:   'uni_cpo_sync_with_module',
                security: builderiusCfg.security,
                pid:      $dropdown.val(),
                method:   view.settingSyncMethodState,
                obj_type: view.model.get('obj_type'),
            };

            $.ajax({
                url:        builderiusCfg.ajax_url,
                data,
                dataType:   'json',
                method:     'POST',
                beforeSend: function () {
                    Builderius._blockForm('#js-block-settings-modal');
                },
                error:      function () {
                    Builderius._unblockForm('#js-block-settings-modal', 'error');
                    console.log('error');
                },
                success:    function (r) {
                    if (r.success) {
                        if (view.settingSyncMethodState === 'connect') {
                            view.model.set(r.data);
                        } else if (view.settingSyncMethodState === 'duplicate') {
                            view.model.set({settings: r.data.settings});
                        }
                        if (view.model.get('obj_type') === 'option') {
                            Builderius._updateListOfVars();
                        }
                        view.closeModal();
                        Builderius._autosave();
                        Builderius._unblockForm('#js-block-settings-modal', 'success');
                    } else {
                        Builderius._unblockForm('#js-block-settings-modal', 'error');
                        console.log('error');
                    }
                }
            });
        },
        unSyncModule:             function () {
            const view = this;
            view.model.set({pid: null});
            view.closeModal();
            Builderius._autosave();
        },
        updateConstrained:        function () {
            Builderius._conditionalFields();
        }
    });

    // general settings
    Builderius.Views.GeneralSettingsModal = Backbone.View.extend({
        el:                       'body',
        events:                   {
            'click .uni-close-modal':           'closeModal',
            'click #js-modal-main-cancel-btn':  'closeModal',
            'click #js-duplicate-product-btn':  'duplicateProductSettings',
            'click #js-fetch-similar-products': 'fetchProducts',
            'click #js-modal-main-save-btn':    'saveSettings'
        },
        template:                 _.template($('#js-builderius-modal-general-settings-tmpl').html()),
        ajaxError:                function (r, s) {
            //console.log(r);
            console.log(s);
            Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'error');
        },
        ajaxSent:                 function (model, xhr, options) {
            Builderius._blockForm('#uni-modal-general-settings-wrapper');
        },
        ajaxSynced:               function (r) {
            const view = this;
            if (r.success) {
                $('#js-modal-save-btn').removeClass('uni-active');
                Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'success');
            } else {
                // restore original settings
                view.model.set({settingsData: view.originalModelSettings});
                Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'error');
            }
        },
        closeModal:               function () {
            this.$el.find('#uni-modal-wrapper').remove();
            this.undelegateEvents(); // tip: use undelegateEvents() if 'setElement' was used before
            this.stopListening();
            Builderius._modal_on = false;
        },
        duplicateProductSettings: function () {
            const view      = this;
            const $dropdown = $('.js-sync-products');
            const data      = {
                action:    'uni_cpo_duplicate_product_settings',
                security:  builderiusCfg.security,
                pid:       view.model.get('id'),
                target_id: $dropdown.val()
            };

            $.ajax({
                url:        builderiusCfg.ajax_url,
                data,
                dataType:   'json',
                method:     'POST',
                beforeSend: function () {
                    Builderius._blockForm('#uni-modal-general-settings-wrapper');
                },
                error:      function () {
                    Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'error');
                    console.log('error');
                },
                success:    function (r) {
                    if (r.success) {
                        location.reload();
                    } else {
                        Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'error');
                    }
                }
            });
        },
        fetchProducts:            function () {
            const view      = this;
            const $dropdown = $('.js-sync-products');
            const $syncBtn  = $('#js-duplicate-product-btn');
            const data      = {
                action:   'uni_cpo_fetch_similar_products',
                security: builderiusCfg.security,
                pid:      view.model.get('id')
            };

            $.ajax({
                url:        builderiusCfg.ajax_url,
                data,
                dataType:   'json',
                method:     'POST',
                beforeSend: function () {
                    Builderius._blockForm('#uni-modal-general-settings-wrapper');
                    const $option = $('<option value="0">- None -</option>');
                    $dropdown.empty().append($option);
                    $syncBtn.hide();
                },
                error:      function () {
                    Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'error');
                    console.log('error');
                },
                success:    function (r) {
                    if (r.success) {
                        $dropdown.empty();
                        _.each(r.data, function (v, k) {
                            const $option = $(`<option value="${k}">${v}</option>`);
                            $dropdown.append($option);
                        });
                        $syncBtn.show();
                        Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'success');
                    } else {
                        $syncBtn.hide();
                        Builderius._unblockForm('#uni-modal-general-settings-wrapper', 'error');
                    }
                }
            });
        },
        initialize:               function () {
            this.originalModelSettings = {};
            this.listenTo(this.model, 'request', this.ajaxSent);
        },
        render:                   function () {
            const view           = this;
            Builderius._modal_on = true;

            const modal = view.template({
                data: view.model.get('settingsData'),
                vars: Builderius._optionVars
            });
            this.$el.append(modal);

            // trigger
            $(document.body).trigger('builderius_general_settings_modal_opening', [view]);

            return this;
        },
        saveSettings:             function () {
            const view             = this;
            const modalFieldValues = Builderius._readSettingsModal(view);
            const originalSettings = JSON.parse(JSON.stringify(view.model.get('settingsData')));

            if (modalFieldValues.valid && !Builderius._ajax_sent) {
                //const finalSettings = $.extend(true, {}, originalSettings, modalFieldValues.data);
                const finalSettings = modalFieldValues.data;
                const data          = {
                    action:   'uni_cpo_save_settings_data',
                    security: builderiusCfg.security,
                    success:  function (r) {
                        view.ajaxSynced(r);
                    },
                    error:    function (r, s) {
                        view.ajaxError(r, s);
                    },
                };

                view.originalModelSettings = originalSettings;
                view.model.set({settingsData: finalSettings});
                view.model.sync('create', view.model, data);
            }
        },

    });

    // MainFormulaModalView
    Builderius.Views.MainFormulaModal = Backbone.View.extend({
        el:           'body',
        events:       {
            'click .uni-close-modal':                         'closeModal',
            'click #js-modal-main-cancel-btn':                'closeModal',
            'click #js-modal-main-save-btn':                  'saveSettings',
            'focus #uni-modal-main-formula-wrapper textarea': 'getFocused',
        },
        template:     _.template($('#js-builderius-modal-main-tmpl').html()),
        ajaxError:    function (r, s) {
            //console.log(r);
            console.log(s);
            Builderius._unblockForm('#uni-modal-wrap', 'error');
        },
        ajaxSent:     function (model, xhr, options) {
            Builderius._blockForm('#uni-modal-wrap');
        },
        ajaxSynced:   function (r) {
            const view = this;
            if (r.success) {
                $('#js-modal-save-btn').removeClass('uni-active');
                Builderius._unblockForm('#uni-modal-wrap', 'success');
            } else {
                // restore original settings
                view.model.set({formulaData: view.originalModelSettings});
                Builderius._unblockForm('#uni-modal-wrap', 'error');
            }
        },
        closeModal:   function () {
            this.$el.find('#uni-modal-main-formula-wrapper').remove();
            this.undelegateEvents(); // tip: use undelegateEvents() if 'setElement' was used before
            this.stopListening();
            Builderius._modal_on = false;
        },
        getFocused:   function () {
            const view = this;
            $(document).find('textarea.builderius-setting-field').on('focus', function () {
                view.textarea = $(this);
            });
        },
        initialize:   function () {
            this.originalModelSettings = {};
            this.listenTo(this.model, 'request', this.ajaxSent);
        },
        render:       function () {
            const view           = this;
            Builderius._modal_on = true;

            const modal = view.template({
                data: view.model.get('formulaData'),
                vars: Builderius._optionVars
            });
            this.$el.append(modal);

            // trigger
            $(document.body).trigger('builderius_main_formula_modal_opening', [view]);

            return this;
        },
        saveSettings: function () {
            const view             = this;
            const modalFieldValues = Builderius._readSettingsModal(view);
            const originalSettings = JSON.parse(JSON.stringify(view.model.get('formulaData')));

            if (modalFieldValues.valid && !Builderius._ajax_sent) {
                //const finalSettings = $.extend(true, {}, originalSettings, modalFieldValues.data);
                const finalSettings = modalFieldValues.data;
                const data          = {
                    action:   'uni_cpo_save_formula_data',
                    security: builderiusCfg.security,
                    success:  function (r) {
                        view.ajaxSynced(r);
                    },
                    error:    function (r, s) {
                        view.ajaxError(r, s);
                    },
                };

                view.originalModelSettings = originalSettings;
                view.model.set({formulaData: finalSettings});
                view.model.sync('create', view.model, data);
            }
        }
    });

    // NovModalView
    Builderius.Views.NovModal = Backbone.View.extend({
        el:                'body',
        events:            {
            'click .uni-close-modal':                              'closeModal',
            'click #js-modal-nov-cancel-btn':                      'closeModal',
            'click #js-modal-nov-save-btn':                        'saveSettings',
            'click .uni_formula_conditional_rule_remove_all':      'removeAllRules',
            'focus .uni-cpo-non-option-vars-options-row textarea': 'getFocused',
            'change [data-uni-constrainer="yes"]':                 'updateConstrained'
        },
        template:          _.template($('#js-builderius-modal-nov-tmpl').html()),
        ajaxError:         function (r, s) {
            //console.log(r);
            console.log(s);
            Builderius._unblockForm('#uni-modal-wrap', 'error');
        },
        ajaxSent:          function (model, xhr, options) {
            Builderius._blockForm('#uni-modal-wrap');
        },
        ajaxSynced:        function (r) {
            const view = this;
            if (r.success) {
                $('#js-modal-save-btn').removeClass('uni-active');
                Builderius._unblockForm('#uni-modal-wrap', 'success');
                Builderius._updateListOfVars();
            } else {
                // restore original settings
                view.model.set({novData: view.originalModelSettings});
                Builderius._unblockForm('#uni-modal-wrap', 'error');
            }
        },
        closeModal: function () {
            this.$el.find('#uni-modal-nov-wrapper').remove();
            this.undelegateEvents(); // tip: use undelegateEvents() if 'setElement' was used before
            this.stopListening();
            Builderius._modal_on = false;
        },
        getFocused: function () {
            const view = this;
            $('.uni-cpo-non-option-vars-options-row').find('textarea').on('focus', function () {
                view.textarea = $(this);
            });
        },
        initialize: function () {
            this.originalModelSettings = {};
            this.listenTo(this.model, 'request', this.ajaxSent);
        },
        removeAllRules: function () {
            const $rows = $('.uni-cpo-non-option-vars-options-row').not('.uni-cpo-non-option-vars-options-template');
            $rows.remove();
            $('.uni_cpo_non_option_vars_option_add').click();
        },
        render: function () {
            Builderius._modal_on = true;
            this.table           = [];
            const view           = this;

            const modal = view.template({
                data: view.model.get('novData'),
                vars: Builderius._optionVars
            });
            this.$el.append(modal);

            this.updateConstrained();
            this.getFocused();

            // trigger
            $(document.body).trigger('builderius_nov_modal_opening', [view]);

            return this;
        },
        saveSettings:      function () {
            const view             = this;
            const modalFieldValues = Builderius._readSettingsModal(view);
            const originalSettings = JSON.parse(JSON.stringify(view.model.get('novData')));

            if (modalFieldValues.valid && !Builderius._ajax_sent) {
                const finalSettings = modalFieldValues.data;

                const data = {
                    action:   'uni_cpo_save_nov_data',
                    security: builderiusCfg.security,
                    success:  function (r) {
                        view.ajaxSynced(r);
                    },
                    error:    function (r, s) {
                        view.ajaxError(r, s);
                    },
                };

                view.originalModelSettings = originalSettings;
                view.model.set({novData: finalSettings});
                view.model.sync('create', view.model, data);
            }
        },
        updateConstrained: function (e) {
            Builderius._conditionalFields(e);
        }
    });

    // initializes the builder
    $(function () {
        Builderius._init();
    });

}(jQuery));

(function ($) {

    $(document.body).on('builderius_module_settings_modal_opening', function (e, view) {
        // init tabs
        $('#uni-modal-tabs').tabs({
            activate: function (event, ui) {}
        });

        $('#uni-modal-tabs').find('input, select, textarea').on('change', function () {
            $('#js-modal-save-btn').addClass('uni-active');
        });
        $(document).on('change', 'input[name="cpo_slug"], input[name="cpo_rate"], input[name="cpo_order_label"], input[name^="cpo_radio_options"], input[name^="cpo_select_options"]', function () {
            $('#js-save-to-db').prop('checked', true);
        });

        if (typeof view.model.get('settings').cpo_conditional !== 'undefined') {
            const filter   = Builderius._getQueryBuilderFilter(view.model);
            const rules    = (typeof view.model.get('settings').cpo_conditional.main.cpo_fc_scheme !== 'undefined' )
                ? view.model.get('settings').cpo_conditional.main.cpo_fc_scheme
                : {};
            const $builder = $('#cpo-field-rule-builder');
            if ($builder.length > 0) {
                $builder.empty();
                if (filter.length > 0) {
                    $builder.queryBuilder({
                        icons:        {
                            add_group:    'fa fa-plus-circle',
                            add_rule:     'fa fa-plus',
                            remove_group: 'fa fa-times',
                            remove_rule:  'fa fa-times',
                            error:        'fa fa-exclamation-circle'
                        },
                        allow_groups: 1,
                        filters:      filter
                    });
                    if (!_.isEmpty(rules)) {
                        finalRule = rules.replace(/\\/g, '');
                        try {
                            $builder.queryBuilder('setRules', JSON.parse(finalRule));
                        }
                        catch (e) {
                            console.warn('It looks like there is a problem with one of the rule for queryBuilder. Read below the full error message:');
                            console.error(e);
                        }
                    }
                    $builder
                        .on('afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function (e) {
                            const $fetchButton = $(e.target).next('.js-uni-fetch-scheme');
                            $fetchButton.removeClass('uni-cpo-settings-saved').addClass('uni-cpo-settings-unsaved');
                        });
                } else {
                    $builder.text('No options variables found');
                }
            }
        }

        const $repeater = $('.uni-select-option-repeat');
        if ($repeater.length > 0) {
            $repeater.each(function () {
                $(this).repeatable_fields({
                    wrapper:               '.uni-select-option-repeat-wrapper',
                    container:             '.uni-select-option-options-wrapper',
                    row:                   '.uni-select-option-options-row',
                    add:                   '.uni_select_option_add',
                    remove:                '.uni-select-option-remove-wrapper',
                    move:                  '.uni-select-option-move-wrapper',
                    template:              '.uni-select-option-options-template',
                    is_sortable:           true,
                    before_add:            null,
                    after_add:             function (container, newRow) {
                        uni_after_add_suboption(container, newRow, '.uni-select-option-options-template');
                    },
                    before_remove:         null,
                    sortable_options:      null,
                    row_count_placeholder: '<%row-count%>',
                });
            });
        }

        Builderius._initTooltip( $('#js-block-settings-modal'), 'center bottom', 'center top-10' );

    });

    $(document.body).on('builderius_main_formula_modal_opening', function (e, view) {
        $('#uni-modal-main-formula-wrapper').on('click', '.uni-variables-list li', function () {
            if (view.textarea !== undefined) {
                view.textarea.insertAtCaret($(this).text().replace(/\s/g, ''));
            }
            return false;
        });

        const filter    = Builderius._getQueryBuilderFilter();
        const rules     = (typeof view.model.get('formulaData').formula_scheme !== 'undefined' )
            ? view.model.get('formulaData').formula_scheme
            : {};
        const $repeater = $('.uni-formula-conditional-rules-repeat');
        if ($repeater.length > 0) {
            $repeater.each(function () {
                $(this).repeatable_fields({
                    wrapper:               '.uni-formula-conditional-rules-repeat-wrapper',
                    container:             '.uni-formula-conditional-rules-options-wrapper',
                    row:                   '.uni-formula-conditional-rules-options-row',
                    add:                   '.uni_formula_conditional_rule_add',
                    remove:                '.uni_formula_conditional_rule_remove',
                    move:                  '.uni_formula_conditional_rule_move',
                    template:              '.uni-formula-conditional-rules-options-template',
                    is_sortable:           true,
                    before_add:            null,
                    after_add:             function (container, newRow) {
                        uni_after_conditional_add(container, newRow, filter, rules);
                    },
                    before_remove:         null,
                    sortable_options:      {
                        stop: function () {
                            $('.js-uni-fetch-scheme').each(function () {
                                $(this).trigger('click');
                            });
                        }
                    },
                    row_count_placeholder: '<%row-count%>',
                });
            });
        }

        const $builder = $('.cpo-query-rule-builder');
        if ($builder.length > 0) {
            $builder.empty();
            if (filter.length > 0) {
                $builder.each(function (i) {

                    const $builderId = $('#cpo-formula-rule-builder-' + i);
                    if ($builderId.length > 0) {
                        $builderId.queryBuilder({
                            icons:        {
                                add_group:    'fa fa-plus-circle',
                                add_rule:     'fa fa-plus',
                                remove_group: 'fa fa-times',
                                remove_rule:  'fa fa-times',
                                error:        'fa fa-exclamation-circle'
                            },
                            allow_groups: 1,
                            filters:      filter
                        });
                        if (typeof rules[i] !== 'undefined' && !_.isEmpty(rules[i].rule)) {
                            finalRule = rules[i].rule.replace(/\\/g, '');
                            try {
                                $builderId.queryBuilder('setRules', JSON.parse(finalRule));
                            }
                            catch (e) {
                                console.warn('It looks like there is a problem with one of the rule for queryBuilder. Read below the full error message:');
                                console.error(e);
                            }
                        }
                    }

                });
                $builder
                    .on('afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function (e) {
                        const $fetchButton = $(e.target).next('.js-uni-fetch-scheme');
                        $fetchButton.removeClass('uni-cpo-settings-saved').addClass('uni-cpo-settings-unsaved');
                    });
            } else {
                $builder.text('No options variables found');
            }
        }

    });

    $(document.body).on('builderius_nov_modal_opening', function (e, view) {
        const wrap        = '.uni-cpo-matrix-options-wrap';
        const $modal      = $('#uni-modal-nov-wrapper');
        const container   = '.uni-matrix-table-container';
        const $matrixJson = $('.uni-matrix-json');

        if ($matrixJson.length > 0) {
            $matrixJson.each(function () {
                if ($(this).val() !== '') {
                    const $wrapper = $(this).closest(wrap);
                    view.generateTable($wrapper);
                }
            });
        }

        $modal.on('click', '.uni-variables-list li', function () {
            view.textarea.insertAtCaret($(this).text().replace(/\s/g, ''));
            return false;
        });

        // init non option variables
        const $repeater = $('.uni-cpo-non-option-vars-options-repeat');
        if ($repeater.length > 0) {
            $repeater.each(function () {
                $(this).repeatable_fields({
                    wrapper:               '.uni-cpo-non-option-vars-options-repeat-wrapper',
                    container:             '.uni-cpo-non-option-vars-options-wrapper',
                    row:                   '.uni-cpo-non-option-vars-options-row',
                    add:                   '.uni_cpo_non_option_vars_option_add',
                    remove:                '.uni-cpo-non-option-vars-options-rules-remove-wrapper',
                    move:                  '.uni-cpo-non-option-vars-options-move-wrapper',
                    template:              '.uni-cpo-non-option-vars-options-template',
                    is_sortable:           true,
                    before_add:            null,
                    after_add:             function (container, newRow) {
                        uni_after_add_nov_item(container, newRow);
                    },
                    before_remove:         null,
                    after_remove:          null,
                    sortable_options:      null,
                    row_count_placeholder: '<%row-count%>',
                });
            });
        }

    });

    $(document.body).on('builderius_general_settings_modal_opening', function (e, view) {
        // init tabs
        $('#uni-modal-tabs').tabs({
            activate: function (event, ui) {}
        });

    });

    // uni_after_add_suboption
    function uni_after_add_suboption (container, newRow, settingsTmpl) {
        let count = $(container).attr('data-rf-row-count');
        count++;

        $('*', newRow).each(function () {
            $.each(this.attributes, function (index, element) {
                this.value = this.value.replace('<%row-count%>', count - 1);
            });
        });

        $(newRow).find('textarea, input').addClass('builderius-setting-field');
        $(container).attr('data-rf-row-count', count);

        update_everything(container, settingsTmpl);
    }

    //
    function uni_after_conditional_add (container, newRow, filter) {
        let count = $(container).attr('data-rf-row-count');
        count++;
        const neededIndex = count - 1;
        $('*', newRow).each(function () {
            $.each(this.attributes, function () {
                this.value = this.value.replace('<%row-count%>', neededIndex);
            });
        });

        update_everything(container, '.template');

        $(newRow).find('textarea, input[type="hidden"]').addClass('builderius-setting-field');
        $(container).attr('data-rf-row-count', count);
        const $builder = $('#cpo-formula-rule-builder-' + neededIndex);
        $builder.empty();
        if (filter.length > 0) {
            $builder.queryBuilder({
                icons:        {
                    add_group:    'fa fa-plus-circle',
                    add_rule:     'fa fa-plus',
                    remove_group: 'fa fa-times',
                    remove_rule:  'fa fa-times',
                    error:        'fa fa-exclamation-circle'
                },
                allow_groups: 1,
                filters:      filter
            });
            //
            $builder
                .on('afterAddRule afterDeleteRule.queryBuilder afterUpdateRuleValue.queryBuilder afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterUpdateGroupCondition.queryBuilder', function (e) {
                    const $fetchButton = $(e.target).next('.js-uni-fetch-scheme');
                    $fetchButton.removeClass('uni-cpo-settings-saved').addClass('uni-cpo-settings-unsaved');
                });
        } else {
            $builder.text('No options variables found');
        }
    }

    // uni_after_add_nov_item
    function uni_after_add_nov_item (container, newRow) {
        let count = $(container).attr('data-rf-row-count');
        count++;

        $('*', newRow).each(function () {
            $.each(this.attributes, function (index, element) {
                this.value = this.value.replace('<%row-count%>', count - 1);
            });
        });

        update_everything(container, '.template');

        $(newRow).find('textarea, input').addClass('builderius-setting-field');
        const $roleWrapperOne = $(newRow).find('.uni-cpo-non-option-vars-role');
        $roleWrapperOne.attr('data-uni-constrained', 'input[name=wholesale_enable]');
        $roleWrapperOne.attr('data-uni-constvalue', 'on');
        const $roleWrapperTwo = $(newRow).find('.uni-cpo-non-option-vars-role-fields');
        $roleWrapperTwo.attr('data-uni-constrained', 'input[name=wholesale_enable]');
        $roleWrapperTwo.attr('data-uni-constvalue', 'on');
        $(container).attr('data-rf-row-count', count);

        Builderius._conditionalFields();
    }

    $(document.body).on('click', '.js-uni-fetch-scheme', function () {
        const filter = Builderius._queryBuilderFilter;

        if (filter.length > 0) {
            const $link           = $(this);
            const $nearestBuilder = $link.prev('.cpo-query-rule-builder');
            const id              = $(this).data('id');
            const rules           = $nearestBuilder.queryBuilder('getRules');

            if (rules) {
                if (typeof id !== 'undefined') {
                    $('#uni_cpo_formula_rule_scheme-' + id).empty().val(JSON.stringify(rules, undefined, 2));
                } else {
                    $('#uni_cpo_field_rule_scheme').empty().val(JSON.stringify(rules, undefined, 2));
                }
                //$( ".uni-cpo-modal-field" ).trigger( "change" );
                //
                $link.removeClass('uni-cpo-settings-unsaved').addClass('uni-cpo-settings-saved');
            } else {
                $link.removeClass('uni-cpo-settings-saved').addClass('uni-cpo-settings-unsaved');
            }
        }
    });

    // converts a value of field_slug input to slug like formatted text
    $(document).on('change focusin focusout', '.js-cpo-label-slug-field', function () {
        const $el         = $(this);
        const elVal       = $el.val();
        const elData      = $el.attr('data-related-slug');
        const slugFieldId = `#builderius-setting-${elData}`;
        const $slugField  = $(slugFieldId);
        try {
            $slugField.val(elVal);
            $slugField.uniConvertToSlug();
            $slugField.parsley().validate();
        } catch (e) {
            console.error(e);
        }
    });

    // converts a value of field_slug input to slug like formatted text
    $(document).on('change focusin focusout', '.js-cpo-slug-field', function () {
        const $el = $(this);
        $el.uniConvertToSlug();
        $el.parsley().validate();
    });
    // replaces commas with dots
    $(document).on('change focusin focusout', '.js-cpo-rate-field, .rule-value-container input', function () {
        const $el = $(this);
        const val = $el.val().replace(/,/, '.');
        $el.val(val);
        $el.parsley().validate();
    });

    //
    let media_uploader;
    $(document).on('click', '.cpo-upload-attachment', function (e) {
        e.preventDefault();

        const $btn         = $(e.target);
        const $parent      = $btn.parents('div').first();
        const $input_id    = $parent.find('.cpo_suboption_attach_id');
        const $input_uri   = $parent.find('.cpo_suboption_attach_uri');
        const $input_name  = $parent.find('.cpo_suboption_attach_name');
        const $btn_remove  = $parent.find('.cpo-remove-attachment');
        const $img_preview = $parent.find('.cpo-image-preview');
        const $img_title   = $parent.find('.cpo-image-title');

        if (typeof media_uploader !== 'undefined') {
            media_uploader.close();
        }

        media_uploader = wp.media({
            frame:    'post',
            state:    'insert',
            multiple: false
        });

        media_uploader.on('insert', function () {
            const json = media_uploader.state().get('selection').first().toJSON();
            $input_id.val(json.id);
            if ($input_uri.length > 0) {
                $input_uri.val(json.url);
            }
            if ($input_name.length > 0) {
                $input_name.val(json.filename);
            }
            $btn_remove.show();
            const $img = $('<img>').attr('src', json.url);
            if ($img_preview.length > 0) {
                $img_preview.empty().append($img);
            }
            if ($img_title.length > 0) {
                $img_title.empty().append(json.filename);
            }
        });

        media_uploader.open();
    });

    //
    $(document).on('click', '.cpo-remove-attachment', function (e) {
        e.preventDefault();
        const $btn         = $(e.target);
        const $parent      = $btn.parents('div').first();
        const $input_id    = $parent.find('.cpo_suboption_attach_id');
        const $input_uri   = $parent.find('.cpo_suboption_attach_uri');
        const $input_name  = $parent.find('.cpo_suboption_attach_name');
        const $img_preview = $parent.find('.cpo-image-preview');
        const $img_title   = $parent.find('.cpo-image-title');

        $input_id.val('');
        if ($input_uri.length > 0) {
            $input_uri.val('');
        }
        if ($input_name.length > 0) {
            $input_name.val('');
        }
        $btn.hide();
        if ($img_preview.length > 0) {
            $img_preview.empty();
        }
        if ($img_title.length > 0) {
            $img_title.empty();
        }
    });

    $(document).on('click', '.uni-cpo-deselectable-input', uniMarkIt);

    // deselectable radio inputs
    let uniPreviousState = null;

    function uniMarkIt () {

        const elClickedInput = this;
        jQuery('.uni-cpo-deselectable-input').each(function () {
            if (elClickedInput !== this) {
                this.checked = false;
            }
        });

        if (uniPreviousState === this && this.checked) {
            this.checked     = false;
            uniPreviousState = null; //allow seemless selection for the same radio
        } else {
            uniPreviousState = this;
        }
    };

}(jQuery));
