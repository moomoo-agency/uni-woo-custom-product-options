=== WooCommerce Product Options and Price Calculation Formulas - Uni CPO ===
Contributors: moomooagency, mrpsiho, andriimatenka, freemius
Tags: custom options, extra options, product visual builder, woocommerce plugins, price calculation, maths formula, conditional logic, wholesale
Requires at least: 4.8
Tested up to: 4.9.5
Requires PHP: 7.0
WC requires at least: 3.2.0
WC tested up to: 3.3.5
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provides an opportunity to add extra product options with the possibility to calculate the price based on the chosen options and using custom maths formula!

== Description ==

=== Overview ===

**Uni CPO - WooCommerce Options and Price Calculation Formulas** is a fully featured plugin that creates an opportunity to add custom options for a WooCommerce products as well as enables custom price calculation based on any maths formula.

It supports products type 'simple' only! But why you ever need any variable product after this plugin, right? :)

https://www.youtube.com/watch?v=dX7-T4gVJ_I

A fully featured visual form builder is used to add custom options. Would like to place the options in two/three/more columns? Easy! Would like to set custom color, margins, add custom text and so on? Yes, it's possible too!!)

Add custom fields to your products, display them conditionally, give a possibility for your customers to customize products, to personalize them by adding highly dynamic info like dimensions, custom labels, comments. Moreover, create a unique scheme for price calculation based on custom options added!

=== Main features ===

* Visual form builder - design the look of your form in easy and smooth way!
* Custom product option types - 10+ different types!
* A possibility to use non option variables (NOV) - synthetic variables which can hold both a specific value or a
maths formula as its value
* A possibility to use wholesale-like functionality for your NOVs - different values for different user roles!
* A possibility to use virtually any maths formula for the price calculation of your product
* A possibility to add formulas conditional logic - apply different formulas under different circumstances!
* A possibility to create fields conditional logic - display/hide certain custom options based on the values of
other custom options and/or NOVs

[The full list of plugin's features](https://moomoo-agency.gitbooks.io/uni-cpo-4-documentation/content/why-uni-cpo.html)
[Documentation](https://moomoo-agency.gitbooks.io/uni-cpo-4-documentation/)

[DEMO (PRO version)](https://cpo.builderius.io)
You can try PRO version. Just use these credentials:
username: `demo`
password: `demo`
[login URL](https://cpo.builderius.io/wp-login.php)

**Pro version of the plugin is [available here](https://builderius.io/cpo).**

== Installation ==

=== Minimum Requirements ===

* WooCommerce 3.2+
* WordPress 4.8 or greater
* PHP version 7.0 or greater

=== Installation ===

1. Upload the plugin files to the `/wp-content/plugins/uni-woo-custom-product-options` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the `Plugins` screen in WordPress
1. Use the WooCommerce->Uni CPO Settings screen to configure the plugin


== Frequently Asked Questions ==

** Q: Do I need to back up before update? **
A: Yes, always and ever! Back up your files as well as database. Always test new versions of the plugin on test/stage server first!

** Q: I'm using Uni CPO 3.1.8 or below purchased on codecanyon.net. Do I need to update immediately? **
A: We wouldn't recommend to do this yet. First, this version is so called 'lite' version and will always have less features than future 'pro' version. Also, **version 4 is incompatible with the previous versions of the plugin**. The migration script isn't ready yet.

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png
4. screenshot-4.png
5. screenshot-5.png
6. screenshot-6.png
7. screenshot-7.png

== Changelog ==

= 4.1.0 =
* Added: Matrix Option
* Added: Colorify functionality
* Added: a possibility to add input for range slider in single mode
* Added: starting price, price prefix, price postfix and price template for archives settings
* Added: support for RTL languages
* Fixed: a possibility to add files for order meta if this meta is for File Upload Option
* Fixed: displaying added item in WC cart widget
* Fixed: some other minor bugs

= 4.0.11 =
* Hot-fix price displaying issue on other products

= 4.0.10 =
* Fixed adding custom image (if selected) upon cart item duplication
* Fixed updating cart item after 'full edit' instead of creating a new cart item
* Fixed disabling 'add to cart' btn by using a special word 'disable' instead of formula

= 4.0.9 =
* Fixed a JS bug related to price calculation data that is returned from backend

= 4.0.8 =
* Fixed a bug when adding product to cart
* Added support for WC 3.3+

= 4.0.7 =
* Added 'min date' and 'max date' settings for Date picker option
* Added 'custom values' setting for Range Slider option
* Added 'font' setting for Select option
* Added cart item edit 'full' mode (PRO)
* Added 'other variables' are now can be used in Dynamic Notice
* Improved File upload: a file will be uploaded automatically after adding
* Improved: WC price is now hidden on init
* Improved: option's values are now preserved after page reload during adding to cart
* Changed: JS event names are prefixed with 'uni_'
* Fixed saving special tags in Dynamic Notice option
* Fixed saving 'img' tag in tooltips
* Fixed displaying/hiding 'order disabled' custom message
* Fixed fields conditional logic when using NOVs

= 4.0.6 =
* Fixed validation for Text Input in 'decimal' mode
* Fixed a bug when it was not possible to set 'one letter' slug for suboptions

= 4.0.5 =
* Added Dynamic Notice option
* Added Range Slider option
* Added Dimensions Conditional Logic
* Added 'convert to unit' functionality for NOVs
* Added order item edit functionality
* Improved support for various WP themes, fixed some CSS related issues
* Improved cart item inline edit functionality - added support for datepicker
* Fixed an issue when alt image for suboptions was not actually optional
* Fixed an issue when two clicks were needed to select radio input in 'image' mode

= 4.0.4 =
* Fixed an issue related to the order of cart/order meta
* Fixed displaying 'Select options' instead of 'Add to cart' on product archives
* Fixed an issue with using a wrong protocol during enqueueing some dynamically generated content on sites with SSL enabled
* Improved styles for several option types
* Added a scroll bar for the options list in the builder panel

= 4.0.3 =
* Enhaced and improved

= 4.0.2 =
* Fixed a bug "Inconsistency of view in builder and in the frontend"

= 4.0.1 =
* Fixed a bug with displaying prices which are higher than one thousand

= 4.0 =
* The release of the plugin
