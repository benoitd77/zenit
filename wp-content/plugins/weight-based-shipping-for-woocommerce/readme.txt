=== WooCommerce Weight Based Shipping ===
Contributors: dangoodman
Tags: ecommerce, woocommerce, shipping, woocommerce shipping, weight-based shipping, conditional free shipping,
conditional flat rate, table rate shipping, weight, subtotal, country, shipping classes
Requires at least: 4.0
Tested up to: 4.7
WC requires at least: 2.3
WC tested up to: 3.0
Stable tag: trunk


Simple yet flexible weight-based shipping for WooCommerce

== Description ==

Weight Based Shipping is a simple yet flexible shipping method for WooCommerce focused mainly on order weight (but not limited to) to calculate shipping cost. Plugin allows you to add multiple rules based on various conditions.

<br>

= Features =

<p></p>
<ul>
    <li>
        <b>Order weight, subtotal and destination</b><br>
        Create as many shipping rules as you need for different order destinations, weight and subtotal ranges.<br><br>
    </li>
    <li>
        <b>Flexible Price Calculation</b><br>
        Each rule can be configured to expose a constant price (like Flat Rate) or a progressive price based on cart weight, or both.<br><br>
    </li>
    <li>
        <b>Conditional Free Shipping</b><br>
        In some cases you want to ship for free depending on subtotal, total weight or some other condition. That can be achieved in a moment with the plugin.<br><br>
    </li>
    <li>
        <b>Shipping Classes Support</b><br>
        For each shipping class you have you can override the way shipping price is calculated for it.<br><br>
    </li>
</ul>

See <a href="https://wordpress.org/plugins/weight-based-shipping-for-woocommerce/screenshots/">screenshots</a> for the list of all supported options.
<br><br>

<blockquote>
    Also, check out our <a href="https://tablerateshipping.com">advanced table rate shipping plugin for WooCommerce</a>.<br>
    <br>
</blockquote>


== Changelog ==

= 5.0.8 =
* Fix IE11 error preventing from adding/importing rules

= 5.0.7 =
* Fix welcome screen buttons appearance in WP 4.7.5

= 5.0.6 =
* A bunch of minor fixes

= 5.0.5 =
* Fix PHP 5.3.x error while importing legacy rules
* Fix WooCommerce 3.x deprecation notice about get_variation_id

= 5.0.4 =
* Fix WooCommerce 3.x deprecation notices
* Deactivate other active versions of the plugin upon activation (fixed)

= 5.0.3-beta =
* Fix 'fatal error: call to undefined function Wbs\wc_get_shipping_method_count()'

= 5.0.2-beta =
* Avoid conflicts with other plugins using same libraries
* Deactivate other active versions of the plugin upon activation

= 5.0.1-beta =
* Fix Destinations not being saved on WooCommerce 3.0

= 5.0.0-beta =
* Rewritten from scratch, better performance and look'n'feel
* Shipping Zones support

= 4.2.3 =
* Fix links to premium plugins

= 4.2.2 =
* Fix rules not imported from an older version when updating from pre-4.0 to 4.2.0 or 4.2.1

= 4.2.1 =
* Fix saving rules order

= 4.2.0 =
* Allow sorting rules by drag'n'drop in admin panel

= 4.1.4 =
* WooCommerce 2.6 compatibility fixes

= 4.1.3 =
* Minimize chances of a float-point rounding error in the weight step count calculation (https://wordpress.org/support/topic/weight-rate-charge-skip-calculate)

= 4.1.2 =
* Don't fail on invalid settings, allow editing them instead

= 4.1.1 =
* Backup old settings on upgrade from pre-4.0 versions

= 4.1.0 =
* Fix WC_Settings_API->get_field_key() missing method usage on WC 2.3.x
* Use package passed to calculate_shipping() funciton instead of global cart object for better integration with 3d-party plugins
* Get rid of wbs_remap_shipping_class hook
* Use class autoloader for better performance and code readability

= 4.0.0 =
* Admin UI redesign

= 3.0.0 =
* Country states/regions targeting support

= 2.6.9 =
* Fixed: inconsistent decimal input handling in Shipping Classes section (https://wordpress.org/support/topic/please-enter-in-monetary-decimal-issue)

= 2.6.8 =
* Fixed: plugin settings are not changed on save with WooCommerce 2.3.10 (WooCommerce 2.3.10 compatibility issue)

= 2.6.6 =
* Introduced 'wbs_profile_settings_form' filter for better 3d-party extensions support
* Removed partial localization

= 2.6.5 =
* Min/Max Shipping Price options

= 2.6.3 =
* Improved upgrade warning system
* Fixed warning about Shipping Classes Overrides changes

= 2.6.2 =
* Fixed Shipping Classes Overrides: always apply base Handling Fee

= 2.6.1 =
* Introduced "Subtotal With Tax" option

= 2.6.0 =
* Min/Max Subtotal condition support

= 2.5.1 =
* Introduce "wbs_remap_shipping_class" filter to provide 3dparty plugins an ability to alter shipping cost calculation
* Wordpress 4.1 compatibility testing

= 2.5.0 =

* Shipping classes support
* Ability to choose all countries except specified
* Select All/None buttons for countries
* Purge shipping price calculations cache on configuration changes to reflect actual config immediatelly
* Profiles table look tweaks
* Other small tweaks

= 2.4.2 =

* Fixed: deleting non-currently selected configuration deletes first configuration from the list

= 2.4.1 =

* Updated pot-file required for translations
* Added three nice buttons to plugin settings page
* Prevent buttons in Actions column from wrapping on multiple lines

= 2.4.0 =

* By default, apply Shipping Rate to the extra weight part exceeding Min Weight. Also a checkbox added to switch off this feature.

= 2.3.0 =

* Duplicate profile feature
* New 'Weight Step' option for rough gradual shipping price calculation
* Added more detailed description to the Handling Fee and Shipping Rate fields to make their purpose clear
* Plugin prepared for localization
* Refactoring

= 2.2.3 =

* Fixed: first time saving settings with fresh install does not save anything while reporting successful saving.
* Replace short php tags with their full equivalents to make code more portable.

= 2.2.2 =

Fix "parse error: syntax error, unexpected T_FUNCTION in woocommerce-weight-based-shipping.php on line 610" http://wordpress.org/support/topic/fatal-error-1164.

= 2.2.1 =

Allow zero weight shipping. Thus only Handling Fee is added to the final price.

Previously, weight based shipping option has not been shown to user if total weight of their cart is zero. Since version 2.2.1 this is changed so shipping option is available to user with price set to Handling Fee. If it does not suite your needs well you can return previous behavior by setting Min Weight to something a bit greater zero, e.g. 0.001, so that zero-weight orders will not match constraints and the shipping option will not be shown.


== Screenshots ==

1. A configuration example
2. Another rule settings
3. How that could look to customer