<?php

/**
 * Handles all the templates
 *
 * @since 4.0.0
 */
final class Uni_Cpo_Templates
{
    /**
     * Hooks.
     *
     * @since 4.0.0
     * @return void
     */
    public static function init()
    {
        /* Actions */
        add_action( 'wp_footer', __CLASS__ . '::builder_panel', 10 );
        add_action( 'wp_footer', __CLASS__ . '::panel_autosave_item', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_general_settings', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_cart_discounts', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_main_formula', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_weight_formula', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_dimensions', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_nov', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_tab_list', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_tab_open', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_tab_close', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_group_open', 10 );
        add_action( 'wp_footer', __CLASS__ . '::modal_group_close', 10 );
        add_action( 'wp_footer', __CLASS__ . '::row_overlay', 10 );
        add_action( 'wp_footer', __CLASS__ . '::column_overlay', 10 );
        add_action( 'wp_footer', __CLASS__ . '::module_overlay', 10 );
        add_action( 'wp_footer', __CLASS__ . '::confirm_action', 10 );
    }
    
    /**
     * A template for the builder panel
     *
     * @since 4.0.0
     * @return string
     */
    public static function builder_panel()
    {
        ?>
        <script id="js-builderius-panel-tmpl" type="text/template">
        <div id="uni-builder-panel" class="uni-builder-panel uni-panel-left uni-panel-light">
            <a href="" class="uni-builder-panel-logo"></a>
            <div class="uni-builder-panel-action-btns uni-clear">
                <div
                    id="js-panel-style-switch"
                    class="uni-panel-action-btn uni-panel-style-switch"
                    data-tip="<?php 
        esc_attr_e( 'Day/night mode', 'uni-cpo' );
        ?>">
                    </div>
                <div
                    id="js-panel-position-switch"
                    class="uni-panel-action-btn uni-panel-position-switch"
                    data-tip="<?php 
        esc_attr_e( 'Left/right panel position', 'uni-cpo' );
        ?>">
                    </div>
                <a
                        href="{{- uri }}"
                        class="uni-panel-action-btn uni-panel-preview-changes-btn"
                        target="_blank"
                        data-tip="<?php 
        esc_attr_e( 'View saved content', 'uni-cpo' );
        ?>"></a>
                <div class="uni-panel-btn-wrap">
                    <div
                            id="js-revision-history-switch"
                            class="uni-panel-action-btn uni-panel-revision-history-btn"
                            data-tip="<?php 
        esc_attr_e( 'History of saved content', 'uni-cpo' );
        ?>"></div>
                    <div class="uni-revision-history-wrap">
                        <div class="uni-revision-items">
                            {{ if (! _.isEmpty(autosaveData) ) { }}
                            {{= autosaveItemTmpl({data: autosaveData}) }}
                            {{ } }}
                            <?php 
        /*
        <div class="uni-revision-item">
            <img class="uni-user-icon" src="http://via.placeholder.com/36x36" alt="">
            <div class="uni-revision-desc">
                <time datetime="2017-03-08T12:57">12:57 &middot; 08.03.2017</time>
                <p>Sergiy Galitsky</p>
            </div>
            <button class="uni-revision-btn uni-delete-revision"></button>
            <button class="uni-revision-btn uni-apply-revision"></button>
        </div>
        */
        ?>
                        </div>
                    </div>
                </div>
                <div
                        id="js-panel-general-settings"
                        class="uni-panel-action-btn uni-panel-global-styles-btn"
                        data-tip="<?php 
        esc_attr_e( 'Product general settings', 'uni-cpo' );
        ?>"></div>

				<div
                        id="js-panel-delete-changes"
                        class="uni-panel-action-btn uni-panel-delete-all-btn"
                        data-tip="<?php 
        esc_attr_e( 'Delete content', 'uni-cpo' );
        ?>"></div>

                <div class="uni-clear"></div>
                <div
                        id="js-panel-cpo-nov"
                        class="uni-panel-action-btn uni-cpo-nov-btn"
                        data-tip="<?php 
        esc_attr_e( 'Non option variables', 'uni-cpo' );
        ?>"></div>
                <?php 
        ?>
                <div
                        id="js-panel-cpo-formula"
                        class="uni-panel-action-btn uni-cpo-formula-btn"
                        data-tip="<?php 
        esc_attr_e( 'Formula and conditional logic', 'uni-cpo' );
        ?>"></div>
                <?php 
        ?>
                <div
                        id="js-panel-save-changes"
                        class="uni-panel-action-btn uni-panel-save-changes-btn"
                        data-tip="<?php 
        esc_attr_e( 'Save content', 'uni-cpo' );
        ?>"></div>
            </div>
            <?php 
        /*
                    <div class="uni-builder-panel-search">
           <input id="" type="text" name="" size="" value="" placeholder="{{- builderius_i18n.panel.smart_search }}">
                    </div>
        */
        ?>
                <div class="uni-builder-panel-blocks">
                    {{ _.each(modules, function(section, name) { }}
                    {{ if ( _.isObject(section) && ! _.isEmpty(section) ) { }}
                    <div class="uni-builder-panel-blocks-section">
                        <div class="uni-builder-panel-blocks-section-title uni-clear">
                            {{= builderius_i18n.panel.groups[name] }}
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </div>

                        <div class="uni-builder-panel-items">
                            {{ if ( name === 'option' ) { }}
                                <div class="uni-builder-panel-{{- name }}-items">
                                    {{ _.each(section, function(val, key) { }}
                                        <div class="uni-builder-panel-block js-panel-block-type-{{- name }}"
                                             data-type="{{- key }}" data-title="{{- val.title }}" data-obj_type="{{- name }}">
                                            <span class="uni-builder-panel-block-title">{{= val.title }}</span>
                                        </div>
                                    {{ }); }}
                                </div>
                            {{ } else { }}
                                {{ let i = 0; var max = Object.keys(section).length; _.each(section, function(val, key) { }}
                                    {{ if ( key === 'row' || key === 'column' ) { max--; }}
                                        <div class="uni-builder-panel-{{- key }}-items">
                                            <div class="uni-builder-panel-block js-panel-block-type-{{- key }}"
                                                 data-type="{{- key }}" data-title="{{- val.title }}" data-obj_type="{{- name }}">
                                                <span class="uni-builder-panel-block-title">{{= val.title }}</span>
                                            </div>
                                        </div>
                                    {{ } else { }}
                                        {{ if ( i === 0 ) { }}
                                            <div class="uni-builder-panel-{{- name }}-items">
                                        {{ } i++; }}
                                            <div class="uni-builder-panel-block js-panel-block-type-{{- name }}"
                                                 data-type="{{- key }}" data-title="{{- val.title }}" data-obj_type="{{- name }}">
                                                <span class="uni-builder-panel-block-title">{{= val.title }}</span>
                                            </div>
                                        {{ if ( i === max ) { }}
                                            </div>
                                        {{ } }}
                                    {{ } }}
                                {{ }); }}
                            {{ } }}
                        </div>
                    </div>
                    {{ } }}
                    {{ }); }}
                </div>
                <div class="uni-builder-panel-switch"></div>
            </div>
        </script>
		<?php 
    }
    
