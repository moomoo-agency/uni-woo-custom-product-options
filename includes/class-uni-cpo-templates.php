<?php

/**
 * Handles all the templates
 *
 * @since 4.0.0
 */
final class Uni_Cpo_Templates {

	/**
	 * Hooks.
	 *
	 * @since 4.0.0
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'wp_footer', __CLASS__ . '::builder_panel', 10 );
		add_action( 'wp_footer', __CLASS__ . '::panel_autosave_item', 10 );
		add_action( 'wp_footer', __CLASS__ . '::modal', 10 );
		add_action( 'wp_footer', __CLASS__ . '::modal_general_settings', 10 );
		add_action( 'wp_footer', __CLASS__ . '::modal_cart_discounts', 10 );
		add_action( 'wp_footer', __CLASS__ . '::modal_main_formula', 10 );
		add_action( 'wp_footer', __CLASS__ . '::modal_image_logic', 10 );
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
	static public function builder_panel() {
		?>
        <script id="js-builderius-panel-tmpl" type="text/template">
        <div id="uni-builder-panel" class="uni-builder-panel uni-panel-left uni-panel-light">
            <a href="" class="uni-builder-panel-logo"></a>
            <div class="uni-builder-panel-action-btns uni-clear">
                <div
                    id="js-panel-style-switch"
                    class="uni-panel-action-btn uni-panel-style-switch"
                    data-tip="<?php esc_attr_e( 'Day/night mode', 'uni-cpo' ); ?>">
                    </div>
                <div
                    id="js-panel-position-switch"
                    class="uni-panel-action-btn uni-panel-position-switch"
                    data-tip="<?php esc_attr_e( 'Left/right panel position', 'uni-cpo' ); ?>">
                    </div>
                <a
                        href="{{- uri }}"
                        class="uni-panel-action-btn uni-panel-preview-changes-btn"
                        target="_blank"
                        data-tip="<?php esc_attr_e( 'View saved content', 'uni-cpo' ); ?>"></a>
                <div class="uni-panel-btn-wrap">
                    <div
                            id="js-revision-history-switch"
                            class="uni-panel-action-btn uni-panel-revision-history-btn"
                            data-tip="<?php esc_attr_e( 'History of saved content', 'uni-cpo' ); ?>"></div>
                    <div class="uni-revision-history-wrap">
                        <div class="uni-revision-items">
                            {{ if (! _.isEmpty(autosaveData) ) { }}
                            {{= autosaveItemTmpl({data: autosaveData}) }}
                            {{ } }}
                            <?php /*
                            <div class="uni-revision-item">
                                <img class="uni-user-icon" src="http://via.placeholder.com/36x36" alt="">
                                <div class="uni-revision-desc">
                                    <time datetime="2017-03-08T12:57">12:57 &middot; 08.03.2017</time>
                                    <p>Sergiy Galitsky</p>
                                </div>
                                <button class="uni-revision-btn uni-delete-revision"></button>
                                <button class="uni-revision-btn uni-apply-revision"></button>
                            </div>
                            */ ?>
                        </div>
                    </div>
                </div>
                <div
                        id="js-panel-general-settings"
                        class="uni-panel-action-btn uni-panel-global-styles-btn"
                        data-tip="<?php esc_attr_e( 'Product general settings', 'uni-cpo' ); ?>"></div>

				<div
                        id="js-panel-delete-changes"
                        class="uni-panel-action-btn uni-panel-delete-all-btn"
                        data-tip="<?php esc_attr_e( 'Delete content', 'uni-cpo' ); ?>"></div>

