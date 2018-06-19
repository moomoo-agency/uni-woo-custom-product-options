=== WooCommerce Product Options and Price Calculation Formulas - Uni CPO ===
Contributors: moomooagency, mrpsiho, andriimatenka, freemius
Tags: custom options, extra options, product visual builder, woocommerce plugins, price calculation, maths formula, conditional logic, wholesale
Requires at least: 4.8
Tested up to: 4.9.6
Requires PHP: 7.0
WC requires at least: 3.2.0
WC tested up to: 3.4.2
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provides an opportunity to add extra product options with the possibility to calculate the price based on the chosen options and using custom maths formula!

== Description ==

=== Overview ===

**Uni CPO - WooCommerce Options and Price Calculation Formulas** is a fully featured plugin that creates an opportunity to add custom options for a WooCommerce products as well as enables custom price calculation based on any maths formula.

It supports ONLY these product types: 'simple' and 'subscription' (experimental)! But why you ever need any variable product after this plugin, right? :)

A fully featured visual form builder is used to add custom options. Would you like to place the options in two/three/more columns? Easy! Would you like to set custom color, margins, add custom text and so on? Yes, it's possible too!!)

[Check out this unique Colorify functionality (PRO) ](https://www.youtube.com/watch?v=-6_CBPuD1w4)

Add extra options to your products, display them conditionally, give a possibility for your customers to customize products, to personalize them by adding highly dynamic info like dimensions, custom labels, comments. Moreover, create a unique scheme for price calculation based on custom options added!

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
* A possibility to use custom price tables (via Non Option Variables functionality), set product price based on one or two custom options!
* Integrate with ShipperHQ or Boxtal and let them use the calculated weight of the ordered item and request the real shipping rates!
...and many many more! ;)

=== Full List of Features and Docs ===

* [The full list of plugin's features](https://moomoo-agency.gitbooks.io/uni-cpo-4-documentation/content/why-uni-cpo.html)
* [The plugin's Documentation](https://moomoo-agency.gitbooks.io/uni-cpo-4-documentation/)

=== Demo - Try By Yourself! ===

[DEMO site with PRO version installed (unlocked all the features)](https://cpo.builderius.io)
Use the following credentials to log in and try by yourself:
* username: `demo`
* password: `demo`
[login URL](https://cpo.builderius.io/wp-login.php)

**Pro version of the plugin is [available here](https://builderius.io/cpo)**

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

= 4.1.6 =
* Added: a possibility to use regular variables and NOVs as cart discounts values
* Fixed: a bug in formula conditional logic

= 4.1.5 =
* Added: a possibility to use Dropbox as the file storage for the files uploaded via File Upload Options
* Updated: Freemius SDK

= 4.1.4 =
* Added: timepicker mode for Datepicker Option
* Added: 'multiple dates' mode for Datepicker Option
* Added: 'subscription' product type unlocked for using in the plugin (experimental)
* Updated: jQuery QueryBuilder script to 2.5.2
* Fixed: several small style issues

= 4.1.3 =
* Added: support for "Popup Maker – Popup Forms, Optins & More" plugin; use popups instead of tooltips
* Improved: some small enhancements, including adding notifications in case of common errors and issues
* Fixed: a bug when 'remove' icon is disappeared in NOV matrices

= 4.1.2 =
* Improvement: small fixes and improvements
* Added: support for the plugin add-ons

= 4.1.1 =
* Hot-fix for price calculation in the cart; there was a bug related to options with suboptions
* Fixed: using NOVs in validation conditional logic
* Added: support for Avada theme for changing image upon selection in option functionality
* Added: a possibility to set NOV value as value for validation attribute

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