    /**
     * tmpl displaying autosave item
     *
     * @since 4.0.0
     * @return string
     */
    public static function panel_autosave_item()
    {
        ?>
        <script id="js-builderius-panel-autosave-item-tmpl" type="text/template">
            <div id="js-autosave-item" class="uni-revision-item">
                {{ if (data.timestamp) { }}
                <img class="uni-user-icon" src="<?php 
        echo  UniCpo()->plugin_url() . '/assets/images/autosave.png' ;
        ?>" alt="">
                <div class="uni-revision-desc">
                    {{ const momentObj = moment.unix(data.timestamp); }}
                    {{ const date = momentObj.format('YYYY/MM/DD h:m a'); }}
                    <time datetime="{{- date }}">{{- date }}</time>
                    <p><?php 
        esc_html_e( 'Autosave', 'uni-cpo' );
        ?></p>
                </div>
                <button id="js-restore-autosaved" class="uni-revision-btn uni-apply-revision"></button>
                {{ } }}
            </div>
        </script>
		<?php 
    }
    
    /**
     * tmpl displaying confirm message
     *
     * @since 4.0.0
     * @return string
     */
    public static function confirm_action()
    {
        ?>
        <script id="js-builderius-confirm-action-tmpl" type="text/template">
            <div id="js-confirm-action-wrapper" class="uni-confirm-action-wrapper uni-confirm-action-wrapper__{{- data.type }}" style="display:none;">
                <div class="uni-confirm-action">
                    <i></i>
                    <p>{{= data.message }}</p>
                    <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-cancel-btn" class="uni-btn-2 uni-modal-cancel-btn">{{= builderius_i18n.modal.cancel }}</span>
                        <span id="js-modal-delete-btn" class="uni-btn-3 uni-modal-delete-btn">{{= builderius_i18n.modal.delete }}</span>
                    </div>
                </div>
            </div>
        </script>
        <?php 
    }
    