                <div class="uni-clear"></div>
                <div
                        id="js-panel-cpo-nov"
                        class="uni-panel-action-btn uni-cpo-nov-btn"
                        data-tip="<?php esc_attr_e( 'Non option variables', 'uni-cpo' ); ?>"></div>
	            <?php
	            if ( ! UniCpo()->is_pro() ) { ?>
	            <div
			            id="js-panel-cpo-formula"
			            class="uni-panel-action-btn uni-cpo-formula-btn"
			            data-tip="<?php esc_attr_e( 'Formula and conditional logic', 'uni-cpo' ); ?>"></div>
	            <?php } ?>
                <?php
				if ( UniCpo()->is_pro() ) { ?>
                    <div
                            id="js-panel-cpo-weight"
                            class="uni-panel-action-btn uni-cpo-weight-btn"
                            data-tip="<?php esc_attr_e( 'Weight conditional logic', 'uni-cpo' ); ?>"></div>
					<div
							id="js-panel-cpo-dimensions"
							class="uni-panel-action-btn uni-cpo-dimensions-btn"
							data-tip="<?php esc_attr_e( 'Dimensions settings', 'uni-cpo' ); ?>"></div>
					<div
							id="js-panel-cpo-image-logic"
							class="uni-panel-action-btn uni-cpo-image-logic-btn"
							data-tip="<?php esc_attr_e( 'Image conditional logic', 'uni-cpo' ); ?>"></div>
	            <?php } ?>
                <div
                        id="js-panel-save-changes"
                        class="uni-panel-action-btn uni-panel-save-changes-btn"
                        data-tip="<?php esc_attr_e( 'Save content', 'uni-cpo' ); ?>"></div>
				<div class="uni-clear"></div>
	            <?php
	            if ( UniCpo()->is_pro() ) { ?>
				<div
                        id="js-panel-cpo-formula"
                        class="uni-panel-action-btn uni-cpo-formula-btn"
                        data-tip="<?php esc_attr_e( 'Formula and conditional logic', 'uni-cpo' ); ?>"></div>
	            <div
			            id="js-panel-cpo-cart-discounts"
			            class="uni-panel-action-btn uni-cpo-cart-discounts-btn"
			            data-tip="<?php esc_attr_e( 'Cart discounts', 'uni-cpo' ); ?>"></div>
	            <?php } ?>
            </div>
            <?php /*
            <div class="uni-builder-panel-search">
                <input id="" type="text" name="" size="" value="" placeholder="{{- builderius_i18n.panel.smart_search }}">
            </div>
             */ ?>
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
	static public function panel_autosave_item() {
		?>
        <script id="js-builderius-panel-autosave-item-tmpl" type="text/template">
            <div id="js-autosave-item" class="uni-revision-item">
                {{ if (data.timestamp) { }}
                <img class="uni-user-icon" src="<?php echo UniCpo()->plugin_url().'/assets/images/autosave.png'; ?>" alt="">
                <div class="uni-revision-desc">
                    {{ const momentObj = moment.unix(data.timestamp); }}
                    {{ const date = momentObj.format('YYYY/MM/DD h:m a'); }}
                    <time datetime="{{- date }}">{{- date }}</time>
                    <p><?php esc_html_e('Autosave', 'uni-cpo') ?></p>
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
    static public function confirm_action() {
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
	static public function modal() {
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
                            <?php echo uni_cpo_help_tip(
	                                __( 'Saving updates the builder content. Checking this will allow to save to DB as well.', 'uni-cpo' ),
                                    false,
                                    array( 'type' => 'warning' )
                            ); ?>
                            <?php esc_html_e( 'Save to DB?', 'uni-cpo' ) ?>
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
	static public function modal_general_settings() {
		global $product;
		$product_id   = $product->get_id();
		$product_data = Uni_Cpo_Product::get_product_data_by_id( $product_id );
		?>
        <script id="js-builderius-modal-general-settings-tmpl" type="text/template">
            <div id="uni-modal-wrapper" class="uni-modal-wrapper">
                <div id="uni-modal-general-settings-wrapper" class="uni-modal-wrap">
                    <div class="uni-modal-head">
                        <span><?php esc_html_e( 'General Settings', 'uni-cpo' ) ?></span>
                        <i class="uni-close-modal uni-close-modal-main"></i>
                    </div>
                    <div id="uni-modal-tabs" class="uni-modal-tabs">
                        <ul>
                            <li>
                                <a href="#tab-general">
                                    <i class="uni-tab-icon-general"></i>
                                    <?php esc_html_e( 'General Settings', 'uni-cpo' ) ?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-price">
                                    <i class="uni-tab-icon-price"></i>
                                    <?php esc_html_e( 'Price Settings', 'uni-cpo' ) ?>
                                </a>
                            </li>
                            <?php
                            if ( UniCpo()->is_pro() ) {
	                            ?>
	                            <li>
		                            <a href="#tab-image">
			                            <i class="uni-tab-icon-general"></i>
			                            <?php esc_html_e( 'Image Related Settings', 'uni-cpo' ) ?>
		                            </a>
	                            </li>
                                <li>
                                    <a href="#tab-import">
                                        <i class="uni-tab-icon-import"></i>
			                            <?php esc_html_e( 'Import/Export', 'uni-cpo' ) ?>
                                    </a>
                                </li>
	                            <?php
                            }
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
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
                                    </label>
                                    <h3><?php esc_html_e( 'Display custom options on the product page?', 'uni-cpo' ) ?></h3>
                                    <p>
                                        <?php esc_html_e( 'This is the main option of the plugin. By choosing "off" you are entirely disabling the work of the plugin for this product. The plugin still may work for other your products, however.', 'uni-cpo' ) ?>
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
                                                <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                                <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                            </span>
                                    </label>
                                    <h3><?php esc_html_e( 'Enable price calculation based on custom options?', 'uni-cpo' ) ?></h3>
                                    <p>
                                        <?php esc_html_e( 'Sometimes you just need to display custom options without using their values in a math formula as well as calculate the product price. Then just the custom options added to this product will be used only as an additional information in an order meta.', 'uni-cpo' ) ?>
                                        <strong>
                                            <?php esc_html_e( 'Important: this option will work only if you enable displaying of custom options!', 'uni-cpo' ) ?>
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
                                                <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                                <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                            </span>
                                    </label>
                                    <h3>
                                        <?php esc_html_e( 'Use a special "calculate" button instead of instant price calculation?', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php esc_html_e( 'Enable this option if you want to use "calculate" button and perform calculation on click on this button instead of instant price calculation after any options chosen/value defined.', 'uni-cpo' ) ?>
                                    </p>
                                </div>

                                    <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
                                        <label class="uni-main-feature__checkbox"
                                               for="uni-cart_duplicate_enable-checkbox">
                                            <input
                                                    id="uni-cart_duplicate_enable-checkbox"
                                                    class="builderius-setting-field builderius-single-checkbox"
                                                    type="checkbox"
                                                    name="cart_duplicate_enable"
                                                    value="on"
                                                    {{ if (data.cart_duplicate_enable === 'on') { print(' checked'); } }}/>
                                            <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
                                        </label>
                                        <h3><?php esc_html_e( 'Enable duplication of this product in the cart?', 'uni-cpo' ) ?></h3>
                                        <p>
			                                <?php esc_html_e( 'Cart item of this product can be duplicated in the cart if this option is enabled.', 'uni-cpo' ) ?>
                                        </p>
                                    </div>

                                    <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
                                        <label class="uni-main-feature__checkbox" for="uni-cart_edit_enable-checkbox">
                                            <input
                                                    id="uni-cart_edit_enable-checkbox"
                                                    class="builderius-setting-field builderius-single-checkbox"
                                                    type="checkbox"
                                                    name="cart_edit_enable"
                                                    value="on"
                                                    {{ if (data.cart_edit_enable === 'on') { print(' checked'); } }}/>
                                            <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
                                        </label>
                                        <h3><?php esc_html_e( 'Enable inline editing of this product in the cart?', 'uni-cpo' ) ?></h3>
                                        <p>
			                                <?php esc_html_e( 'Cart item of this product will be possible to edit inline in the cart if this option is enabled. Inline editing mode means that form fields will be shown directly in the cart. Do not forget to enable inline editing for all or some options as it also works on per option basis!', 'uni-cpo' ) ?>
                                        </p>
                                    </div>

                                    <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
                                        <label class="uni-main-feature__checkbox" for="uni-cart_edit_full_enable-checkbox">
                                            <input
                                                    id="uni-cart_edit_full_enable-checkbox"
                                                    class="builderius-setting-field builderius-single-checkbox"
                                                    type="checkbox"
                                                    name="cart_edit_full_enable"
                                                    value="on"
                                                    {{ if (data.cart_edit_full_enable === 'on') { print(' checked'); } }}/>
                                            <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
                                        </label>
                                        <h3><?php esc_html_e( 'Enable editing of this product in the cart by returning to product page?', 'uni-cpo' ) ?></h3>
                                        <p>
			                                <?php esc_html_e( 'Cart item of this product will be possible to edit in the cart in "full" edit mode if this option is enabled. It means that a customer will be redirected to the product page where options\' values will be pre-filled with those from the cart item. A customer will be redirected to the cart after clicking both "Cancel" and "Update" buttons. The related cart item options\' values will be updated if "Update" button will be clicked.', 'uni-cpo' ) ?>
                                        </p>
                                    </div>

                                <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
                                    <label class="uni-main-feature__checkbox" for="uni-silent_validation_on-checkbox">
                                        <input
                                                id="uni-silent_validation_on-checkbox"
                                                class="builderius-setting-field builderius-single-checkbox"
                                                type="checkbox"
                                                name="silent_validation_on"
                                                value="on"
                                                {{ if (data.silent_validation_on === 'on') { print(' checked'); } }}/>
                                        <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
                                    </label>
                                    <h3><?php esc_html_e( 'Enable "silent validation" mode?', 'uni-cpo' ) ?></h3>
                                    <p>
                                        <?php esc_html_e( 'Removes validation error messages for all the required fields, but still correctly validates them; useful for preventing "this field is required" messages spam in the form field.', 'uni-cpo' ) ?>
                                    </p>
                                </div>

	                            <div class="uni-form-row uni-form-row-v2 uni-clear <?php echo uni_cpo_pro_content() ?>">
		                            <h3>
			                            <?php esc_html_e( 'Custom quantity field', 'uni-cpo' ) ?>
		                            </h3>
		                            <p>
			                            <?php esc_html_e( 'By default it is a standard WC qty field. But it also can be any Text Input based custom option. Important: WC original "sold individually" setting must be enabled so this setting could work!', 'uni-cpo' ) ?>
		                            </p>
		                            <select
				                            class="uni-modal-select builderius-setting-field"
				                            name="qty_field">
			                            <option value="wc"><?php esc_html_e( 'WC qty field', 'uni-cpo' ) ?></option>
			                            {{ if(typeof vars.regular !== 'undefined'){ }}
			                            {{ _.each(vars.regular, function(value){ }}
			                            <option value="{{- value }}"{{ if (typeof data.qty_field !== 'undefined' && data.qty_field === value) { print(' selected'); } }}>{{- '{'+value+'}' }}</option>
			                            {{ }); }}
			                            {{ } }}
		                            </select>
	                            </div>

	                            <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
		                            <label class="uni-main-feature__checkbox" for="uni-sold_individually-checkbox">
			                            <input
					                            id="uni-sold_individually-checkbox"
					                            class="builderius-setting-field builderius-single-checkbox"
					                            type="checkbox"
					                            name="sold_individually"
					                            value="on"
					                            {{ if (data.sold_individually === 'on') { print(' checked'); } }}/>
			                            <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
		                            </label>
		                            <h3><?php esc_html_e( 'Sold individually?', 'uni-cpo' ) ?></h3>
		                            <p>
			                            <?php esc_html_e( 'Uni CPO uses WC original "sold individually" setting for hiding WC qty field only. Still, we need a possibility to restrict adding the same product twice to the cart even for Uni CPO enabled products. This is exactly what this setting does!', 'uni-cpo' ) ?>
		                            </p>
	                            </div>
                                <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
                                    <label class="uni-main-feature__checkbox" for="uni-reset_form_btn-checkbox">
                                        <input
                                                id="uni-reset_form_btn-checkbox"
                                                class="builderius-setting-field builderius-single-checkbox"
                                                type="checkbox"
                                                name="reset_form_btn"
                                                value="on"
                                                {{ if (data.reset_form_btn === 'on') { print(' checked'); } }}/>
                                        <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
                                    </label>
                                    <h3><?php esc_html_e( 'Reset form button?', 'uni-cpo' ) ?></h3>
                                    <p>
                                        <?php esc_html_e( 'Enable this option if you want to show "Reset all form" button', 'uni-cpo' ) ?>
                                    </p>
                                </div>
                            </div>

                            <div id="tab-price" class="uni-tab-content">
                                <div class="uni-form-row uni-clear">
                                    <h3>
                                        <?php esc_html_e( 'Minimal price', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php esc_html_e( 'Set minimal possible price for the product regardless the calculated value by using the product custom formula.', 'uni-cpo' ) ?>
                                        <strong>
                                            <?php esc_html_e( ' Important: you still have to define a regular product price under General tab! Otherwise, this product will be considered as free.', 'uni-cpo' ) ?>
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
                                        <?php esc_html_e( 'Maximum price', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php esc_html_e( 'Set maximum possible price for the product. The calculated price will be compared with this value and ordering of the product will be disabled if the calculated price is bigger than this value. If "Text to display when ordering is disabled" setting is not empty, its value will be displayed in this case.', 'uni-cpo' ) ?>
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
                                        <?php esc_html_e( 'Text to display when ordering is disabled', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php esc_html_e( 'Every time you use a special word "disable" instead of actual formula, the product becomes disabled for ordering and the text below is displayed just under the product price. Leave it empty to disable the setting.', 'uni-cpo' ) ?>
                                    </p>
                                    <textarea
                                            class="builderius-setting-field"
                                            name="price_disabled_msg"
                                            cols="30"
                                            rows="10">{{- data.price_disabled_msg }}</textarea>
                                </div>

                                <div class="uni-form-row uni-clear <?php echo uni_cpo_pro_content() ?>">
                                    <h3>
                                        <?php esc_html_e( 'Price suffix', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php esc_html_e( 'Custom price tag suffix. If set, will be shown ONLY if the product ordering is disabled for any reason.', 'uni-cpo' ) ?>
                                    </p>
                                    <input
                                            type="text"
                                            class="builderius-setting-field"
                                            name="price_suffix"
                                            value="{{- data.price_suffix }}"
                                            data-parsley-trigger="change focusout submit" />
                                </div>

                                <div class="uni-form-row uni-clear <?php echo uni_cpo_pro_content() ?>">
                                    <h3>
                                        <?php esc_html_e( 'Price postfix', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php esc_html_e( 'Custom price tag postfix. If set, will be shown in any case.', 'uni-cpo' ) ?>
                                    </p>
                                    <input
                                            type="text"
                                            class="builderius-setting-field"
                                            name="price_postfix"
                                            value="{{- data.price_postfix }}"
                                            data-parsley-trigger="change focusout submit" />
                                </div>

                                <div class="uni-form-row uni-clear <?php echo uni_cpo_pro_content() ?>">
                                    <h3>
                                        <?php esc_html_e( 'Starting price', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php esc_html_e( 'Displays a starting price (marketing price) instead of "0.00" if any of required options is empty. Leave empty to disable the setting.', 'uni-cpo' ) ?>
                                    </p>
                                    <input
                                            type="text"
                                            class="builderius-setting-field"
                                            name="starting_price"
                                            value="{{- data.starting_price }}"
                                            data-parsley-trigger="change focusout submit"
                                            data-parsley-pattern="/^(\d+(?:[\.]\d{0,4})?)$/" />
                                </div>

                                <div class="uni-form-row uni-clear <?php echo uni_cpo_pro_content() ?>">
                                    <h3>
                                        <?php esc_html_e( 'Price template for archives', 'uni-cpo' ) ?>
                                    </h3>
                                    <p>
                                        <?php _e( sprintf( 'Custom price template to be displayed on archives. Special variables (%s the complete list of such vars %s) and/or NOVs in triple curly braces can be used in the template.', '<a href="https://moomoo-agency.gitbooks.io/uni-cpo-4-documentation/content/general-settings.html" target="_blank">', '</a>'), 'uni-cpo' ) ?>
                                    </p>
                                    <input
                                            type="text"
                                            class="builderius-setting-field"
                                            name="price_archives"
                                            value="{{- data.price_archives }}"
                                            data-parsley-trigger="change focusout submit" />
                                </div>

	                            <div class="uni-form-row uni-clear <?php echo uni_cpo_pro_content() ?>">
		                            <h3>
			                            <?php esc_html_e( 'Price template for archives during active sale', 'uni-cpo' ) ?>
		                            </h3>
		                            <p>
			                            <?php esc_html_e( 'This the same as above, but this template will be chosen only during active sales campaign for this product.', 'uni-cpo' ) ?>
		                            </p>
		                            <input
				                            type="text"
				                            class="builderius-setting-field"
				                            name="price_archives_sale"
				                            value="{{- data.price_archives_sale }}"
				                            data-parsley-trigger="change focusout submit" />
	                            </div>
                            </div>

                            <?php
                            if ( UniCpo()->is_pro() ) {
	                            ?>
	                            <div id="tab-image" class="uni-tab-content">
		                            <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
			                            <label class="uni-main-feature__checkbox" for="uni-layered_image_enable-checkbox">
				                            <input
						                            id="uni-layered_image_enable-checkbox"
						                            class="builderius-setting-field builderius-single-checkbox"
						                            type="checkbox"
						                            name="layered_image_enable"
						                            value="on"
						                            {{ if (data.layered_image_enable === 'on') { print(' checked'); } }}/>
				                            <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
			                            </label>
			                            <h3><?php esc_html_e( 'Enable Colorify?', 'uni-cpo' ) ?></h3>
			                            <p>
				                            <?php esc_html_e( 'Enables "Colorify" functionality. Important: product image must be set (either alone or with product gallery images).', 'uni-cpo' ) ?>
			                            </p>
		                            </div>

		                            <div class="uni-form-row uni-form-row__with-checkbox <?php echo uni_cpo_pro_content() ?>">
			                            <label class="uni-main-feature__checkbox" for="uni-imagify_enable-checkbox">
				                            <input
						                            id="uni-imagify_enable-checkbox"
						                            class="builderius-setting-field builderius-single-checkbox"
						                            type="checkbox"
						                            name="imagify_enable"
						                            value="on"
						                            {{ console.log(data); if (data.imagify_enable === 'on') { print(' checked'); } }}/>
				                            <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
			                            </label>
			                            <h3><?php esc_html_e( 'Enable Imagify?', 'uni-cpo' ) ?></h3>
			                            <p>
				                            <?php esc_html_e( 'Enables "Imagify" functionality. Important: it does not work when Colorify is enabled! You have to use either Colorify or Imagify. Important: product image must be set (either alone or with product gallery images).', 'uni-cpo' ) ?>
			                            </p>
		                            </div>

		                            <div class="uni-form-row uni-clear <?php echo uni_cpo_pro_content() ?>">
			                            <h3>
				                            <?php esc_html_e( 'Base image for Imagify', 'uni-cpo' ) ?>
			                            </h3>
			                            <p>
				                            <?php esc_html_e( 'Adds a base image (the static one that will be placed below all other "images-layers") to be used for Imagify', 'uni-cpo' ) ?>
			                            </p>
                                        <div class = 'uni-imagify-base-image-wrapper'>
                                            <input
                                                    type="hidden"
                                                    class="builderius-setting-field"
                                                    name="imagify_base_image"
                                                    value="{{- data.imagify_base_image }}" />
                                            <button
                                                    type="button"
                                                    class="cpo-upload-attachment"
                                                    data-tip="<?php esc_attr_e('Add/Change attachment', 'uni-cpo') ?>">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button
                                                    type="button"
                                                    class="cpo-remove-attachment"
                                                    <?php if ( ! empty( $product_data['settings_data']['imagify_base_image'] ) ) {
                                                        echo 'style="display:block;"';
                                                    } ?>
                                                    data-tip="<?php esc_attr_e('Remove attachment', 'uni-cpo') ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <div class="cpo-image-preview">
                                                <?php
                                                if ( ! empty( $product_data['settings_data']['imagify_base_image'] ) ) {
                                                    $image = wp_get_attachment_image_src( $product_data['settings_data']['imagify_base_image'], 'woocommerce_single' );
                                                    echo '<img src="' . esc_url( $image[0] ) . '" />';
                                                }
                                                ?>
                                            </div>
                                        </div>
		                            </div>
	                            </div>

                                <div id="tab-import" class="uni-tab-content">
                                    <div class="uni-settings-group-title uni-settings-group-title__duplicate">
                                        <span><?php esc_html_e( 'Duplicate from another product', 'uni-cpo' ) ?></span>
                                    </div>
                                    <div class="uni-modal-row uni-clear">
                                        <div class="uni-modal-row-second uni-fetch-products uni-clear">
                                            <button
                                                    id="js-fetch-similar-products"
                                                    title="<?php esc_attr_e( 'Fetch data', 'uni-cpo' ) ?>"
                                                    class="uni-fetch-data"></button>
                                            <select class="uni-modal-select js-sync-products">
                                                <option value="0"><?php esc_html_e( '-None-', 'uni-cpo' ) ?></option>
                                            </select>
                                            <button
                                                    style="display:none;"
                                                    id="js-duplicate-product-btn"
                                                    title="<?php esc_attr_e( 'Duplicate', 'uni-cpo' ) ?>"
                                                    class="uni-btn-1 uni-save-data"><?php esc_attr_e( 'Duplicate', 'uni-cpo' ) ?></button>
                                        </div>
                                    </div>
                                    <div class="uni-settings-group-title">
                                        <span><?php esc_html_e( 'Import', 'uni-cpo' ) ?></span>
                                    </div>
                                    <div class="uni-modal-row uni-clear uni-modal-row__custom-label">
                                        <div class="uni-modal-row-first">
                                            <label for="js-cpo-import-checkbox">
                                                <input
                                                        id="js-cpo-import-preference-checkbox"
                                                        class="builderius-single-checkbox"
                                                        type="checkbox"
                                                        name=""
                                                        value="on" />
					                            <?php esc_html_e( 'remove pid attribute values from all the modules', 'uni-cpo' ) ?>
                                            </label>
                                        </div>
                                        <div class="uni-modal-row-second">
                                            <div class="uni-import-file-wrap">
                                                <input
                                                    id="js-cpo-import-file"
                                                    class=""
                                                    type="file"
                                                    name=""
                                                    value="">
                                                <label for="js-cpo-import-file">
                                                    <span></span>
                                                    <?php esc_html_e( 'Choose a file', 'uni-cpo' ) ?>
                                                </label>
                                            </div>
                                            <button
                                                    id="js-modal-main-import-btn"
                                                    class="uni-btn-1 uni-modal-main-import-btn">
					                            <?php esc_html_e( 'Import', 'uni-cpo' ) ?></button>
                                        </div>
                                    </div>
                                    <div class="uni-settings-group-title">
                                        <span><?php esc_html_e( 'Export', 'uni-cpo' ) ?></span>
                                    </div>
                                    <div class="uni-modal-row uni-clear">
                                        <div class="uni-modal-row-first">
                                            <label for="">
					                            <?php esc_html_e( 'Email', 'uni-cpo' ) ?>
                                            </label>
                                        </div>
                                        <div class="uni-modal-row-second uni-clear">
                                            <input
                                                    id="js-cpo-export-email"
                                                    class="uni-export-to-email"
                                                    type="email"
                                                    name=""
                                                    value=""
                                                    placeholder="<?php esc_html_e( 'Your email', 'uni-cpo' ) ?>">
                                            <button
                                                    id="js-modal-main-export-btn"
                                                    class="uni-btn-1 uni-modal-main-export-btn"><?php esc_html_e( 'Export', 'uni-cpo' ) ?></button>
                                        </div>
                                    </div>
                                </div>
	                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-main-cancel-btn"
                              class="uni-btn-2 uni-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'uni-cpo' ) ?></span>
                        <span id="js-modal-main-save-btn"
                              class="uni-btn-1 uni-modal-save-btn"><?php esc_html_e( 'Submit', 'uni-cpo' ) ?></span>
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
	static public function modal_cart_discounts() {
		if ( UniCpo()->is_pro() ) {
			?>
            <script id="js-builderius-modal-cart-discounts-tmpl" type="text/template">
	            {{ discountsStrategy = uniGet(data, 'cart_discounts_strategy', ''); }}
	            {{ qtyDiscounts = uniGet(data, 'qty_cart_discounts_enable', 'off'); }}
                <div id="uni-modal-wrapper" class="uni-modal-wrapper">
                    <div id="uni-modal-cart-discounts-wrapper" class="uni-modal-wrap">
                        <div class="uni-modal-head">
                            <span><?php esc_html_e( 'Cart discounts', 'uni-cpo' ) ?></span>
                            <i class="uni-close-modal uni-close-modal-main"></i>
                        </div>
                        <div id="uni-modal-tabs" class="uni-modal-tabs">
                            <ul>
                                <li>
                                    <a href="#tab-general">
                                        <i class="uni-tab-icon-general"></i>
										<?php esc_html_e( 'General Settings', 'uni-cpo' ) ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab-role-based">
                                        <i class="uni-tab-icon-role"></i>
										<?php esc_html_e( 'Role Based Discounts', 'uni-cpo' ) ?>
                                    </a>
                                </li>
	                            <li>
		                            <a href="#tab-qty-based">
			                            <i class="uni-tab-icon-role"></i>
			                            <?php esc_html_e( 'Quantity Based Discounts', 'uni-cpo' ) ?>
		                            </a>
	                            </li>
                            </ul>
                            <div class="uni-modal-content uni-clear">
                                <div id="tab-general" class="uni-tab-content">
	                                <div class="uni-form-row uni-form-row-v2 uni-clear <?php echo uni_cpo_pro_content() ?>">
		                                <h3>
			                                <?php esc_html_e( 'Cart discounts strategy', 'uni-cpo' ) ?>
		                                </h3>
		                                <p>
			                                <?php esc_html_e( 'Choose how to apply different types of cart discounts', 'uni-cpo' ) ?>
		                                </p>
		                                <select name="cart_discounts_strategy" class="uni-modal-select builderius-setting-field">
			                                <option value="highest"{{ if (discountsStrategy === 'highest') { print(' selected'); } }}><?php esc_html_e( 'Use the highest discount only', 'uni-cpo' ) ?></option>
			                                <option value="combine"{{ if (discountsStrategy === 'combine') { print(' selected'); } }}><?php esc_html_e( 'Combine all applicable discounts', 'uni-cpo' ) ?></option>
		                                </select>
	                                </div>

                                    <div class="uni-form-row uni-form-row__with-checkbox">
		                                <label class="uni-main-feature__checkbox"
		                                       for="uni-role_cart_discounts_enable-checkbox">
			                                <input
					                                id="uni-role_cart_discounts_enable-checkbox"
					                                class="builderius-setting-field builderius-single-checkbox"
					                                type="checkbox"
					                                name="role_cart_discounts_enable"
					                                value="on"
					                                {{ if (data.role_cart_discounts_enable === 'on') { print(' checked'); } }}/>
			                                <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
		                                </label>
		                                <h3><?php esc_html_e( 'Enable role based cart discounts?', 'uni-cpo' ) ?></h3>
		                                <p>
			                                <?php esc_html_e( 'This setting enables/disables cart discounts functionality based on a user role.', 'uni-cpo' ) ?>
		                                </p>
	                                </div>

	                                <div class="uni-form-row uni-form-row__with-checkbox">
		                                <label class="uni-main-feature__checkbox"
		                                       for="uni-qty_cart_discounts_enable-checkbox">
			                                <input
					                                id="uni-qty_cart_discounts_enable-checkbox"
					                                class="builderius-setting-field builderius-single-checkbox"
					                                type="checkbox"
					                                name="qty_cart_discounts_enable"
					                                value="on"
					                                {{ if (qtyDiscounts === 'on') { print(' checked'); } }}/>
			                                <span class="uni-main-feature__label-wrap">
                                            <span class="uni-main-feature__checkbox-label"></span>
                                            <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                            <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                        </span>
		                                </label>
		                                <h3><?php esc_html_e( 'Enable quantity based cart discounts?', 'uni-cpo' ) ?></h3>
		                                <p>
			                                <?php esc_html_e( 'This setting enables/disables cart discounts functionality based on the quantity of ordered items.', 'uni-cpo' ) ?>
		                                </p>
	                                </div>

                                </div>
                                <div id="tab-role-based" class="uni-tab-content">
                                    {{ const role_based = data.role_cart_discounts; }}
                                    {{ _.each(builderiusCfg.wholesale, function(v, k) { }}
                                    {{ let value = '' }}
                                    {{ if(typeof role_based[k] !== 'undefined' && typeof role_based[k].value !== 'undefined') { }}
                                    {{ value = role_based[k].value; }}
                                    {{ } }}
                                    <div class="uni-settings-group-title uni-settings-group-title__duplicate">
                                    <span>
                                        <?php esc_html_e( 'Type and value of discount for', 'uni-cpo' ) ?> {{- v }}
                                    </span>
                                    </div>
                                    <div class="uni-modal-row uni-clear">
                                        <div class="uni-modal-row-first">
                                            <select
                                                    class="uni-modal-select builderius-setting-field"
                                                    name="role_cart_discounts[{{- k }}][type]">
                                                <option value="per"><?php esc_html_e( 'Percentage', 'uni-cpo' ) ?></option>
	                                            <option value="abs"><?php esc_html_e( 'Fixed', 'uni-cpo' ) ?></option>
                                            </select>
                                        </div>
                                        <div class="uni-modal-row-second">
                                            <input
                                                    class="builderius-setting-field"
                                                    name="role_cart_discounts[{{- k }}][value]"
                                                    type="text"
                                                    value="{{- value }}"/>
                                        </div>
                                    </div>
                                    {{ }); }}
                                </div>
	                            <div id="tab-qty-based" class="uni-tab-content">
                                    <div class="uni-form-row uni-form-row-v2 uni-clear <?php echo uni_cpo_pro_content() ?>">
                                        <h3>
                                            <?php esc_html_e( 'Choose a qty field to check for qty discounts based on.', 'uni-cpo' ) ?>
                                        </h3>
                                        <p>
                                            <?php esc_html_e( 'It may be standard WC qty field or any custom option that serves as qty field. We do not recommend using the option chosen here in your price calculation formula, still it is possible. Always double check ;)', 'uni-cpo' ) ?>
                                        </p>
                                        <select
                                                class="uni-modal-select builderius-setting-field"
                                                name="qty_cart_discounts_field">
                                            <option value="wc"><?php esc_html_e( 'WC qty field', 'uni-cpo' ) ?></option>
                                            {{ if(typeof vars.regular !== 'undefined'){ }}
                                            {{ _.each(vars.regular, function(value){ }}
                                            <option value="{{- value }}"{{ if (data.qty_cart_discounts_field === value) { print(' selected'); } }}>{{- '{'+value+'}' }}</option>
                                            {{ }); }}
                                            {{ } }}
                                        </select>
                                    </div>

		                            <div class="uni-form-row uni-clear <?php echo uni_cpo_pro_content() ?>">
			                            <h3>
				                            <?php esc_html_e( 'Rules', 'uni-cpo' ) ?>
			                            </h3>
			                            <div class="uni-cpo-non-option-vars-options-repeat">
				                            <div class="uni-cpo-non-option-vars-options-repeat-wrapper">

					                            <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
						                            <span class="uni_cpo_non_option_vars_option_add"><?php esc_html_e( 'Add Rule', 'uni-cpo' ) ?></span>
						                            <span class="uni-rules-remove-all"><?php esc_html_e( 'Remove All', 'uni-cpo' ) ?></span>
					                            </div>

					                            <div class="uni-cpo-non-option-vars-options-wrapper">

						                            <div class="uni-cpo-non-option-vars-options-template uni-cpo-non-option-vars-options-row uni-query-builder-row">
							                            <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                                            <span class="uni_cpo_non_option_vars_option_move">
	                                                            <i class="fas fa-arrows-alt"></i>
                                                            </span>
							                            </div>
							                            <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
								                            <label><?php esc_html_e( 'Min. qty', 'uni-cpo' ) ?></label>
								                            <input
										                            type="text"
										                            name="qty_cart_discounts[<%row-count%>][min]"
										                            value=""
										                            class="uni-cpo-modal-field uni-cpo-input-for-nov"
										                            data-parsley-required="true"
										                            data-parsley-trigger="change focusout submit"/>

								                            <label><?php esc_html_e( 'Max. qty', 'uni-cpo' ) ?></label>
								                            <input
										                            type="text"
										                            name="qty_cart_discounts[<%row-count%>][max]"
										                            value=""
										                            class="uni-cpo-modal-field uni-cpo-input-for-nov"
										                            data-parsley-required="true"
										                            data-parsley-trigger="change focusout submit"/>

								                            <label><?php esc_html_e( 'Type', 'uni-cpo' ) ?></label>
								                            <select
										                            class="uni-modal-select"
										                            name="qty_cart_discounts[<%row-count%>][type]">
									                            <option value="per"><?php esc_html_e( 'Percentage', 'uni-cpo' ) ?></option>
									                            <option value="abs"><?php esc_html_e( 'Fixed', 'uni-cpo' ) ?></option>
								                            </select>

								                            <label><?php esc_html_e( 'Discount', 'uni-cpo' ) ?></label>
								                            <input
										                            type="text"
										                            name="qty_cart_discounts[<%row-count%>][value]"
										                            value=""
										                            class="uni-cpo-modal-field uni-cpo-input-for-nov"
										                            data-parsley-required="true"
										                            data-parsley-trigger="change focusout submit"/>

							                            </div>
							                            <div class="uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                            <span class="uni_cpo_non_option_vars_option_remove">
                                                                <i class="fas fa-times"></i>
                                                            </span>
							                            </div>
						                            </div>

						                            {{ if (data.qty_cart_discounts) { }}
						                            {{ let i = 0; }}
						                            {{ _.each(data.qty_cart_discounts, function(obj){ }}
						                            <div class="uni-cpo-non-option-vars-options-row uni-query-builder-row">
							                            <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                                            <span class="uni_cpo_non_option_vars_option_move">
	                                                            <i class="fas fa-arrows-alt"></i>
                                                            </span>
							                            </div>
							                            <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                                            <label><?php esc_html_e( 'Min. qty', 'uni-cpo' ) ?></label>
                                                            <input
                                                                    type="text"
                                                                    name="qty_cart_discounts[{{- i }}][min]"
                                                                    value="{{- obj.min }}"
                                                                    class="uni-cpo-modal-field uni-cpo-input-for-nov builderius-setting-field"
                                                                    data-parsley-required="true"
                                                                    data-parsley-trigger="change focusout submit"/>

                                                            <label><?php esc_html_e( 'Max. qty', 'uni-cpo' ) ?></label>
                                                            <input
                                                                    type="text"
                                                                    name="qty_cart_discounts[{{- i }}][max]"
                                                                    value="{{- obj.max }}"
                                                                    class="uni-cpo-modal-field uni-cpo-input-for-nov builderius-setting-field"
                                                                    data-parsley-required="true"
                                                                    data-parsley-trigger="change focusout submit"/>

                                                            <label><?php esc_html_e( 'Type', 'uni-cpo' ) ?></label>
                                                            <select
                                                                    class="uni-modal-select builderius-setting-field"
                                                                    name="qty_cart_discounts[{{- i }}][type]">
                                                                <option value="per"{{ if (obj.type === 'per') { print(' selected'); } }}><?php esc_html_e( 'Percentage', 'uni-cpo' ) ?></option>
                                                                <option value="abs"{{ if (obj.type === 'abs') { print(' selected'); } }}><?php esc_html_e( 'Fixed', 'uni-cpo' ) ?></option>
                                                            </select>

                                                            <label><?php esc_html_e( 'Discount', 'uni-cpo' ) ?></label>
                                                            <input
                                                                    type="text"
                                                                    name="qty_cart_discounts[{{- i }}][value]"
                                                                    value="{{- obj.value }}"
                                                                    class="uni-cpo-modal-field uni-cpo-input-for-nov builderius-setting-field"
                                                                    data-parsley-required="true"
                                                                    data-parsley-trigger="change focusout submit"/>

							                            </div>
							                            <div class="uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                            <span class="uni_cpo_non_option_vars_option_remove">
                                                                <i class="fas fa-times"></i>
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
                            </div>
                        </div>
                        <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-main-cancel-btn"
                              class="uni-btn-2 uni-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'uni-cpo' ) ?></span>
                            <span id="js-modal-main-save-btn"
                                  class="uni-btn-1 uni-modal-save-btn"><?php esc_html_e( 'Submit', 'uni-cpo' ) ?></span>
                        </div>
                    </div>
                </div>
            </script>
			<?php
		}
		if ( ! UniCpo()->is_pro() ) {
		    ?>
			<script id="js-builderius-modal-cart-discounts-tmpl" type="text/template">
			    <div id="uni-modal-wrapper" class="uni-modal-wrapper">
			    </div>
			</script>
            <?php
        }
	}

	/**
	 * A template for the main formula modal window
	 *
	 * @since 4.0.0
	 * @return string
	 */
	static public function modal_main_formula() {
		?>
        <script id="js-builderius-modal-main-tmpl" type="text/template">
            <div id="uni-modal-main-formula-wrapper" class="uni-modal-wrapper">
                <div id="uni-modal-wrap" class="uni-modal-wrap">

                    <div class="uni-modal-head uni-modal-cpo-head">
                        <span><?php esc_html_e( 'Main Formula & Formulas Conditional Logic', 'uni-cpo' ) ?></span>
                        <i class="uni-close-modal uni-close-modal-main"></i>
                    </div>

                    <div class="uni-modal-content uni-clear">
                        <div class="uni-modal-formula">
                            <h3><?php esc_html_e( 'Formula', 'uni-cpo' ) ?></h3>
                            <p>
								<?php esc_html_e( 'This is a simple formula for your product. It will be applied if no rules are added or none of them are match.', 'uni-cpo' ) ?>
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
                                        {{ if (data.rules_enable === 'on') { print(' checked'); } }} />
                                <span class="uni-conditional-logic__label-wrap">
                                    <span class="uni-conditional-logic__checkbox-label"></span>
                                    <span class="uni-conditional-logic__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                    <span class="uni-conditional-logic__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                </span>
                            </label>
                            <h3><?php esc_html_e( 'Conditional Logic', 'uni-cpo' ) ?></h3>
                            <p>
								<?php esc_html_e( 'It is also possible to use Formula Conditional Rules feature. First, enable it here and add some rules then.', 'uni-cpo' ) ?>
                            </p>
                        </div>
                        <div class="uni-clear"></div>
                        <div class="uni-form-row">
                            <h3>
								<?php esc_html_e( 'Main formula', 'uni-cpo' ) ?>
                            </h3>
                            <textarea
                                    class="builderius-setting-field"
                                    name="main_formula"
                                    cols="30"
                                    rows="10">{{- data.main_formula }}</textarea>
                        </div>
                        <div class="uni-form-row uni-variables-list-row">
                            <h3><?php esc_html_e( 'Available variables:', 'uni-cpo' ) ?></h3>
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
                            <h3><?php esc_html_e( 'Controls:', 'uni-cpo' ) ?></h3>
                            <div class="uni-formula-conditional-rules-repeat">
                                <div class="uni-formula-conditional-rules-repeat-wrapper">
                                    <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
                                        <span class="uni_formula_conditional_rule_add"><?php esc_html_e( 'Add Rule', 'uni-cpo' ) ?></span>
                                        <span class="uni-rules-remove-all"><?php esc_html_e( 'Remove All', 'uni-cpo' ) ?></span>
                                    </div>
                                    <div class="uni-formula-conditional-rules-options-wrapper">

                                        <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row uni-clear">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i
                                                            class="fas fa-arrows-alt"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-<%row-count%>"
                                                             class="cpo-query-rule-builder"></div>
                                                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                               data-id="<%row-count%>" type="button"
                                                               value="<?php esc_attr_e( 'Fetch the rule', 'uni-cpo' ) ?>"/>
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
                                                            class="fas fa-times"></i></span>
                                            </div>
                                        </div>
                                        {{ if(! _.isEmpty(data.formula_scheme) ) { }}
                                        {{ let i = 0; }}
                                        {{ _.each(data.formula_scheme, function(obj){ }}
                                        <div class="uni-formula-conditional-rules-options-row uni-clear">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i
                                                            class="fas fa-arrows-alt"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-{{- i }}"
                                                             class="cpo-query-rule-builder"></div>
                                                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                               data-id="{{- i }}" type="button"
                                                               value="<?php esc_attr_e( 'Fetch the rule', 'uni-cpo' ) ?>"/>
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
                                                            class="fas fa-times"></i></span>
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
                              class="uni-btn-2 uni-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'uni-cpo' ) ?></span>
                        <span id="js-modal-main-save-btn"
                              class="uni-btn-1 uni-modal-save-btn"><?php esc_html_e( 'Submit', 'uni-cpo' ) ?></span>
                    </div>

                </div>
            </div>
        </script>
		<?php
	}

	/**
	 * A template for the image conditional logic modal window
	 *
	 * @since 4.0.0
	 * @return string
	 */
	static public function modal_image_logic() {
		if ( UniCpo()->is_pro() ) {
		?>
        <script id="js-builderius-modal-image-logic-tmpl" type="text/template">
            <div id="uni-modal-image-logic-wrapper" class="uni-modal-wrapper">
                <div id="uni-modal-wrap" class="uni-modal-wrap">

                    <div class="uni-modal-head uni-modal-cpo-head">
                        <span><?php esc_html_e( 'Image Conditional Logic', 'uni-cpo' ) ?></span>
                        <i class="uni-close-modal uni-close-modal-main"></i>
                    </div>

                    <div class="uni-modal-content uni-clear">
						<div class="uni-modal-formula">
							<label class="uni-main-feature__checkbox" for="uni-main-feature-checkbox">
								<input
										id="uni-main-feature-checkbox"
										class="builderius-setting-field builderius-single-checkbox"
										type="checkbox"
										name="image_enable"
										value="on"
										{{ if (data.image_enable === 'on') { print(' checked'); } }} />
								<span class="uni-main-feature__label-wrap">
								<span class="uni-main-feature__checkbox-label"></span>
								<span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
								<span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
							</span>
							</label>
							<h3><?php esc_html_e( 'Image Conditional Logic', 'uni-cpo' ) ?></h3>
							<p>
								<?php esc_html_e( 'It is possible to change the main image of product item based on the values of custom options.', 'uni-cpo' ) ?>
							</p>
						</div>
						<div class="uni-form-row">
                            <h3><?php esc_html_e( 'Controls:', 'uni-cpo' ) ?></h3>
                            <div class="uni-formula-conditional-rules-repeat">
                                <div class="uni-formula-conditional-rules-repeat-wrapper">
                                    <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
                                        <span class="uni_formula_conditional_rule_add"><?php esc_html_e( 'Add Rule', 'uni-cpo' ) ?></span>
                                        <span class="uni-rules-remove-all"><?php esc_html_e( 'Remove All', 'uni-cpo' ) ?></span>
                                    </div>
                                    <div class="uni-formula-conditional-rules-options-wrapper">

                                        <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row uni-clear">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i
                                                            class="fas fa-arrows-alt"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-<%row-count%>"
                                                             class="cpo-query-rule-builder"></div>
                                                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                               data-id="<%row-count%>" type="button"
                                                               value="<?php esc_attr_e( 'Fetch the rule', 'uni-cpo' ) ?>"/>
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-<%row-count%>" type="hidden"
                                                           name="image_scheme[<%row-count%>][rule]" value=""
                                                           class="js-sort-image_scheme-rule"/>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-field-wrapper uni-image-conditional-image uni-row-<%row-count%>-validation-container">
													<label>
														<?php echo esc_html__( 'Image', 'uni-cpo' ) ?>
														<span class="uni-cpo-tooltip" data-tip="<?php esc_attr_e('Is used as a suboption image as well as can be used as the one that replaces the main product image', 'uni-cpo') ?>"></span>
		                                            </label>
													<input
															class="cpo_suboption_attach_id"
															name="image_scheme[<%row-count%>][attach_id]"
															value=""
															type="hidden"
															data-parsley-required="true"
															data-parsley-trigger="change focusout submit"
															data-parsley-errors-container=".uni-row-<%row-count%>-validation-container">
													<input
															class="cpo_suboption_attach_uri"
															name="image_scheme[<%row-count%>][attach_uri]"
															value=""
															type="hidden">
													<input
															class="cpo_suboption_attach_name"
															name="image_scheme[<%row-count%>][attach_name]"
															value=""
															type="hidden">
													<button
											            type="button"
											            class="cpo-upload-attachment"
											            data-tip="<?php esc_attr_e('Add/Change attachment', 'uni-cpo') ?>">
											            <i class="fas fa-pencil-alt"></i>
											        </button>
											        <button
											            type="button"
											            class="cpo-remove-attachment"
											            data-tip="<?php esc_attr_e('Remove attachment', 'uni-cpo') ?>">
											            <i class="fas fa-times"></i>
											        </button>
											        <div class="cpo-image-preview"></div>
											        <div class="cpo-image-title"></div>
                                                </div>
                                            </div>
                                            <div class="uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i
                                                            class="fas fa-times"></i></span>
                                            </div>
                                        </div>
                                        {{ if(! _.isEmpty(data.image_scheme) ) { }}
                                        {{ let i = 0; }}
                                        {{ _.each(data.image_scheme, function(obj){ }}
                                        <div class="uni-formula-conditional-rules-options-row uni-clear">
                                            <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i
                                                            class="fas fa-arrows-alt"></i></span>
                                            </div>
                                            <div class="uni-formula-conditional-rules-content-wrapper">
                                                <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <div class="uni-query-builder-wrapper">
                                                        <div id="cpo-formula-rule-builder-{{- i }}"
                                                             class="cpo-query-rule-builder"></div>
                                                        <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                               data-id="{{- i }}" type="button"
                                                               value="<?php esc_attr_e( 'Fetch the rule', 'uni-cpo' ) ?>"/>
                                                    </div>
                                                    <input id="uni_cpo_formula_rule_scheme-{{- i }}" type="hidden"
                                                           name="image_scheme[{{- i }}][rule]" value="{{- obj.rule }}"
                                                           class="builderius-setting-field js-sort-image_scheme-rule"/>
                                                </div>
												<div class="uni-formula-conditional-rules-content-field-wrapper uni-image-conditional-image uni-row-{{- i }}-validation-container">
													<label>
														<?php echo esc_html__( 'Image', 'uni-cpo' ) ?>
														<span class="uni-cpo-tooltip" data-tip="<?php esc_attr_e('Is used as a suboption image as well as can be used as the one that replaces the main product image', 'uni-cpo') ?>"></span>
		                                            </label>
													<input
															class="cpo_suboption_attach_id builderius-setting-field"
															name="image_scheme[{{- i }}][attach_id]"
															value="{{- obj.attach_id }}"
															type="hidden"
															data-parsley-required="true"
															data-parsley-trigger="change focusout submit"
															data-parsley-errors-container=".uni-row-{{- i }}-validation-container">
													<input
															class="cpo_suboption_attach_uri builderius-setting-field"
															name="image_scheme[{{- i }}][attach_uri]"
															value="{{- obj.attach_uri }}"
															type="hidden">
													<input
															class="cpo_suboption_attach_name builderius-setting-field"
															name="image_scheme[{{- i }}][attach_name]"
															value="{{- obj.attach_name }}"
															type="hidden">
													<button
											            type="button"
											            class="cpo-upload-attachment"
											            data-tip="<?php esc_attr_e('Add/Change attachment', 'uni-cpo') ?>">
											            <i class="fas fa-pencil-alt"></i>
											        </button>
											        <button
											            type="button"
											            class="cpo-remove-attachment"
											            data-tip="<?php esc_attr_e('Remove attachment', 'uni-cpo') ?>"
														{{ if ( obj.attach_uri !== '' ) { }} style="display:block;" {{ } }}>
											            <i class="fas fa-times"></i>
											        </button>
											        <div class="cpo-image-preview">
														<img src="{{- obj.attach_uri }}" />
											        </div>
											        <div class="cpo-image-title">{{- obj.attach_name }}</div>
                                                </div>
                                            </div>
                                            <div class="uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove"><i
                                                            class="fas fa-times"></i></span>
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
                              class="uni-btn-2 uni-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'uni-cpo' ) ?></span>
                        <span id="js-modal-main-save-btn"
                              class="uni-btn-1 uni-modal-save-btn"><?php esc_html_e( 'Submit', 'uni-cpo' ) ?></span>
                    </div>

                </div>
            </div>
        </script>
		<?php
		}
		if ( ! UniCpo()->is_pro() ) {
			?>
			<script id="js-builderius-modal-image-logic-tmpl" type="text/template">
                <div id="uni-modal-image-logic-wrapper" class="uni-modal-wrapper">
                </div>
            </script>
			<?php
		}
	}

	/**
	 * A template for the weight formula modal window
	 *
	 * @since 4.0.0
	 * @return string
	 */
	static public function modal_weight_formula() {
		if ( UniCpo()->is_pro() ) {
			?>
            <script id="js-builderius-modal-weight-tmpl" type="text/template">
                <div id="uni-modal-weight-wrapper" class="uni-modal-wrapper">
                    <div id="uni-modal-wrap" class="uni-modal-wrap">

                        <div class="uni-modal-head uni-modal-cpo-head">
                            <span><?php esc_html_e( 'Weight Calculation', 'uni-cpo' ) ?></span>
                            <i class="uni-close-modal uni-close-modal-main"></i>
                        </div>

                        <div class="uni-modal-content uni-clear">
                            <div class="uni-modal-formula">
                                <label class="uni-main-feature__checkbox" for="uni-main-feature-checkbox">
                                    <input
                                            id="uni-main-feature-checkbox"
                                            class="builderius-setting-field builderius-single-checkbox"
                                            type="checkbox"
                                            name="weight_enable"
                                            value="on"
                                            {{ if (data.weight_enable=== 'on') { print(' checked'); } }} />
                                    <span class="uni-main-feature__label-wrap">
                                    <span class="uni-main-feature__checkbox-label"></span>
                                    <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                    <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                </span>
                                </label>
                                <h3><?php esc_html_e( 'Weight Calculation', 'uni-cpo' ) ?></h3>
                                <p>
									<?php esc_html_e( 'It is possible to calculate the weight of the ordered product item based on the values of custom options and a custom maths formula.', 'uni-cpo' ) ?>
                                </p>
                            </div>
                            <div class="uni-modal-conditional-logic">
                                <label class="uni-conditional-logic__checkbox" for="uni-conditional-logic-checkbox">
                                    <input
                                            id="uni-conditional-logic-checkbox"
                                            class="builderius-setting-field builderius-single-checkbox"
                                            type="checkbox"
                                            name="weight_rules_enable"
                                            value="on"
                                            {{ if (data.weight_rules_enable=== 'on') { print(' checked'); } }} />
                                    <span class="uni-conditional-logic__label-wrap">
                                    <span class="uni-conditional-logic__checkbox-label"></span>
                                    <span class="uni-conditional-logic__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                    <span class="uni-conditional-logic__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                </span>
                                </label>
                                <h3><?php esc_html_e( 'Conditional Logic', 'uni-cpo' ) ?></h3>
                                <p>
									<?php esc_html_e( 'It is also possible to use Weight Formula Conditional Rules feature.', 'uni-cpo' ) ?>
                                </p>
                            </div>
                            <div class="uni-clear"></div>
                            <div class="uni-form-row">
                                <h3>
									<?php esc_html_e( 'Main formula for weight', 'uni-cpo' ) ?>
                                </h3>
                                <textarea
                                        class="builderius-setting-field"
                                        name="main_weight_formula"
                                        cols="30"
                                        rows="10">{{- data.main_weight_formula }}</textarea>
                            </div>
                            <div class="uni-form-row uni-variables-list-row">
                                <h3><?php esc_html_e( 'Available variables:', 'uni-cpo' ) ?></h3>
                                <ul class="uni-variables-list uni-clear">
                                    {{ _.each(vars, function(arr, group){ }}
                                    {{ if (arr) { }}
                                    {{ _.each(arr, function(value){ }}
                                    <li class="uni-cpo-var-{{- group }}">
                                        <span>{{= '{'+value+'}' }}</span>
                                    </li>
                                    {{ }); }}
                                    {{ } }}
                                    {{ }); }}
                                </ul>
                            </div>
                            <div class="uni-form-row">
                                <h3><?php esc_html_e( 'Controls:', 'uni-cpo' ) ?></h3>
                                <div class="uni-formula-conditional-rules-repeat">
                                    <div class="uni-formula-conditional-rules-repeat-wrapper">
                                        <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
                                            <span class="uni_formula_conditional_rule_add"><?php esc_html_e( 'Add Rule', 'uni-cpo' ) ?></span>
                                            <span class="uni-rules-remove-all"><?php esc_html_e( 'Remove All', 'uni-cpo' ) ?></span>
                                        </div>
                                        <div class="uni-formula-conditional-rules-options-wrapper">

                                            <div class="uni-formula-conditional-rules-options-template uni-formula-conditional-rules-options-row uni-clear">
                                                <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move">
                                                    <i class="fas fa-arrows-alt"></i>
                                                </span>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-wrapper">
                                                    <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                        <div class="uni-query-builder-wrapper">
                                                            <div id="cpo-formula-rule-builder-<%row-count%>"
                                                                 class="cpo-query-rule-builder"></div>
                                                            <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                                   data-id="<%row-count%>" type="button"
                                                                   value="<?php esc_attr_e( 'Fetch the rule', 'uni-cpo' ) ?>"/>
                                                        </div>
                                                        <input id="uni_cpo_formula_rule_scheme-<%row-count%>"
                                                               type="hidden"
                                                               name="weight_scheme[<%row-count%>][rule]" value=""
                                                               class="js-sort-formula_scheme-rule"/>
                                                    </div>
                                                    <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <textarea name="weight_scheme[<%row-count%>][formula]"
                                                              data-parsley-required="true"
                                                              data-parsley-trigger="change focusout submit"
                                                              class="js-sort-formula_scheme-formula"></textarea>
                                                    </div>
                                                </div>
                                                <div class="uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove">
                                                    <i class="fas fa-times"></i>
                                                </span>
                                                </div>
                                            </div>
                                            {{ if(! _.isEmpty(data.weight_scheme) ) { }}
                                            {{ let i = 0; }}
                                            {{ _.each(data.weight_scheme, function(obj){ }}
                                            <div class="uni-formula-conditional-rules-options-row uni-clear">
                                                <div class="uni-formula-conditional-rules-move-wrapper">
                                                <span class="uni_formula_conditional_rule_move"><i
                                                            class="fas fa-arrows-alt"></i></span>
                                                </div>
                                                <div class="uni-formula-conditional-rules-content-wrapper">
                                                    <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                        <div class="uni-query-builder-wrapper">
                                                            <div id="cpo-formula-rule-builder-{{- i }}"
                                                                 class="cpo-query-rule-builder"></div>
                                                            <input class="js-uni-fetch-scheme uni-cpo-settings-btn uni-cpo-settings-saved"
                                                                   data-id="{{- i }}" type="button"
                                                                   value="<?php esc_attr_e( 'Fetch the rule', 'uni-cpo' ) ?>"/>
                                                        </div>
                                                        <input id="uni_cpo_formula_rule_scheme-{{- i }}" type="hidden"
                                                               name="weight_scheme[{{- i }}][rule]"
                                                               value="{{- obj.rule }}"
                                                               class="builderius-setting-field js-sort-formula_scheme-rule"/>
                                                    </div>
                                                    <div class="uni-formula-conditional-rules-content-field-wrapper">
                                                    <textarea name="weight_scheme[{{- i }}][formula]"
                                                              class="builderius-setting-field js-sort-formula_scheme-formula"
                                                              data-parsley-required="true"
                                                              data-parsley-trigger="change focusout submit">{{- obj.formula }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="uni-formula-conditional-rules-remove-wrapper">
                                                <span class="uni_formula_conditional_rule_remove">
                                                    <i class="fas fa-times"></i>
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
                        <span id="js-modal-main-cancel-btn"
                              class="uni-btn-2 uni-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'uni-cpo' ) ?></span>
                            <span id="js-modal-main-save-btn"
                                  class="uni-btn-1 uni-modal-save-btn"><?php esc_html_e( 'Submit', 'uni-cpo' ) ?></span>
                        </div>

                    </div>
                </div>
            </script>
			<?php
		}
		if ( ! UniCpo()->is_pro() ) {
			?>
            <script id="js-builderius-modal-weight-tmpl" type="text/template">
                <div id="uni-modal-weight-wrapper" class="uni-modal-wrapper">
                </div>
            </script>
			<?php
		}
	}

	/**
	 * A template for dimensions settings modal window
	 *
	 * @since 4.0.5
	 * @return string
	 */
	static public function modal_dimensions() {
		if ( UniCpo()->is_pro() ) {
			?>
            <script id="js-builderius-modal-dimensions-tmpl" type="text/template">
                <div id="uni-modal-dimensions-wrapper" class="uni-modal-wrapper">
                    <div id="uni-modal-wrap" class="uni-modal-wrap">

                        <div class="uni-modal-head uni-modal-cpo-head">
                            <span><?php esc_html_e( 'Dimensions Settings', 'uni-cpo' ) ?></span>
                            <i class="uni-close-modal uni-close-modal-main"></i>
                        </div>

                        <div class="uni-modal-content uni-clear">
                            <div class="uni-form-row uni-form-row__with-checkbox">
                                <label class="uni-main-feature__checkbox" for="uni-main-feature-checkbox">
                                    <input
                                            id="uni-main-feature-checkbox"
                                            class="builderius-setting-field builderius-single-checkbox"
                                            type="checkbox"
                                            name="dimensions_enable"
                                            value="on"
                                            {{ if (data.dimensions_enable === 'on') { print(' checked'); } }} />
                                    <span class="uni-main-feature__label-wrap">
                                    <span class="uni-main-feature__checkbox-label"></span>
                                    <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                    <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                </span>
                                </label>
                                <h3><?php esc_html_e( 'Dimensions Calculation', 'uni-cpo' ) ?></h3>
                                <p>
									<?php esc_html_e( 'It is possible to enable setting ordered product dimensions dynamically based on chosen custom options', 'uni-cpo' ) ?>
                                </p>
                            </div>
                            <div class="uni-clear"></div>
                            <div class="uni-form-row">
                                <h3>
									<?php esc_html_e( 'Measurement unit', 'uni-cpo' ) ?>
                                </h3>
                                <p><?php echo sprintf( __('The following measurement unit is being used in your store: %s (according to the WC settings). The same unit will be used by your shipping plugin (if you use any).', 'uni-cpo'), strtolower( get_option( 'woocommerce_dimension_unit' ) ) ) ?></p>
                                <p>
		                            <?php esc_html_e( 'Additionally, you may use this setting if you want to let you customers to choose an input measurement unit dynamically. Select the option which handles this. The values of the options responsible for width, height and length will be automatically converted FROM the unit chosen in this option TO the measurement unit of your store before are being sent both to shipping plugin and used in price calculation.', 'uni-cpo' ) ?>
                                </p>
                                <select class="builderius-setting-field uni-modal-select" name="d_unit_option">
                                    <option value=""><?php esc_html_e( '- Not selected -', 'uni-cpo' ) ?></option>
                                    {{ _.each(vars.regular, function(value){ }}
                                    <option value="{{= value }}"{{ if (data.d_unit_option === value) { print(' selected'); } }}>{{= '{'+value+'}' }}</option>
                                    {{ }); }}
                                </select>
                            </div>
                            <div class="uni-form-row">
                                <h3>
			                        <?php esc_html_e( 'Length', 'uni-cpo' ) ?>
                                </h3>
								<div class="uni-form-row__with-checkbox uni-clear">
									<label class="uni-regular-setting__checkbox" for="uni-convert-length-checkbox">
	                                    <input
	                                            id="uni-convert-length-checkbox"
	                                            class="builderius-setting-field builderius-single-checkbox"
	                                            type="checkbox"
	                                            name="convert_length"
	                                            value="on"
	                                            {{ if (data.convert_length === 'on') { print(' checked'); } }} />
	                                    <span class="uni-regular-setting__label-wrap">
		                                    <span class="uni-regular-setting__checkbox-label"></span>
		                                    <span class="uni-regular-setting__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
		                                    <span class="uni-regular-setting__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
		                                </span>
	                                </label>
									<h3><?php esc_html_e( 'Convert length automatically?', 'uni-cpo' ) ?></h3>
								</div>
                                <p>
			                        <?php esc_html_e( 'Select the option which value will be used as product length.', 'uni-cpo' ) ?>
                                </p>
                                <select class="builderius-setting-field uni-modal-select" name="d_length_option">
                                    <option value=""><?php esc_html_e( '- Not selected -', 'uni-cpo' ) ?></option>
                                    {{ _.each(vars.regular, function(value){ }}
                                    <option value="{{= value }}"{{ if (data.d_length_option === value) { print(' selected'); } }}>{{= '{'+value+'}' }}</option>
                                    {{ }); }}
                                    {{ _.each(vars.nov, function(value){ }}
                                    <option value="{{= value }}"{{ if (data.d_length_option === value) { print(' selected'); } }}>{{= '{'+value+'}' }}</option>
                                    {{ }); }}
                                </select>
                            </div>
                            <div class="uni-form-row">
                                <h3>
			                        <?php esc_html_e( 'Width', 'uni-cpo' ) ?>
                                </h3>
								<div class="uni-form-row__with-checkbox uni-clear">
									<label class="uni-regular-setting__checkbox" for="uni-convert-width-checkbox">
	                                    <input
	                                            id="uni-convert-width-checkbox"
	                                            class="builderius-setting-field builderius-single-checkbox"
	                                            type="checkbox"
	                                            name="convert_width"
	                                            value="on"
	                                            {{ if (data.convert_width === 'on') { print(' checked'); } }} />
	                                    <span class="uni-regular-setting__label-wrap">
		                                    <span class="uni-regular-setting__checkbox-label"></span>
		                                    <span class="uni-regular-setting__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
		                                    <span class="uni-regular-setting__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
		                                </span>
	                                </label>
									<h3><?php esc_html_e( 'Convert width automatically?', 'uni-cpo' ) ?></h3>
								</div>
                                <p>
			                        <?php esc_html_e( 'Select the option which value will be used as product width.', 'uni-cpo' ) ?>
                                </p>
                                <select class="builderius-setting-field uni-modal-select" name="d_width_option">
                                    <option value=""><?php esc_html_e( '- Not selected -', 'uni-cpo' ) ?></option>
                                    {{ _.each(vars.regular, function(value){ }}
                                    <option value="{{= value }}"{{ if (data.d_width_option === value) { print(' selected'); } }}>{{= '{'+value+'}' }}</option>
                                    {{ }); }}
                                    {{ _.each(vars.nov, function(value){ }}
                                    <option value="{{= value }}"{{ if (data.d_width_option === value) { print(' selected'); } }}>{{= '{'+value+'}' }}</option>
                                    {{ }); }}
                                </select>
                            </div>
                            <div class="uni-form-row">
                                <h3>
			                        <?php esc_html_e( 'Height', 'uni-cpo' ) ?>
                                </h3>
								<div class="uni-form-row__with-checkbox uni-clear">
									<label class="uni-regular-setting__checkbox" for="uni-convert-height-checkbox">
	                                    <input
	                                            id="uni-convert-height-checkbox"
	                                            class="builderius-setting-field builderius-single-checkbox"
	                                            type="checkbox"
	                                            name="convert_height"
	                                            value="on"
	                                            {{ if (data.convert_height === 'on') { print(' checked'); } }} />
	                                    <span class="uni-regular-setting__label-wrap">
		                                    <span class="uni-regular-setting__checkbox-label"></span>
		                                    <span class="uni-regular-setting__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
		                                    <span class="uni-regular-setting__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
		                                </span>
	                                </label>
									<h3><?php esc_html_e( 'Convert height automatically?', 'uni-cpo' ) ?></h3>
								</div>
                                <p>
			                        <?php esc_html_e( 'Select the option which value will be used as product height.', 'uni-cpo' ) ?>
                                </p>
                                <select class="builderius-setting-field uni-modal-select" name="d_height_option">
                                    <option value=""><?php esc_html_e( '- Not selected -', 'uni-cpo' ) ?></option>
                                    {{ _.each(vars.regular, function(value){ }}
                                    <option value="{{= value }}"{{ if (data.d_height_option === value) { print(' selected'); } }}>{{= '{'+value+'}' }}</option>
                                    {{ }); }}
                                    {{ _.each(vars.nov, function(value){ }}
                                    <option value="{{= value }}"{{ if (data.d_height_option === value) { print(' selected'); } }}>{{= '{'+value+'}' }}</option>
                                    {{ }); }}
                                </select>
                            </div>
                        </div>
                        <div class="uni-modal-btns-wrap uni-clear">
                        <span id="js-modal-main-cancel-btn"
                              class="uni-btn-2 uni-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'uni-cpo' ) ?></span>
                            <span id="js-modal-main-save-btn"
                                  class="uni-btn-1 uni-modal-save-btn"><?php esc_html_e( 'Submit', 'uni-cpo' ) ?></span>
                        </div>

                    </div>
                </div>
            </script>
			<?php
		}
		if ( ! UniCpo()->is_pro() ) {
			?>
            <script id="js-builderius-modal-dimensions-tmpl" type="text/template">
                <div id="uni-modal-dimensions-wrapper" class="uni-modal-wrapper">
                </div>
            </script>
			<?php
		}
	}

	/**
	 * A template for the non option variables modal window
	 *
	 * @since 4.0.0
	 * @return string
	 */
	static public function modal_nov() {
		?>
        <script id="js-builderius-modal-nov-tmpl" type="text/template">
            <div id="uni-modal-nov-wrapper" class="uni-modal-wrapper">
                <div id="uni-modal-wrap" class="uni-modal-wrap">

                    <div class="uni-modal-head uni-modal-cpo-head">
                        <span><?php esc_html_e( 'Non Option Variables', 'uni-cpo' ) ?></span>
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
                                        {{ if (data.nov_enable === 'on') { print(' checked'); } }} />
                                <span class="uni-main-feature__label-wrap">
                                    <span class="uni-main-feature__checkbox-label"></span>
                                    <span class="uni-main-feature__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                    <span class="uni-main-feature__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                </span>
                            </label>
                            <h3><?php esc_html_e( 'Non Option Variables', 'uni-cpo' ) ?></h3>
                            <p><?php esc_html_e( 'NOVs are variables without direct connection to any options.', 'uni-cpo' ) ?></p>
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
                                        <span class="uni-conditional-logic__checkbox-on"><?php esc_html_e( 'on', 'uni-cpo' ) ?></span>
                                        <span class="uni-conditional-logic__checkbox-off"><?php esc_html_e( 'off', 'uni-cpo' ) ?></span>
                                    </span>
                            </label>
                            <h3><?php esc_html_e( 'Wholesale', 'uni-cpo' ) ?></h3>
                            <p><?php esc_html_e( 'Enabling this functionality will make it possible to set different value/formula on per user role basis', 'uni-cpo' ) ?></p>
                        </div>
                        <div class="uni-clear"></div>
                        <div class="uni-form-row uni-variables-list-row">
                            <h3><?php esc_html_e( 'Available variables:', 'uni-cpo' ) ?></h3>
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
                        <?php /*
						<div class="uni-form-row">
					        <div class="uni-modal-row-first">
				            	<label for="cpo_matrix_data[round]">Round</label>
				        	</div>
						    <div class="uni-modal-row-second uni-setting-fields-wrap-2">
				            	<div class="uni-setting-radio-inputs">
				                	<div class="uni-setting-radio-inputs uni-clear">
				            	    	<div class="uni-setting-radio-item">
				                    		<input id="uni-nov-round" class="builderius-setting-field" name="nov_round" value="round" {{ if (data.nov_round === 'round') { print(' checked'); } }} type="radio">
				                    		<label for="uni-nov-round">Closest</label>
				                		</div>
					                    <div class="uni-setting-radio-item">
											<input id="uni-nov-floor" class="builderius-setting-field" name="nov_round" value="floor" {{ if (data.nov_round === 'floor') { print(' checked'); } }} type="radio">
				                    		<label for="uni-nov-floor">Floor</label>
						                </div>
				                        <div class="uni-setting-radio-item">
											<input id="uni-nov-ceil" class="builderius-setting-field" name="nov_round" value="ceil" {{ if (data.nov_round === 'ceil') { print(' checked'); } }} type="radio">
				                    		<label for="uni-nov-ceil">Ceil</label>
						                </div>
		                            </div>
		                        </div>
		                    </div>
						</div>
                        */ ?>
                        <div class="uni-form-row">
                            <h3><?php esc_html_e( 'Controls', 'uni-cpo' ) ?>:</h3>
                            <div class="uni-cpo-non-option-vars-options-repeat">
                                <div class="uni-cpo-non-option-vars-options-repeat-wrapper">

                                    <div class="uni-formula-conditional-rules-btn-wrap uni-clear">
                                        <span class="uni_cpo_non_option_vars_option_add"><?php esc_html_e( 'Add Rule', 'uni-cpo' ) ?></span>
                                        <span class="uni-rules-remove-all"><?php esc_html_e( 'Remove All', 'uni-cpo' ) ?></span>
                                    </div>

                                    <div class="uni-cpo-non-option-vars-options-wrapper">

                                        <div class="uni-cpo-non-option-vars-options-template uni-cpo-non-option-vars-options-row">
                                            <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_move"><i
                                                            class="fas fa-arrows-alt"></i></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-clear">
                                                    <span><code>{uni_nov_cpo_</code></span>
                                                    <input
                                                            type="text"
                                                            name="nov[<%row-count%>][slug]"
                                                            value=""
                                                            class="uni-cpo-modal-field uni-cpo-non-option-slug-field uni-cpo-input-for-nov"
                                                            data-parsley-required="true"
                                                            data-parsley-trigger="change focusout submit"
                                                            data-parsley-notequalto=".uni-cpo-non-option-slug-field"/>
                                                    <span><code>}</code></span>
                                                </div>
                                                <?php
                                                if ( UniCpo()->is_pro() ) {
	                                                ?>
                                                    <label for="uni-row[<%row-count%>]-matrix"
                                                           class="uni-matrix-checkbox">
		                                                <?php esc_html_e( 'Matrix?', 'uni-cpo' ) ?>
                                                        <input
                                                                id="uni-row[<%row-count%>]-matrix"
                                                                class="builderius-single-checkbox"
                                                                type="checkbox"
                                                                name="nov[<%row-count%>][matrix][enable]"
                                                                data-uni-constrainer="yes"
                                                                value="on"/>
                                                        <span></span>
                                                    </label>

                                                    <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-cpo-convert-wrapper uni-clear">
                                                        <label for="uni-row[<%row-count%>]-convert"
                                                               class="uni-convert-checkbox">
		                                                    <?php esc_html_e( 'Convert unit?', 'uni-cpo' ) ?>
                                                            <input
                                                                    id="uni-row[<%row-count%>]-convert"
                                                                    class="builderius-single-checkbox"
                                                                    type="checkbox"
                                                                    name="nov[<%row-count%>][convert][enable]"
                                                                    data-uni-constrainer="yes"
                                                                    value="on"/>
                                                            <span></span>
                                                        </label>
                                                        <label for="uni-row[<%row-count%>]-convert-to">
	                                                        <?php esc_html_e( 'to', 'uni-cpo' ) ?>
                                                        </label>
                                                        <select
                                                                id="uni-row[<%row-count%>]-convert-to"
                                                                name="nov[<%row-count%>][convert][to]">
                                                            <option value=""><?php esc_html_e( 'WC default unit', 'uni-cpo' ) ?></option>
                                                            <option value="mm"><?php esc_html_e( 'mm', 'uni-cpo' ) ?></option>
                                                            <option value="cm"><?php esc_html_e( 'cm', 'uni-cpo' ) ?></option>
                                                            <option value="m"><?php esc_html_e( 'm', 'uni-cpo' ) ?></option>
                                                            <option value="in"><?php esc_html_e( 'in', 'uni-cpo' ) ?></option>
                                                            <option value="ft"><?php esc_html_e( 'ft', 'uni-cpo' ) ?></option>
                                                            <option value="yd"><?php esc_html_e( 'yd', 'uni-cpo' ) ?></option>
                                                        </select>
                                                    </div>

	                                                <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-clear">
		                                                <label for="uni-row[<%row-count%>]-cart-display"
		                                                       class="uni-cart-display-checkbox">
			                                                <?php esc_html_e( 'Display in cart?', 'uni-cpo' ) ?>
			                                                <input
					                                                id="uni-row[<%row-count%>]-cart-display"
					                                                class="builderius-single-checkbox"
					                                                type="checkbox"
					                                                name="nov[<%row-count%>][cart_display][enable]"
					                                                value="on"/>
			                                                <span></span>
		                                                </label>
                                                    </div>
                                                    <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-cpo-cart-display-wrapper uni-clear">
		                                                <label for="uni-row[<%row-count%>]-cart-display-to">
			                                                <?php esc_html_e( 'Name', 'uni-cpo' ) ?>
		                                                </label>
		                                                <input
				                                                id="uni-row[<%row-count%>]-cart-display-name"
				                                                class="uni-cpo-input-for-nov"
				                                                type="text"
				                                                name="nov[<%row-count%>][cart_display][name]"
				                                                value=""/>
                                                    </div>
	                                                <?php
                                                }
                                                ?>
                                                <div
                                                        class="uni-cpo-not-matrix-options-wrap"
                                                        <?php if ( UniCpo()->is_pro() ) { ?>data-uni-constrained="input[name=nov\[<%row-count%>\]\[matrix\]\[enable\]]"
                                                        data-uni-constvalue="off"<?php } ?>>
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
                                                if ( UniCpo()->is_pro() ) {
	                                                ?>
                                                    <div
                                                            class="uni-cpo-matrix-options-wrap"
                                                            data-uni-constrained="input[name=nov\[<%row-count%>\]\[matrix\]\[enable\]]"
                                                            data-uni-constvalue="on">
                                                        <div class="uni-cpo-matrix-options-row uni-clear">
                                                            <label for="uni-row-<%row-count%>-matrix-col-var">1st
                                                                var</label>
                                                            <select
                                                                    id="uni-row-<%row-count%>-matrix-col-var"
                                                                    name="nov[<%row-count%>][matrix][x_var]">
                                                                {{ _.each(vars, function(arr, group){ }}
                                                                {{ if (arr && ('builtin' !== group)) { }}
                                                                {{ _.each(arr, function(value){ }}
                                                                <option>{{- '{'+value+'}' }}</option>
                                                                {{ }); }}
                                                                {{ } }}
                                                                {{ }); }}
                                                            </select>
                                                        </div>
                                                        <div class="uni-cpo-matrix-options-row uni-clear">
                                                            <label for="uni-row-<%row-count%>-matrix-in-col"># in
                                                                cols</label>
                                                            <input
                                                                    class="uni-matrix-data"
                                                                    id="uni-row-<%row-count%>-matrix-in-col"
                                                                    type="text"
                                                                    name="nov[<%row-count%>][matrix][x_axis]"
                                                                    value="">
                                                        </div>
                                                        <div class="uni-cpo-matrix-options-row uni-clear">
                                                            <label for="uni-row-<%row-count%>-matrix-row-var">2nd
                                                                var</label>
                                                            <select
                                                                    id="uni-row-<%row-count%>-matrix-row-var"
                                                                    name="nov[<%row-count%>][matrix][y_var]">
                                                                {{ _.each(vars, function(arr, group){ }}
                                                                {{ if (arr && ('builtin' !== group)) { }}
                                                                {{ _.each(arr, function(value){ }}
                                                                <option>{{- '{'+value+'}' }}</option>
                                                                {{ }); }}
                                                                {{ } }}
                                                                {{ }); }}
                                                            </select>
                                                        </div>
                                                        <div class="uni-matrix-generate-btn uni-row-<%row-count%>-matrix-generate">
			                                                <?php esc_html_e( 'Generate', 'uni-cpo' ) ?>
                                                        </div>
                                                        <div class="uni-matrix-import uni-row-<%row-count%>-matrix-import">
                                                            <input
                                                                    id="uni-row-<%row-count%>-matrix-import-input"
                                                                    name="import"
                                                                    type="file"/>
                                                            <label
                                                                    for="uni-row-<%row-count%>-matrix-import-input">
                                                                <span></span>
				                                                <?php esc_html_e( 'Choose a file', 'uni-cpo' ) ?>
                                                            </label>
                                                            <button
                                                                    type="button"
                                                                    class="uni-matrix-import-btn uni-row-<%row-count%>-matrix-import-btn">
				                                                <?php esc_html_e( 'Import', 'uni-cpo' ) ?>
                                                            </button>
                                                        </div>
                                                        <div class="uni-matrix-table-wrapper">
                                                            <div
                                                                    id="uni-row-<%row-count%>-matrix-table-container"
                                                                    data-row="<%row-count%>"
                                                                    class="uni-matrix-table-container"></div>
                                                        </div>
                                                        <input
                                                                class="uni-matrix-json"
                                                                type="hidden"
                                                                name="nov[<%row-count%>][matrix][data]"
                                                                value="">
                                                    </div>
	                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_non_option_vars_option_remove">
                                                        <i class="fas fa-times"></i>
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
	                                    {{ inCartDisplay = uniGet(obj, 'cart_display.enable', 'off'); }}
                                        {{ convertTo = uniGet(obj, 'convert.to', ''); }}
	                                    {{ cartName = uniGet(obj, 'cart_display.name', ''); }}
                                        <div class="uni-cpo-non-option-vars-options-row">
                                            <div class="uni-cpo-non-option-vars-options-move-wrapper">
                                                <span class="uni_cpo_non_option_vars_option_move"><i
                                                            class="fas fa-arrows-alt"></i></span>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-content-wrapper uni-clear">
                                                <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-clear">
                                                    <span><code>{uni_nov_cpo_</code></span>
                                                    <input
                                                            type="text"
                                                            name="nov[{{- i }}][slug]"
                                                            value="{{- obj.slug }}"
                                                            class="uni-cpo-modal-field uni-cpo-non-option-slug-field builderius-setting-field uni-cpo-input-for-nov"
                                                            data-parsley-required="true"
                                                            data-parsley-trigger="change focusout submit"
                                                            data-parsley-notequalto=".uni-cpo-non-option-slug-field"/>
                                                    <span><code>}</code></span>
                                                </div>
                                                <?php
                                                if ( UniCpo()->is_pro() ) {
	                                                ?>
                                                    <label for="uni-row[{{- i }}]-matrix" class="uni-matrix-checkbox">
		                                                <?php esc_html_e( 'Matrix?', 'uni-cpo' ) ?>
                                                        <input
                                                                id="uni-row[{{- i }}]-matrix"
                                                                class="builderius-setting-field builderius-single-checkbox"
                                                                type="checkbox"
                                                                name="nov[{{- i }}][matrix][enable]"
                                                                data-uni-constrainer="yes"
                                                                value="on"
                                                                {{ if (obj.matrix.enable === 'on') { print(' checked'); } }} />
                                                        <span></span>
                                                    </label>

                                                    <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-cpo-convert-wrapper uni-clear">
                                                        <label for="uni-row[{{- i }}]-convert"
                                                               class="uni-convert-checkbox">
			                                                <?php esc_html_e( 'Convert unit?', 'uni-cpo' ) ?>
                                                            <input
                                                                    id="uni-row[{{- i }}]-convert"
                                                                    class="builderius-setting-field builderius-single-checkbox"
                                                                    type="checkbox"
                                                                    name="nov[{{- i }}][convert][enable]"
                                                                    data-uni-constrainer="yes"
                                                                    value="on"
                                                                    {{ if (convertEnable === 'on') { print(' checked'); } }}/>
                                                            <span></span>
                                                        </label>
                                                        <label for="uni-row[{{- i }}]-convert-to">
			                                                <?php esc_html_e( 'to', 'uni-cpo' ) ?>
                                                        </label>
                                                        <select
                                                                id="uni-row[{{- i }}]-convert-to"
                                                                class="builderius-setting-field"
                                                                name="nov[{{- i }}][convert][to]">
                                                            <option value=""{{ if (!convertTo) { print(' selected'); } }}><?php esc_html_e( 'WC default unit', 'uni-cpo' ) ?></option>
                                                            <option value="mm"{{ if (convertTo === 'mm') { print(' selected'); } }}><?php esc_html_e( 'mm', 'uni-cpo' ) ?></option>
                                                            <option value="cm"{{ if (convertTo === 'cm') { print(' selected'); } }}><?php esc_html_e( 'cm', 'uni-cpo' ) ?></option>
                                                            <option value="m"{{ if (convertTo === 'm') { print(' selected'); } }}><?php esc_html_e( 'm', 'uni-cpo' ) ?></option>
                                                            <option value="in"{{ if (convertTo === 'in') { print(' selected'); } }}><?php esc_html_e( 'in', 'uni-cpo' ) ?></option>
                                                            <option value="ft"{{ if (convertTo === 'ft') { print(' selected'); } }}><?php esc_html_e( 'ft', 'uni-cpo' ) ?></option>
                                                            <option value="yd"{{ if (convertTo === 'yd') { print(' selected'); } }}><?php esc_html_e( 'yd', 'uni-cpo' ) ?></option>
                                                        </select>
                                                    </div>

	                                                <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-clear">
		                                                <label for="uni-row[{{- i }}]-cart-display"
		                                                       class="uni-cart-display-checkbox">
			                                                <?php esc_html_e( 'Display in cart?', 'uni-cpo' ) ?>
			                                                <input
					                                                id="uni-row[{{- i }}]-cart-display"
					                                                class="builderius-setting-field builderius-single-checkbox"
					                                                type="checkbox"
					                                                name="nov[{{- i }}][cart_display][enable]"
					                                                value="on"
					                                                {{ if (inCartDisplay === 'on') { print(' checked'); } }}/>
			                                                <span></span>
		                                                </label>
                                                    </div>
                                                    <div class="uni-cpo-non-option-vars-options-content-field-wrapper uni-cpo-cart-display-wrapper uni-clear">
		                                                <label for="uni-row[{{- i }}]-cart-display-to">
			                                                <?php esc_html_e( 'Name', 'uni-cpo' ) ?>
		                                                </label>
		                                                    <input
				                                                id="uni-row[{{- i }}]-cart-display-name"
				                                                class="builderius-setting-field uni-cpo-input-for-nov"
				                                                type="text"
				                                                name="nov[{{- i }}][cart_display][name]"
				                                                value="{{- cartName}}"/>
                                                    </div>
	                                                <?php
                                                }
                                                ?>
                                                <div
                                                        class="uni-cpo-not-matrix-options-wrap"
                                                        <?php if ( UniCpo()->is_pro() ) { ?>data-uni-constrained="input[name=nov\[{{- i }}\]\[matrix\]\[enable\]]"
                                                        data-uni-constvalue="off"<?php } ?>>
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
                                                if ( UniCpo()->is_pro() ) {
	                                                ?>
                                                    <div
                                                            class="uni-cpo-matrix-options-wrap"
                                                            data-uni-constrained="input[name=nov\[{{- i }}\]\[matrix\]\[enable\]]"
                                                            data-uni-constvalue="on">
                                                        <div class="uni-cpo-matrix-options-row uni-clear">
                                                            <label for="uni-row-{{- i }}-matrix-col-var">1st var</label>
                                                            <select
                                                                    id="uni-row-{{- i }}-matrix-col-var"
                                                                    class="builderius-setting-field"
                                                                    name="nov[{{- i }}][matrix][x_var]">
                                                                {{ _.each(vars, function(arr, group){ }}
                                                                {{ if (arr && ('builtin' !== group)) { }}
                                                                {{ _.each(arr, function(value){ }}
                                                                {{ const niceVarName = '{'+value+'}'; }}
                                                                {{ let xVar; }}
                                                                {{ if (typeof obj.matrix.x_var !== 'undefined') { }}
                                                                {{ xVar = obj.matrix.x_var; }}
                                                                {{ } }}
                                                                <option
                                                                        {{ if (xVar=== niceVarName) { }}
                                                                        {{ print(' selected'); }}
                                                                        {{ } }}>{{- niceVarName }}</option>
                                                                {{ }); }}
                                                                {{ } }}
                                                                {{ }); }}
                                                            </select>
                                                        </div>
                                                        <div class="uni-cpo-matrix-options-row uni-clear">
                                                            <label for="uni-row-{{- i }}-matrix-in-col"># in
                                                                cols</label>
                                                            {{ let xAxis; }}
                                                            {{ if (typeof obj.matrix.x_axis !== 'undefined') { }}
                                                            {{ xAxis = obj.matrix.x_axis; }}
                                                            {{ } }}
                                                            <input
                                                                    class="uni-matrix-data builderius-setting-field"
                                                                    id="uni-row-{{- i }}-matrix-in-col"
                                                                    type="text"
                                                                    name="nov[{{- i }}][matrix][x_axis]"
                                                                    value="{{- xAxis }}">
                                                        </div>
                                                        <div class="uni-cpo-matrix-options-row uni-clear">
                                                            <label for="uni-row-{{- i }}-matrix-row-var">2nd var</label>
                                                            <select
                                                                    id="uni-row-{{- i }}-matrix-row-var"
                                                                    class="builderius-setting-field"
                                                                    name="nov[{{- i }}][matrix][y_var]">
                                                                {{ _.each(vars, function(arr, group){ }}
                                                                {{ if (arr && ('builtin' !== group)) { }}
                                                                {{ _.each(arr, function(value){ }}
                                                                {{ const niceVarName = '{'+value+'}'; }}
                                                                {{ let yVar; }}
                                                                {{ if (typeof obj.matrix.y_var !== 'undefined') { }}
                                                                {{ yVar = obj.matrix.y_var; }}
                                                                {{ } }}
                                                                <option
                                                                        {{ if (yVar=== niceVarName) { }}
                                                                        {{ print(' selected'); }}
                                                                        {{ } }}>{{- niceVarName }}</option>
                                                                {{ }); }}
                                                                {{ } }}
                                                                {{ }); }}
                                                            </select>
                                                        </div>
                                                        <div class="uni-matrix-generate-btn uni-row-{{- i }}-matrix-generate">
			                                                <?php esc_html_e( 'Generate', 'uni-cpo' ) ?>
                                                        </div>
                                                        <div class="uni-matrix-import uni-row-{{- i }}-matrix-import">
                                                            <input
                                                                    id="uni-row-{{- i }}-matrix-import-input"
                                                                    name="import"
                                                                    type="file"/>
                                                            <label
                                                                    for="uni-row-{{- i }}-matrix-import-input">
                                                                <span></span>
				                                                <?php esc_html_e( 'Choose a file', 'uni-cpo' ) ?>
                                                            </label>
                                                            <button
                                                                    type="button"
                                                                    class="uni-matrix-import-btn uni-row-{{- i }}-matrix-import-btn">
				                                                <?php esc_html_e( 'Import', 'uni-cpo' ) ?>
                                                            </button>
                                                        </div>
                                                        <div class="uni-matrix-table-wrapper">
                                                            <div
                                                                    id="uni-row-{{- i }}-matrix-table-container"
                                                                    data-row="{{- i }}"
                                                                    class="uni-matrix-table-container"></div>
                                                        </div>
                                                        {{ let matrixData; }}
                                                        {{ if (typeof obj.matrix.data !== 'undefined') { }}
                                                        {{ matrixData = obj.matrix.data; }}
                                                        {{ } }}
                                                        <input
                                                                class="uni-matrix-json builderius-setting-field"
                                                                type="hidden"
                                                                name="nov[{{- i }}][matrix][data]"
                                                                value="{{- matrixData }}">
                                                    </div>
	                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="uni-cpo-non-option-vars-options-rules-remove-wrapper">
                                                    <span class="uni_cpo_non_option_vars_option_remove">
                                                        <i class="fas fa-times"></i>
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
	static public function modal_tab_list() {
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
	static public function modal_tab_open() {
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
	static public function modal_tab_close() {
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
	static public function modal_group_open() {
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
	static public function modal_group_close() {
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
	static public function row_overlay() {
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
	static public function column_overlay() {
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
	static public function module_overlay() {
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