    /**
     * A template for a module's settings modal window
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal()
    {
        ?>
        <script id="js-builderius-modal-tmpl" type="text/template">
            <div id="uni-modal-wrapper" class="uni-modal-wrapper">
                <div id="js-block-settings-modal" class="uni-modal-wrap">
                    <div class="uni-modal-head">
                        <span>{{= model_data.title }} settings</span>
                        <i class="uni-close-modal"></i>
                    </div>
                    <div id="uni-modal-tabs" class="uni-modal-tabs">
                        <ul>
                            {{ const moduleSettingsData = builderiusModules[model_data.obj_type][model_data.type]; }}
                            {{ const modSettings = window[moduleSettingsData.cfg].settings; }}
                            {{ _.each(modSettings, function(tabObj, tab) { }}
                            {{ if(! _.isEmpty(tabObj)) { }}
                                {{= template_tab_list({tab: tab}) }}
                            {{ } }}
                            {{ }); }}
                        </ul>
                        <div class="uni-modal-content-wrap">
                            {{ _.each(modSettings, function(tabObj, tab) { }}
                            {{ if(! _.isEmpty(tabObj)) { }}
                                {{= template_tab_open({tab: tab}) }}
                                    {{ _.each(tabObj, function(groupObj, group) { }}
                                    {{= template_group_open({group: group}) }}
                                        {{ _.each(groupObj, function(settingValue, settingName) { }}
                                        {{ if (settingTmpls.hasOwnProperty(settingName)) { }}
                                            {{ if (settingName === 'sync') { }}
                                                {{= settingTmpls[settingName](model_data) }}
                                            {{ } else { }}
                                                {{ let actualSettings = ''; }}
                                                {{ if (typeof model_data.settings[tab] !== 'undefined' && typeof model_data.settings[tab][group] !== 'undefined' && typeof model_data.settings[tab][group][settingName] !== 'undefined') { }}
                                                    {{ actualSettings = model_data.settings[tab][group][settingName]; }}
                                                {{ } else { }}
                                                    {{ actualSettings = settingValue; }}
                                                {{ } }}
                                                {{= settingTmpls[settingName](actualSettings) }}
                                            {{ } }}
                                        {{ } }}
                                        {{ }); }}
                                    {{= template_group_close() }}
                                    {{ }); }}
                                {{= template_tab_close() }}
                            {{ } }}
                            {{ }); }}
                        </div>
                    </div>
                    <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-cancel-btn" class="uni-btn-2 uni-modal-cancel-btn">{{= builderius_i18n.modal.cancel }}</span>
                        <span id="js-modal-save-btn" class="uni-btn-1 uni-modal-save-btn">{{= builderius_i18n.modal.save }}</span>
                        {{ if ('option' === model_data.obj_type) { }}
                        <label class="uni-save-to-db-label" for="js-save-to-db">
                            <?php 
        echo  uni_cpo_help_tip( __( 'Saving updates the builder content. Checking this will allow to save to DB as well.', 'uni-cpo' ), false, array(
            'type' => 'warning',
        ) ) ;
        ?>
                            <?php 
        esc_html_e( 'Save to DB?', 'uni-cpo' );
        ?>
                            <input id="js-save-to-db" type="checkbox" name="" value="">
                            <span></span>
                        </label>
                        {{ } }}
                    </div>
                </div>
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template for the general settings modal window
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_general_settings()
    {
        ?>
        <script id="js-builderius-modal-general-settings-tmpl" type="text/template">
            <div id="uni-modal-wrapper" class="uni-modal-wrapper">
                <div id="uni-modal-general-settings-wrapper" class="uni-modal-wrap">
                    <div class="uni-modal-head">
                        <span><?php 
        esc_html_e( 'General Settings', 'uni-cpo' );
        ?></span>
                        <i class="uni-close-modal uni-close-modal-main"></i>
                    </div>
                    <div id="uni-modal-tabs" class="uni-modal-tabs">
                        <ul>
                            <li>
                                <a href="#tab-general">
                                    <i class="uni-tab-icon-general"></i>
                                    <?php 
        esc_html_e( 'General Settings', 'uni-cpo' );
        ?>
                                </a>
                            </li>
                            <?php 
        ?>
                        </ul>
                        <div class="uni-modal-content uni-clear">
                            <div id="tab-general" class="uni-tab-content">
                                <div class="uni-form-row uni-form-row__with-checkbox">
                                    <label class="uni-main-feature__checkbox" for="uni-cpo-enable-checkbox">
                                        <input
                                                id="uni-cpo-enable-checkbox"
                                                class="builderius-setting-field builderius-single-checkbox"
                                                type="checkbox"
                                                name="cpo_enable"
                                                value="on"
                                                {{ if (data.cpo_enable=== 'on') { print(' checked'); } }}/>
                                        <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php 
        esc_html_e( 'on', 'uni-cpo' );
        ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php 
        esc_html_e( 'off', 'uni-cpo' );
        ?></span>
                                        </span>
                                    </label>
                                    <h3><?php 
        esc_html_e( 'Display custom options on the product page?', 'uni-cpo' );
        ?></h3>
                                    <p>
                                        <?php 
        esc_html_e( 'This is the main option of the plugin. By choosing "off" you are entirely disabling the work of the plugin for this product. The plugin still may work for other your products, however.', 'uni-cpo' );
        ?>
                                    </p>
                                </div>

                                <div class="uni-form-row uni-form-row__with-checkbox">
                                    <label class="uni-main-feature__checkbox" for="uni-calc-enable-checkbox">
                                        <input
                                                id="uni-calc-enable-checkbox"
                                                class="builderius-setting-field builderius-single-checkbox"
                                                type="checkbox"
                                                name="calc_enable"
                                                value="on"
                                                {{ if (data.calc_enable=== 'on') { print(' checked'); } }}/>
                                        <span class="uni-main-feature__label-wrap">
                                                <span class="uni-main-feature__checkbox-label"></span>
                                                <span class="uni-main-feature__checkbox-on"><?php 
        esc_html_e( 'on', 'uni-cpo' );
        ?></span>
                                                <span class="uni-main-feature__checkbox-off"><?php 
        esc_html_e( 'off', 'uni-cpo' );
        ?></span>
                                            </span>
                                    </label>
                                    <h3><?php 
        esc_html_e( 'Enable price calculation based on custom options?', 'uni-cpo' );
        ?></h3>
                                    <p>
                                        <?php 
        esc_html_e( 'Sometimes you just need to display custom options without using their values in a math formula as well as calculate the product price. Then just the custom options added to this product will be used only as an additional information in an order meta.', 'uni-cpo' );
        ?>
                                        <strong>
                                            <?php 
        esc_html_e( 'Important: this option will work only if you enable displaying of custom options!', 'uni-cpo' );
        ?>
                                        </strong>
                                    </p>
                                </div>

                                <div class="uni-form-row uni-form-row__with-checkbox">
                                    <label class="uni-main-feature__checkbox" for="uni-calc-btn-enable-checkbox">
                                        <input
                                                id="uni-calc-btn-enable-checkbox"
                                                class="builderius-setting-field builderius-single-checkbox"
                                                type="checkbox"
                                                name="calc_btn_enable"
                                                value="on"
                                                {{ if (data.calc_btn_enable=== 'on') { print(' checked'); } }}/>
                                        <span class="uni-main-feature__label-wrap">
                                                <span class="uni-main-feature__checkbox-label"></span>
                                                <span class="uni-main-feature__checkbox-on"><?php 
        esc_html_e( 'on', 'uni-cpo' );
        ?></span>
                                                <span class="uni-main-feature__checkbox-off"><?php 
        esc_html_e( 'off', 'uni-cpo' );
        ?></span>
                                            </span>
                                    </label>
                                    <h3>
                                        <?php 
        esc_html_e( 'Use a special "calculate" button instead of instant price calculation?', 'uni-cpo' );
        ?>
                                    </h3>
                                    <p>
                                        <?php 
        esc_html_e( 'Enable this option if you want to use "calculate" button and perform calculation on click on this button instead of instant price calculation after any options chosen/value defined.', 'uni-cpo' );
        ?>
                                    </p>
                                </div>

                                <div class="uni-form-row uni-clear">
                                    <h3>
                                        <?php 
        esc_html_e( 'Minimal price', 'uni-cpo' );
        ?>
                                    </h3>
                                    <p>
                                        <?php 
        esc_html_e( 'Calculated product price will not be lower then the value of min. price. Consider this as the lowest possible price for ordering this product regardless the calculated value by using the product custom formula. Additionally, prices of products will be displayed as "from XX" on archive pages, where XX is the minimal price value of a particular product.', 'uni-cpo' );
        ?>
                                        <strong>
                                            <?php 
        esc_html_e( ' Important: you still have to define a regular product price under General tab! Otherwise, this product will be considered as free.', 'uni-cpo' );
        ?>
                                        </strong>
                                    </p>
                                    <input
                                            type="text"
                                            class="builderius-setting-field"
                                            name="min_price"
                                            value="{{- data.min_price }}"
                                            data-parsley-trigger="change focusout submit"
                                            data-parsley-pattern="/^(\d+(?:[\.]\d{0,4})?)$/" />
                                </div>

                                <div class="uni-form-row uni-clear">
                                    <h3>
                                        <?php 
        esc_html_e( 'Maximum price', 'uni-cpo' );
        ?>
                                    </h3>
                                    <p>
                                        <?php 
        esc_html_e( 'It is possible to set a max possible price for the product. The calculated price will be compared with this value and ordering of the product will be disabled if the calculated price is bigger than this value. The text from "Text to display when ordering is disabled" setting could be displayed in this case.', 'uni-cpo' );
        ?>
                                    </p>
                                    <input
                                            type="text"
                                            class="builderius-setting-field"
                                            name="max_price"
                                            value="{{- data.max_price }}"
                                            data-parsley-trigger="change focusout submit"
                                            data-parsley-pattern="/^(\d+(?:[\.]\d{0,4})?)$/" />
                                </div>

                                <div class="uni-form-row uni-clear">
                                    <h3>
                                        <?php 
        esc_html_e( 'Text to display when ordering is disabled', 'uni-cpo' );
        ?>
                                    </h3>
                                    <p>
                                        <?php 
        esc_html_e( 'Every time you use a special word "disable" instead of actual formula, the product becomes disabled for ordering and the text below is displayed just under the product price. Leave it empty if you do not want to display this message at all.', 'uni-cpo' );
        ?>
                                    </p>
                                    <textarea
                                            class="builderius-setting-field"
                                            name="price_disabled_msg"
                                            cols="30"
                                            rows="10">{{- data.price_disabled_msg }}</textarea>
                                </div>
                                <?php 
        ?>
                            </div>
                            <?php 
        ?>
                        </div>
                    </div>
                    <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-main-cancel-btn"
                              class="uni-btn-2 uni-modal-cancel-btn"><?php 
        esc_html_e( 'Cancel', 'uni-cpo' );
        ?></span>
                        <span id="js-modal-main-save-btn"
                              class="uni-btn-1 uni-modal-save-btn"><?php 
        esc_html_e( 'Submit', 'uni-cpo' );
        ?></span>
                    </div>
                </div>
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template for the cart discounts modal window
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_cart_discounts()
    {
        ?>
    <script id="js-builderius-modal-cart-discounts-tmpl" type="text/template">
        <div id="uni-modal-wrapper" class="uni-modal-wrapper">
        </div>
    </script>
            <?php 
    }
    
    /**
     * A template for the main formula modal window
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_main_formula()
    {
        ?>
        <script id="js-builderius-modal-main-tmpl" type="text/template">
            <div id="uni-modal-main-formula-wrapper" class="uni-modal-wrapper">
                <div id="uni-modal-wrap" class="uni-modal-wrap">

                    <div class="uni-modal-head uni-modal-cpo-head">
                        <span><?php 
        esc_html_e( 'Main Formula & Formulas Conditional Logic', 'uni-cpo' );
        ?></span>
                        <i class="uni-close-modal uni-close-modal-main"></i>
                    </div>

                    <div class="uni-modal-content uni-clear">
                        <div class="uni-modal-formula">
                            <h3><?php 
        esc_html_e( 'Formula', 'uni-cpo' );
        ?></h3>
                            <p>
								<?php 
        esc_html_e( 'This is a simple formula for your product. It will be applied if no rules are added or none of them are match.', 'uni-cpo' );
        ?>
                            </p>
                        </div>
                        <div class="uni-modal-conditional-logic">
                            <label class="uni-conditional-logic__checkbox" for="uni-conditional-logic-checkbox">
                                <input
                                        id="uni-conditional-logic-checkbox"
                                        class="builderius-setting-field builderius-single-checkbox"
                                        type="checkbox"
                                        name="rules_enable"
                                        value="on"
                                        {{ if (data.rules_enable=== 'on') { print(' checked'); } }} />
                                <span class="uni-conditional-logic__label-wrap">
                                    <span class="uni-conditional-logic__checkbox-label"></span>
                                    <span class="uni-conditional-logic__checkbox-on"><?php 
        esc_html_e( 'on', 'uni-cpo' );
        ?></span>
                                    <span class="uni-conditional-logic__checkbox-off"><?php 
        esc_html_e( 'off', 'uni-cpo' );
        ?></span>
                                </span>
                            </label>
                            <h3><?php 
        esc_html_e( 'Conditional Logic', 'uni-cpo' );
        ?></h3>
                            <p>
								<?php 
        esc_html_e( 'It is also possible to use Formula Conditional Rules feature. First, enable it here and add some rules then.', 'uni-cpo' );
        ?>
                            </p>
                        </div>
                        <div class="uni-clear"></div>
                        <div class="uni-form-row">
                            <h3>
								<?php 
        esc_html_e( 'Main formula', 'uni-cpo' );
        ?>
                            </h3>
                            <textarea
                                    class="builderius-setting-field"
                                    name="main_formula"
                                    cols="30"
                                    rows="10">{{- data.main_formula }}</textarea>
                        </div>
                        <div class="uni-form-row uni-variables-list-row">
                            <h3><?php 
        esc_html_e( 'Available variables:', 'uni-cpo' );
        ?></h3>
                            <ul class="uni-variables-list uni-clear">
                                {{ _.each(vars, function(arr, group){ }}
                                {{ if (arr) { }}
                                {{ _.each(arr, function(value){ }}
                                <li class="uni-cpo-var-{{- group }}">
                                    <span>{{- '{'+value+'}' }}</span>
                                </li>
                                {{ }); }}
                                {{ } }}
                                {{ }); }}
                            </ul>
                        </div>
                        <div class="uni-form-row">
                            <h3><?php 
        esc_html_e( 'Controls:', 'uni-cpo' );
        ?></h3>
                            <div class="uni-formula-conditional-rules-repeat">
                                <div class="uni-formula-conditional-rules-repeat-wrapper">
                                    <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
                                        <span class="uni_formula_conditional_rule_add"><?php 
        esc_html_e( 'Add Rule', 'uni-cpo' );
        ?></span>
                                        <span class="uni-rules-remove-all"><?php 
        esc_html_e( 'Remove All', 'uni-cpo' );
        ?></span>
                                    </div>
                                    <div class="uni-formula-conditional-rules-options-wrapper">

                                        <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row uni-clear">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i
                                                            class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-<%row-count%>"
                                                             class="cpo-query-rule-builder"></div>
                                                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                               data-id="<%row-count%>" type="button"
                                                               value="<?php 
        esc_attr_e( 'Fetch the rule', 'uni-cpo' );
        ?>"/>
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-<%row-count%>" type="hidden"
                                                           name="formula_scheme[<%row-count%>][rule]" value=""
                                                           class="js-sort-formula_scheme-rule"/>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <textarea name="formula_scheme[<%row-count%>][formula]"
                                                              data-parsley-required="true"
                                                              data-parsley-trigger="change focusout submit"
                                                              class="js-sort-formula_scheme-formula"></textarea>
                                                </div>
                                            </div>
                                            <div class="uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i
                                                            class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                        {{ if(! _.isEmpty(data.formula_scheme) ) { }}
                                        {{ let i = 0; }}
                                        {{ _.each(data.formula_scheme, function(obj){ }}
                                        <div class="uni-formula-conditional-rules-options-row uni-clear">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i
                                                            class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-{{- i }}"
                                                             class="cpo-query-rule-builder"></div>
                                                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                               data-id="{{- i }}" type="button"
                                                               value="<?php 
        esc_attr_e( 'Fetch the rule', 'uni-cpo' );
        ?>"/>
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-{{- i }}" type="hidden"
                                                           name="formula_scheme[{{- i }}][rule]" value="{{- obj.rule }}"
                                                           class="builderius-setting-field js-sort-formula_scheme-rule"/>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <textarea name="formula_scheme[{{- i }}][formula]"
                                                              class="builderius-setting-field js-sort-formula_scheme-formula"
                                                              data-parsley-required="true"
                                                              data-parsley-trigger="change focusout submit">{{- obj.formula }}</textarea>
                                                </div>
                                            </div>
                                            <div class="uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i
                                                            class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                        {{ i++; }}
                                        {{ }); }}
                                        {{ } }}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-main-cancel-btn"
                              class="uni-btn-2 uni-modal-cancel-btn"><?php 
        esc_html_e( 'Cancel', 'uni-cpo' );
        ?></span>
                        <span id="js-modal-main-save-btn"
                              class="uni-btn-1 uni-modal-save-btn"><?php 
        esc_html_e( 'Submit', 'uni-cpo' );
        ?></span>
                    </div>

                </div>
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template for the weight formula modal window
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_weight_formula()
    {
        ?>
            <script id="js-builderius-modal-weight-tmpl" type="text/template">
                <div id="uni-modal-weight-wrapper" class="uni-modal-wrapper">
                </div>
            </script>
			<?php 
    }
    
    /**
     * A template for dimensions settings modal window
     *
     * @since 4.0.5
     * @return string
     */
    public static function modal_dimensions()
    {
        ?>
            <script id="js-builderius-modal-dimensions-tmpl" type="text/template">
                <div id="uni-modal-dimensions-wrapper" class="uni-modal-wrapper">
                </div>
            </script>
			<?php 
    }
    
    /**
     * A template for the non option variables modal window
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_nov()
    {
        ?>
        <script id="js-builderius-modal-nov-tmpl" type="text/template">
            <div id="uni-modal-nov-wrapper" class="uni-modal-wrapper">
                <div id="uni-modal-wrap" class="uni-modal-wrap">

                    <div class="uni-modal-head uni-modal-cpo-head">
                        <span><?php 
        esc_html_e( 'Non Option Variables', 'uni-cpo' );
        ?></span>
                        <i class="uni-close-modal"></i>
                    </div>
                    <div class="uni-modal-content uni-clear">
                        <div class="uni-modal-formula">
                            <label class="uni-main-feature__checkbox" for="uni-main-feature-checkbox">
                                <input
                                        id="uni-main-feature-checkbox"
                                        class="builderius-setting-field builderius-single-checkbox"
                                        type="checkbox"
                                        name="nov_enable"
                                        value="on"
                                        {{ if (data.nov_enable=== 'on') { print(' checked'); } }} />
                                <span class="uni-main-feature__label-wrap">
                                    <span class="uni-main-feature__checkbox-label"></span>
                                    <span class="uni-main-feature__checkbox-on"><?php 
        esc_html_e( 'on', 'uni-cpo' );
        ?></span>
                                    <span class="uni-main-feature__checkbox-off"><?php 
        esc_html_e( 'off', 'uni-cpo' );
        ?></span>
                                </span>
                            </label>
                            <h3><?php 
        esc_html_e( 'Non Option Variables', 'uni-cpo' );
        ?></h3>
                            <p><?php 
        esc_html_e( 'NOVs are variables without direct connection to any options.', 'uni-cpo' );
        ?></p>
                        </div>
                        <div class="uni-modal-conditional-logic">
                            <label class="uni-conditional-logic__checkbox"
                                   for="uni_cpo_non_option_vars_wholesale_enable">
                                <input
                                        id="uni_cpo_non_option_vars_wholesale_enable"
                                        class="builderius-setting-field builderius-single-checkbox"
                                        type="checkbox"
                                        name="wholesale_enable"
                                        data-uni-constrainer="yes"
                                        value="on"
                                        {{ if (data.wholesale_enable=== 'on') { print(' checked'); } }} />
                                <span class="uni-conditional-logic__label-wrap">
                                        <span class="uni-conditional-logic__checkbox-label"></span>
                                        <span class="uni-conditional-logic__checkbox-on"><?php 
        esc_html_e( 'on', 'uni-cpo' );
        ?></span>
                                        <span class="uni-conditional-logic__checkbox-off"><?php 
        esc_html_e( 'off', 'uni-cpo' );
        ?></span>
                                    </span>
                            </label>
                            <h3><?php 
        esc_html_e( 'Wholesale', 'uni-cpo' );
        ?></h3>
                            <p><?php 
        esc_html_e( 'Enabling this functionality will make it possible to set different value/formula on per user role basis', 'uni-cpo' );
        ?></p>
                        </div>
                        <div class="uni-clear"></div>
                        <div class="uni-form-row uni-variables-list-row">
                            <h3><?php 
        esc_html_e( 'Available variables:', 'uni-cpo' );
        ?></h3>
                            <ul class="uni-variables-list uni-clear">
                                {{ _.each(vars, function(arr, group){ }}
                                {{ if (arr) { }}
                                {{ _.each(arr, function(value){ }}
                                <li class="uni-cpo-var-{{- group }}">
                                    <span>{{- '{'+value+'}' }}</span>
                                </li>
                                {{ }); }}
                                {{ } }}
                                {{ }); }}
                            </ul>
                        </div>
                        <div class="uni-form-row">
                            <h3><?php 
        esc_html_e( 'Controls', 'uni-cpo' );
        ?>:</h3>
                            <div class="uni-cpo-non-option-vars-options-repeat">
                                <div class="uni-cpo-non-option-vars-options-repeat-wrapper">

                                    <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
                                        <span class="uni_cpo_non_option_vars_option_add"><?php 
        esc_html_e( 'Add Rule', 'uni-cpo' );
        ?></span>
                                        <span class="uni-rules-remove-all"><?php 
        esc_html_e( 'Remove All', 'uni-cpo' );
        ?></span>
                                    </div>

                                    <div class="uni-cpo-non-option-vars-options-wrapper">

                                        <div class="uni-cpo-non-option-vars-options-template uni-cpo-non-option-vars-options-row">
                                            <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_move"><i
                                                            class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-clear">
                                                    <span><code>{uni_nov_cpo_</code></span>
                                                    <input
                                                            type="text"
                                                            name="nov[<%row-count%>][slug]"
                                                            value=""
                                                            class="uni-cpo-modal-field uni-cpo-non-option-slug-field"
                                                            data-parsley-required="true"
                                                            data-parsley-trigger="change focusout submit"
                                                            data-parsley-notequalto=".uni-cpo-non-option-slug-field"/>
                                                    <span><code>}</code></span>
                                                </div>
                                                <?php 
        ?>
                                                <div
                                                        class="uni-cpo-not-matrix-options-wrap"
                                                        <?php 
        ?>>
                                                    <div class="uni-cpo-non-option-vars-options-content-field-wrapper">
                                                        <div class="uni-cpo-non-option-vars-options-content-formula-wrapper">
                                                            <textarea
                                                                    name="nov[<%row-count%>][formula]"
                                                                    col="10"
                                                                    row="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-cpo-non-option-vars-role">
                                                        <div class="uni-cpo-non-option-vars-options-checkboxes-wrap uni-clear">
                                                            {{ _.each(builderiusCfg.wholesale, function(val, key) { }}
                                                            <label for="id-row[<%row-count%>]-role[{{= key }}]">
                                                                <input
                                                                        id="id-row[<%row-count%>]-role[{{- key }}]"
                                                                        type="checkbox"
                                                                        name="nov[<%row-count%>][roles][]"
                                                                        data-uni-constrainer="yes"
                                                                        value="{{- key }}">
                                                                <span></span>
                                                                {{= val }}
                                                            </label>
                                                            {{ }); }}
                                                        </div>
                                                    </div>
                                                    <div class="uni-cpo-non-option-vars-role-fields">
                                                        {{ _.each(builderiusCfg.wholesale, function(val, key) { }}
                                                        <div
                                                                class="uni-cpo-non-option-vars-options-content-field-wrapper js-row-<%row-count%>-role-{{- key }}"
                                                                data-uni-constrained="input[name=nov\[<%row-count%>\]\[roles\]\[\]]"
                                                                data-uni-constvalue="{{- key }}">
                                                            <div class="uni-cpo-non-option-vars-options-content-formula-wrapper full-wrapper">
                                                                <label># {{- val }}</label>
                                                                <textarea
                                                                        name="nov[<%row-count%>][{{- key }}][formula]"
                                                                        col="10"
                                                                        row="3"
                                                                        placeholder="# {{- val }}"></textarea>
                                                            </div>
                                                        </div>
                                                        {{ }); }}
                                                    </div>
                                                </div>
                                                <?php 
        ?>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_non_option_vars_option_remove">
                                                        <i class="fa fa-times"></i>
                                                    </span>
                                            </div>
                                        </div>

                                        {{ if (data.nov) { }}
                                        {{ let i = 0; }}
                                        {{ _.each(data.nov, function(obj){ }}
                                        {{ let isRoles = false; }}
                                        {{ if (typeof obj.roles !== 'undefined' && !_.isEmpty(obj.roles)) { isRoles = true; } }}
                                        {{ if (typeof obj.matrix === 'undefined') { }}
                                        {{ obj.matrix = { enable: 'off' }; }}
                                        {{ } }}
                                        {{ convertEnable = uniGet(obj, 'convert.enable', 'off'); }}
                                        {{ convertTo = uniGet(obj, 'convert.to', ''); }}
                                        <div class="uni-cpo-non-option-vars-options-row">
                                            <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_move"><i
                                                            class="fa fa-arrows"></i></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-clear">
                                                    <span><code>{uni_nov_cpo_</code></span>
                                                    <input
                                                            type="text"
                                                            name="nov[{{- i }}][slug]"
                                                            value="{{- obj.slug }}"
                                                            class="uni-cpo-modal-field uni-cpo-non-option-slug-field builderius-setting-field"
                                                            data-parsley-required="true"
                                                            data-parsley-trigger="change focusout submit"
                                                            data-parsley-notequalto=".uni-cpo-non-option-slug-field"/>
                                                    <span><code>}</code></span>
                                                </div>
                                                <?php 
        ?>
                                                <div
                                                        class="uni-cpo-not-matrix-options-wrap"
                                                        <?php 
        ?>>
                                                    <div class="uni-cpo-non-option-vars-options-content-field-wrapper">
                                                        <div class="uni-cpo-non-option-vars-options-content-formula-wrapper">
                                                                <textarea
                                                                        class="builderius-setting-field"
                                                                        name="nov[{{- i }}][formula]"
                                                                        col="10"
                                                                        row="3">{{- obj.formula }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div
                                                            class="uni-cpo-non-option-vars-options-content-field-wrapper uni-cpo-non-option-vars-role"
                                                            data-uni-constrained="input[name=wholesale_enable]"
                                                            data-uni-constvalue="on">
                                                        <div class="uni-cpo-non-option-vars-options-checkboxes-wrap uni-clear">
                                                            {{ _.each(builderiusCfg.wholesale, function(val, key) { }}
                                                            <label for="id-row[{{- i }}]-role[{{- key }}]">
                                                                <input
                                                                        id="id-row[{{- i }}]-role[{{- key }}]"
                                                                        class="builderius-setting-field"
                                                                        type="checkbox"
                                                                        name="nov[{{- i }}][roles][]"
                                                                        data-uni-constrainer="yes"
                                                                        value="{{- key }}"
                                                                        {{ if (isRoles) { }}
                                                                        {{ if (_.indexOf(obj.roles, key) !== -1) { }}
                                                                        {{ print(' checked'); }}
                                                                        {{ } }}
                                                                {{ } }} />
                                                                <span></span>
                                                                {{- val }}
                                                            </label>
                                                            {{ }); }}
                                                        </div>
                                                    </div>
                                                    <div
                                                            class="uni-cpo-non-option-vars-role-fields"
                                                            data-uni-constrained="input[name=wholesale_enable]"
                                                            data-uni-constvalue="on">
                                                        {{ _.each(builderiusCfg.wholesale, function(val, key) { }}
                                                        {{ let formulaForRole; }}
                                                        {{ if (isRoles && typeof obj[key] !== 'undefined') { }}
                                                        {{ formulaForRole = obj[key].formula; }}
                                                        {{ } }}
                                                        <div
                                                                class="uni-cpo-non-option-vars-options-content-field-wrapper js-row-{{- i }}-role-{{- key }}"
                                                                data-uni-constrained="input[name=nov\[{{- i }}\]\[roles\]\[\]]"
                                                                data-uni-constvalue="{{- key }}">
                                                            <div class="uni-cpo-non-option-vars-options-content-formula-wrapper full-wrapper">
                                                                <label># {{- val }}</label>
                                                                <textarea
                                                                        name="nov[{{- i }}][{{- key }}][formula]"
                                                                        class="builderius-setting-field"
                                                                        col="10"
                                                                        row="3"
                                                                        placeholder="# {{- val }}">{{- formulaForRole }}</textarea>
                                                            </div>
                                                        </div>
                                                        {{ }); }}
                                                    </div>
                                                </div>
                                                <?php 
        ?>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_non_option_vars_option_remove">
                                                        <i class="fa fa-times"></i>
                                                    </span>
                                            </div>
                                        </div>
                                        {{ i++; }}
                                        {{ }); }}
                                        {{ } }}

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-nov-cancel-btn" class="uni-btn-2 uni-modal-cancel-btn">Cancel</span>
                        <span id="js-modal-nov-save-btn" class="uni-btn-1 uni-modal-save-btn">Submit</span>
                    </div>

                </div>
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template - tab list
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_tab_list()
    {
        ?>
        <script id="js-builderius-modal-tab-list-tmpl" type="text/template">
            {{ const tabData = builderius_i18n.settings_groups[tab]; }}
            <li><a href="#tab-{{- tab }}"><i class="uni-tab-icon-{{- tabData.icon }}"></i> {{= tabData.title }}</a></li>
        </script>
		<?php 
    }
    
    /**
     * A template - tab opening
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_tab_open()
    {
        ?>
        <script id="js-builderius-modal-tab-open-tmpl" type="text/template">
            <div id="tab-{{- tab }}" class="uni-tab-content" data-section="{{- tab }}">
        </script>
		<?php 
    }
    
    /**
     * A template - tab closing
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_tab_close()
    {
        ?>
        <script id="js-builderius-modal-tab-close-tmpl" type="text/template">
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template - group opening
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_group_open()
    {
        ?>
        <script id="js-builderius-modal-group-open-tmpl" type="text/template">
            <div data-group="{{- group }}">
                <div class="uni-settings-group-title">
                    {{ let title = group.replace(/_/g,' '); title = title.charAt(0).toUpperCase() + title.slice(1); }}
                    <span>{{= title }}</span>
                </div>
        </script>
		<?php 
    }
    
    /**
     * A template - group closing
     *
     * @since 4.0.0
     * @return string
     */
    public static function modal_group_close()
    {
        ?>
        <script id="js-builderius-modal-group-close-tmpl" type="text/template">
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template for the row overlay
     *
     * @since 4.0.0
     * @return string
     */
    public static function row_overlay()
    {
        ?>
        <script id="js-builderius-row-overlay-tmpl" type="text/template">
            <div class="uni-row-overlay">
                <div class="uni-block-overlay-header uni-clear">
                    <div class="uni-block-overlay-actions uni-clear">
                        <div class="uni-block-overlay-title">{{= builderius_i18n.overlay.row }}</div>
                        <i class="uni-block-move js-uni-row-move" title="{{- builderius_i18n.overlay.move }}"></i>
                        <i class="uni-block-settings js-uni-row-settings"
                           title="{{- builderius_i18n.overlay.settings }}"></i>
                        <i class="uni-block-copy js-uni-row-clone" title="{{- builderius_i18n.overlay.duplicate }}"></i>
                        <i class="uni-block-remove js-uni-row-remove" title="{{- builderius_i18n.overlay.remove }}"></i>
                    </div>
                </div>
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template for the column overlay
     *
     * @since 4.0.0
     * @return string
     */
    public static function column_overlay()
    {
        ?>
        <script id="js-builderius-col-overlay-tmpl" type="text/template">
            <div class="uni-col-overlay">
                <div class="uni-block-overlay-header uni-clear">
                    <div class="uni-block-overlay-actions uni-clear">
                        <div class="uni-block-overlay-title">{{= builderius_i18n.overlay.column }}</div>
                        <i class="uni-block-move js-uni-col-move" title="{{- builderius_i18n.overlay.move }}"></i>
                        <i class="uni-block-settings js-uni-column-settings"
                           title="{{- builderius_i18n.overlay.settings }}"></i>
                        <i class="uni-block-copy js-uni-column-clone"
                           title="{{- builderius_i18n.overlay.duplicate }}"></i>
                        <i class="uni-block-remove js-uni-column-remove"
                           title="{{- builderius_i18n.overlay.remove }}"></i>
                    </div>
                </div>
            </div>
        </script>
		<?php 
    }
    
    /**
     * A template for the module overlay
     *
     * @since 4.0.0
     * @return string
     */
    public static function module_overlay()
    {
        ?>
        <script id="js-builderius-module-overlay-tmpl" type="text/template">
            <div class="uni-module-overlay">
                <div class="uni-block-overlay-header uni-clear">
                    <div class="uni-block-overlay-actions uni-clear">
                        <div class="uni-block-overlay-title">{{= builderius_i18n.overlay.module }}</div>
                        <i class="uni-block-move js-uni-module-move" title="{{- builderius_i18n.overlay.move }}"></i>
                        <i class="uni-block-settings js-uni-module-settings"
                           title="{{- builderius_i18n.overlay.settings }}"></i>
                        <i class="uni-block-copy js-uni-module-clone"
                           title="{{- builderius_i18n.overlay.duplicate }}"></i>
                        <i class="uni-block-remove js-uni-module-remove"
                           title="{{- builderius_i18n.overlay.remove }}"></i>
                    </div>
                </div>
            </div>
        </script>
		<?php 
    }

}