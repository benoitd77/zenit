
=== Abandoned Cart Lite for WooCommerce ===
Contributors: ashokrane, pinal.shah, bhavik.kiri, chetnapatel, tychesoftwares
Tags: abandon cart, cart recovery, recover lost sales, recover woocommerce cart, increase sales with woocommerce
Author URI: https://www.tychesoftwares.com/
Requires at least: 1.3
Tested up to: 4.7.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.me/TycheSoftwares

This easy-to-use plugin allows WooCommerce store owners to recover sales that are lost to abandoned shopping carts by customers. 

== Description ==

Abandoned Cart plugin works in the background, sending email notifications to your guests customers & logged-in customers, reminding them about their abandoned orders.

The Abandoned Cart plugin allows you to recover orders that were just a step away from closing. It enables you to create automatic & well-timed email reminders to be sent to your customers who have added your products to their cart, but did not complete the order. As a result, with this plugin you will start recovering at least 30% or more of your lost sales. Why let this 30% revenue go unclaimed?

Abandoned Cart Lite plugin enables to do the following things:
<ol>
<li>Recover their abandoned carts in a single click</li>
<li>Identify the Abandoned Orders information, including the products that were abandoned</li>
<li>The plugin now captures abandoned guest carts. A guest user's cart will be captured on the Checkout page, if it is abandoned after entering the email address.</li>
<li>Track abandoned orders value v/s recovered orders value</li>
<li>Admin is notified by email when an order is recovered</li>
<li>Works off-the-shelf as it comes with 1 default email template</li>
<li>Create unlimited email templates to be sent at intervals that you set - Intervals start from 1 hour after cart is abandoned</li>
<li>Add custom variables like Customer First Name, Customer Last name, Customer full name, Cart Link & Product Cart Information in the email template</li>
<li>Copy HTML from anywhere & create templates using the powerful Rich Text Editor</li>
<li>Automatically stops email notifications when a customer makes a purchase or uses the cart recovery link</li>
</ol>

**Pro Version:**

**[Abandoned Cart Pro for WooCommerce 4.9](http://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro "Abandoned Cart Pro for WooCommerce")** enables to do the following additional things:
<ol>
<li>Works off-the-shelf as it comes with 3 default email templates</li>
<li>Offer incentives to customers to return and complete their checkout with discounts and coupons</li>
<li>Add custom variables like Customer Name, Product Information, Coupons, etc. in the email template</li>
<li>Embed WooCommerce coupons & also generate unique coupons in the emails being sent to customers</li>
<li>Track whether expired coupons are causing cart abandonment</li>
<li>Track emails sent, emails opened, links clicked for each template/email</li>
<li>Product report allows you to see which products are being abandoned & which are being recovered the most</li>
<li>Start sending email templates within minutes of cart being abandoned.</li>
<li>Admin can send the customer emails to specific abandoned cart(s) using 'Send Custom Email' feature.</li>
<li>If the store is using WPML then admin can translate all the abandoned cart reminder email templates using WPML. Then the emails will be sent to the customers in the same language in which they have abandoned the cart.</li>
<li>Admin can Print or export the Abandoned Orders to CSV format.</li>
<li>Admin can send different email templates to 'Registered Users', 'Guest Users', 'For carts Abandoned with one product' or 'with more than one product' or to 'All'. </li>
<li>Admin can restrict the abandoned carts based on the 'Email address', 'IP address' & 'Domain name'. </li>
<li>Admin can take a glimpse of abandoned carts, recovered orders, and the states of the email template from the Dashboard tab.</li>
</ol>


**Email Sending Setup:**

From version 1.3, it is not mandatory to set a cron job via CPanel for the abandoned cart email notifications to be sent. We are now using WP-Cron that sends the emails automatically whenever a page is requested.

Abandoned Cart Plugin relies on a function called WP-Cron, and this function only runs when there is a page requested. So, if there are no visits to your website, then the scheduled jobs are not run. Generally this method of sending the abandoned cart notification emails is reliable. However, if you are not very confident about the traffic volume of your website, then you can set a manual cron job via Cpanel or any other control panel that your host provides. 

**Some of our other free plugins:**

1. **[Order Delivery Date for WooCommerce](https://wordpress.org/plugins/order-delivery-date-for-woocommerce/ "Order Delivery Date for WooCommerce")**

2. **[Product Delivery Date for WooCommerce - Lite](https://wordpress.org/plugins/product-delivery-date-for-woocommerce-lite/ "Product Delivery Date for WooCommerce")**


**Some of our Pro plugins:**

1. **[Booking & Appointment Plugin for WooCommerce 3.5.1](https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/ "Booking & Appointment Plugin for WooCommerce")**

2. **[Order Delivery Date Pro for WooCommerce 6.4.3](https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/ "Order Delivery Date Pro for WooCommerce")**

3. **[Product Delivery Date Pro for WooCommerce 2.0.1](https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/ "Product Delivery Date Pro for WooCommerce")**

== Installation ==

1. Ensure you have latest version of WooCommerce plugin installed
2. Unzip and upload contents of the plugin to your /wp-content/plugins/ directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. The plugin will start working as per the settings entered.


== Frequently Asked Questions ==

= Can the plugin track carts abandoned by guest users? =

Currently there is no provision for tracking guest carts. This is planned in a future release.

UPDATE: This feature has been released in version 2.2.

= Why are abandoned cart notification emails not getting sent? =

Please ensure you have followed the instructions in "Email Sending Setup" right above this FAQ. Additionally, if you have the PRO version, please verify that you have selected "Enable abandoned cart notifications" option in Settings. With this option turned off, the abandoned carts are recorded, but emails are not sent.

= Where can I find the documentation on how to setup the plugin? =

The documentation can be found **[here](https://www.tychesoftwares.com/woocommerce-abandon-cart-plugin-documentation/ "WooCommerce Abandoned Cart Pro")**. The Lite version is a subset of the Pro version, so the same documentation can be used to refer for the Lite version of the plugin.

== Screenshots ==

1. Lists all Abandoned Orders.

2. Lists Email Templates.

3. Abandoned Cart General Settings.

4. Abandoned Cart Recovery Email Settings.

5. Lists Recovered Orders.

6. Product Report Tab.

== Changelog ==

= 3.6 (01.04.2017) = 

* This version has 1 bug fix.

* Bug Fixed - Warnings were displayed on the Order Received page with WooCommerce version 3.0.0. This is fixed now.

= 3.5 (10.03.2017) =

* This version has 1 new feature and 3 enhancements.
 
* New Feature - New setting named as "Enable tracking carts when customer doesn't enter details" on the settings page. When this setting is enabled it will enable tracking of abandoned products & carts even if the customer does not visit the checkout page or does not enter any details on the checkout page like Name or Email. Tracking will begin as soon as a visitor adds a product to their cart and visits the cart page.

* Enhancement - The default settings value for "Cart abandoned cut-off time" for logged-in users has been modified to 10 minutes.

* Enhancement - The Settings tab of the plugin has been redesigned. Now, the plugin will display 2 sub-tabs for different settings. The sub-tabs are named as "General Settings" & "Email Sending Settings". The settings of add/edit email template "Send From This Name", "Send From This Email Address" & "Send Reply Emails to" has been moved to the Settings -> Email Sending Setting tab.

* Enhancement - In this version we have introduced three new views of abandoned orders in the Abandoned Orders tab. The views named are "All", "Registered Users", "Guest Users" & "Carts without Customer Details". The "Registered Users" view will display only the logged-in user's abandoned carts. The "Guest Users" view will display the guest user's carts & "Carts without Customer Details" view will display all the abandoned carts of the guest users who have not entered an email address or any information on the checkout page.

= 3.4 (08.02.2017) =

* This version has 3 enhancements.

* Enhancement - We have made our plugin compatible with the "Loco Translation" plugin. Now, all the strings from Abandoned Cart Lite for WooCommerce plugin can be translated using the Loco Translation plugin.

* Enhancement - The email template are now compatible with the "WP Better Emails" plugin. If the "WP Better Emails" plugin is activated on the site then CSS style of this plugin will be applied to the test email and abandoned cart reminder email. 

* Enhancement - When the email sending script is executed on the server, it was running a large number of MySQL queries for guest users. That was causing an increase in the server load. In this version, we have optimised the queries. Now, the plugin will not fetch all the guest users record as it will fetch the necessary record which needs to be checked. This will improve the site performance.

= 3.3 (22.11.2016) =

* This version has 2 bug fixes.

* Bug fixed: If the store has the wrong guest user id in the database then it was creating issues where guest users cart was not populating the correct cart data while recovering their cart from reminder email received. This has been fixed.

* Bug fixed: If plugin is being used with PHP7, it was showing an error "Bitwise shifts by negative number will throw an ArithmeticError in PHP 7.0". This has been fixed.

= 3.2 (19.10.2016) =

* This version has 3 enhancements along with 2 bug fixes.

* Enhancements - The plugin will now display the user's billing first name & billing last name for the "Customer Details" column of the abandoned orders tab.

* Enhancements - Earlier rounding off of the decimal values was hardcoded in the plugin. Now, it will round the decimal values based on the WooCommerce setting named "Number of Decimals" which is located at WooCommerce -> Settings menu.

* Enhancements - Earlier plugin was sending the abandoned cart reminder to the customer if the email body content was blank. Now, the plugin will not consider the blank email body template for sending the abandoned cart reminder emails.

* Bug Fixed - If "Send the Abandoned cart emails to the admin" setting is enabled and Admin of the store create manual order from WooCommerce -> Orders page then abandoned cart recovered email was getting sent to the Admin. This has been fixed.

* Bug Fixed - The count of abandoned carts on Recovered Orders tab was updated before "Cart abandoned cut-off time" limit is passed. This has been fixed.

= 3.1 (15.09.2016) =

* New Feature - The email sending logic has been changed. Due to some reason, the abandoned cart remainder emails are not sent to the customers at the time they are intended to be sent, and if after some time, the emails start sending, then multiple email templates whose interval has been reached were all sent together. Now, the plugin will also consider the last abandoned cart reminder email sent time and will keep a time gap between the last email template that was sent & the current email template's time, thereby ensuring emails are not sent together.

* Bug Fixed - The "Alter" table queries have been changed in the plugin. Instead of using get_results(), it now uses the query().

* Bug Fixed - We have used the encryption libraries mcrypt_encrypt, MCRYPT_MODE_CBC, MCRYPT_RIJNDAEL_256 to generate the cart links sent in the abandoned cart reminder emails. Due to this on some servers where these libraries were not enabled, the abandoned cart email notification were not sent to the recipients. This has been fixed. 

* Bug Fixed - Earlier the "InnoDB" table engine type was hardcoded in plugin queries. As a result, the plugins tables were not created where the engine type was other than "InnoDB". This has been fixed.

* Bug Fixed - Some warnings were displayed in the Debug log file. This has been fixed.

= 3.0 (26.07.2016) =

* Enhancements - In this version, the code has been refined throughout the plugin & the folder structure has also been modified.

* Enhancements - Earlier rounding off of the decimal values was hardcoded in the plugin. Now, it will round the decimal values based on the WooCommerce setting named "Number of Decimals" which is located at WooCommerce -> Settings menu.

* Bugs Fixed - When setting "Email admin On Order Recovery" is enabled & order is recovered from the abandoned cart reminder notifications using "Cash On Delivery" payment gateway. Then the order is not considered as recovered & the order was not displayed in the "Recovered Orders" tab. This has been fixed.

* Bugs Fixed - Earlier the abandoned cart reminder emails & abandoned orders details page was not displaying the selected attributes for the variable products. This has been fixed.

* Bugs Fixed - The "Abandoned Date" column of the abandoned orders tab was not considering the time for sorting. This has been fixed.

= 2.9 (05.07.2016) =

* New Feature - New merge tag {{cart.unsubscribe}} has been added for email templates. This merge tag allows user to stop receiving further abandoned cart reminder email notifications. This merge tag has been added to comply with email sending laws in different countries.

* Bugs Fixed - Earlier if any user came from abandoned cart reminder email and place the order using PayPal payment gateway and do not reach the order received page. Then plugin was not considering that order as a recovered order. From now onwards if the user came from the abandoned cart reminder email and place the order using PayPal and does not reach the order received the page. Then plugin will consider that cart as a recovered order.

* Bugs Fixed - When the cart is abandoned as a guest & product have the special character in the attributes name, then it was displaying a blank row with only a checkbox on the Abandoned Orders tab. This has been fixed.

* Tweak - If the order is recovered from the abandoned cart reminder email then it will add a note "This order was abandoned & subsequently recovered." for the order.

= 2.8 (18.05.2016) =

* We have changed the encryption for the links that are sent in the Abandoned cart email notifications. Earlier we were using the mcrypt library for encoding the data. If mcrypt library was not installed on the server, then abandoned cart email notifications were not sent. Now we have used different functions for encoding the string. We have used microtime function & a security key. Using this security key, and after applying an algorithm on it, we generate the encoded string.

* The session now starts only on required pages of the store. Earlier it was started globally. This will help to improve the site performance.

* If billing email address of the logged-in user is not set then it was showing blank space on the abandoned orders list. This has been fixed. Now it will show the email address which was used while registering to the store.

* Earlier if email body was blank and we send the test email then blank email was sent. This has been fixed. Now if email body is blank then test email will not be sent.

* Tweak - Earlier we were populating the guest cart information by looping into the global WooCommerce cart. Now we are not looping & instead using the WooCommerce session object itself.

* Tweak - Earlier if the 'wp-content' directory has been renamed, then wp-load.php file was not included and abandoned cart reminder email was not sent.  Now, we have changed the way of including the wp-load.php file for sending the abandoned cart reminder notifications.

* Tweak - Earlier when {{products.cart}} merge tag is used in abandoned cart email template, then on click of the product name and product image, it was redirecting to the product page. Now it will redirect the user to the cart page.

* Tweak - We are now rounding off the prices with the 'round' function.

= 2.7 (14.04.2016) =

* New setting named as "Email Template Header Text" is added in Add / Edit template page. It will allow to change the header text of the email which have WooCommerce template style setting enabled for the template.

* From this version, the email sending process will run every 15 minutes instead of every 5 minutes. This will result in improved overall performance of the website.

* When Lite version of the plugin is activated on the site then it was not allowing to activate the PRO version of the plugin. This has been fixed.

* When templates are created / updated and if it has the same duration as one of the existing templates, then new template was not saved. This has been fixed.


= 2.6 (02.03.2016) =

* The plugin is now using the TinyMCE editor available in WordPress core for the email body content. The external TinyMCE library is removed from the plugin.

* The plugin is made compatible with Huge IT Image Gallery plugin. The test email was not sent to the user when Huge IT Image Gallery plugin was activated.

* The Product Report tab has been redesigned to look consistent with the WordPress style.

= 2.5.2 (12.02.2016) =

* Abandoned Orders tab has been redesigned with the WordPress WP list tables. "Action" column has been removed. The "Delete" link has been added in the abandoned orders tab. It is capable of deleting the abandoned orders when hovering the abandoned order in the list. It is also capable of deleting abandoned orders in bulk from the "Abandoned orders" tab.

* Email Templates tab has been re-designed to be consistent with the WordPress styling. It is now capable of deleting email templates in bulk. Action column in the Email templates tab has been removed. User can Edit & Delete template using hover affect on the template. This update allows you to activate / deactivate email template from the "Email Templates" page itself without having to edit the template & set it as "Active" by checking the checkbox.

* Recover Orders tab has been re-designed to be consistent with the WordPress style tables. "View Details" column in the Recovered Orders tab has been removed. User can view Details of the recovered cart using the link 'View Details' on the hover affect on 'User name' column.

* New setting named as "Send From This Email Address:" is added in Add / Edit template page. It will allow to change the From Email address of abandoned cart notification.

* New setting named as "Send Reply Emails to:" is added in Add / Edit template page. It will allow the user to change the Reply to email address of the abandoned cart notification.

* If the "Wp-Content" folder name is changed using iThemes Security (formerly Better WP Security) plugin then abandoned cart email notifications were not sent. This has been fixed.

* If the cart has been empty and we have tried to recover the order via email then it was displaying wrong Cart Recovery date. It has been fixed.

* In some cases, when cart or checkout link was clicked from the abandoned cart email notification, it was displaying "Link Expired" in the browser. This has been fixed.

* If the user has emptied their cart before the abandoned cut-off time is reached, for such carts the record in the DB will become blank. Such cart records were displayed in the abandoned orders list. This has been fixed.

= 2.5.1 (05.01.2016) =
* Some warnings were displayed on Email Templates tab. These have been fixed.

= 2.5 (05.01.2016) =

* The Settings page for the plugin has been redone. We are now using the WordPress Settings API for all the options on that page.
* When the plugin is installed & activated for the first time, 1 default email template will also be created. The template will be inactive when the plugin is activated.
* A new setting is added on the Add/Edit Template page named as "Active". If this setting is checked, only then the abandoned cart reminder email will be sent to the customers. If this setting is unchecked, then the email template won't be sent to the customers, but still you can keep it in the plugin. By default, this is unchecked.
* A new setting is added on the Add/Edit Template page named as "Use WooCommerce Template Style". If this setting is checked then abandoned cart reminder email will use the WooCommerce style (header, footer, background, etc.) for the notifications. If it is not checked then the regular email will be sent to the customer as per the formatting that is set in the template editor.
For existing users, this setting will remain unchecked. For new users of the plugin, the setting will be enabled for the existing default email template that is provided with the plugin.
* Abandoned cart email notification will be sent to the client's billing address entered on checkout instead of on the email address added by the user while registering to the website. This applies only for logged in users.
* New shortcode "{{cart.abandoned_date}}" has been introduced in this version. It will display the date and time when the cart was abandoned in the abandoned cart email notification.
* When a customer places an order within the abandoned cart cut off time, then the order received page was displaying a warning. This has been fixed.
* Abandoned Orders tab was not sorting according to the "Date" column. Same way, Recovered Orders tab was not sorting according to "Created On" & "Recovered Date" column. This has been fixed.
* Some warnings were displayed on the Abandoned Orders, Recovered Orders and Product Report tab. These have been fixed.
* The 'mailto' link was not working on the Abandoned Order details page. This has been fixed.
* Tweak - Removed the background white color for the add / edit template page.
* Tweak - Abandoned Orders tab will display the user's billing address using which the cart was abandoned. This applies only for logged in users.

= 2.4 (10.12.2015) =
* Abandon Cart record was not being deleted for users, when they do not reach the order received page but the payment for the order is already done. Also user was receiving the abandoned cart notification for such orders. This has been fixed.

= 2.3 (04.12.2015) =
* A new setting has been added "Email admin on order recovery". It will send an email to the admin's email address when an abandoned cart is recovered by the customer. This will work only for registered users.
* A new tab "Product Report" has been added. It will list the product name, number of times product has been abandoned and the number of times product has been recovered.

= 2.2 (05.11.2015) =
* The plugin now captures abandoned guest carts. A guest user's cart will be captured on the Checkout page, if it is abandoned after entering the email address.
* A new shortcode "{{cart.link}}" is added, which will include the cart URL of your shop.
* Fixed some warnings being displayed in the Settings tab.

= 2.1 (14.10.2015) =
* From this version, you can view the abandoned order details, which includes product details, billing & shipping address, under the Abandoned Orders tab.

= 2.0.1 (20.08.2015) =
* Applied fix for warning displayed on the abandoned orders page.

= 2.0 (20.08.2015) =
* The image link was coming broken while creating or editing the template if the image is present on the same server. This is fixed now.
* Added translations file for Hebrew which was contributed by a user.

= 1.9 (24.07.2015) =
* Fixed security issues pointed out by Wordpress.org review team.

= 1.8 (16.07.2015) =
* The strings for the products table, added using the shortcode {{products.cart}} in email templates have been added to the .pot, .po and .mo files of the plugin. Now the cart data will be translated to the respective language in the reminder emails as well as the test emails.

= 1.7 (07.07.2015) =
* Merge fields like {{products.cart}}, {{customer.firstname}}, etc. will be replaced with dummy data in the test emails that are sent from the template add / edit page. This ensures that you get a very close approximation of the actual email that will be delivered to your customers.
* Product image size in the abandon cart notification emails is set to a fixed height & width now.
* On WordPress Multisite, incorrect table prefix was used due to which the plugin was not functioning correctly on multisite installs. This is fixed now.

= 1.6 (24.06.2015) = 
* We have included .po, .pot and .mo files in the plugin. The plugin strings can now be translated to any language using these files.

= 1.5 (25.05.2015) =
* A shortcode {{products.cart}} can now be added in the abandoned cart notification emails. It will add the product information in the email like Product image, Product name, Quantity, Price & Total. The shortcode needs to be added from the AC menu from the template editor.
* The default value of the field "Cart abandoned cut-off time" in Settings tab was blank when the plugin is installed. This is now set to 60 minutes upon plugin activation.

= 1.4 (14.05.2015) =
* The abandoned cart emails were being sent multiple times for a single email template due to a bug. This is fixed.
* The plugin will now work on WordPress Multisite too.

= 1.3 (01.04.2015) =
* The abandoned cart email notifications are now sent out automatically without the necessity of having to set up a cron job manually.

= 1.2 (02.12.2014) =
* The test emails were not getting sent.
* Warnings fixed for some of the plugin setting pages.
* The image urls in the email were coming broken, this is fixed.

= 1.1 (25.04.2013) =
* Compatibility with WooCommerce 2.x versions
* Fixed 404 errors with images & other files

= 1.0 (18.02.2013) =
* Initial release.

== Upgrade Notice ==

= 3.6 (01.04.2017) = 

* This version has 1 bug fix.

* Bug Fixed - Warnings were displayed on the Order Received page with WooCommerce version 3.0.0. This is fixed now.

= 3.5 (10.03.2017) =

* This version has 1 new feature and 3 enhancements.
 
* New Feature - New setting named as "Enable tracking carts when customer doesn't enter details" on the settings page. When this setting is enabled it will enable tracking of abandoned products & carts even if the customer does not visit the checkout page or does not enter any details on the checkout page like Name or Email. Tracking will begin as soon as a visitor adds a product to their cart and visits the cart page.

* Enhancement - The default settings value for "Cart abandoned cut-off time" for logged-in users has been modified to 10 minutes.

* Enhancement - The Settings tab of the plugin has been redesigned. Now, the plugin will display 2 sub-tabs for different settings. The sub-tabs are named as "General Settings" & "Email Sending Settings". The settings of add/edit email template "Send From This Name", "Send From This Email Address" & "Send Reply Emails to" has been moved to the Settings -> Email Sending Setting tab.

* Enhancement - In this version we have introduced three new views of abandoned orders in the Abandoned Orders tab. The views named are "All", "Registered Users", "Guest Users" & "Carts without Customer Details". The "Registered Users" view will display only the logged-in user's abandoned carts. The "Guest Users" view will display the guest user's carts & "Carts without Customer Details" view will display all the abandoned carts of the guest users who have not entered an email address or any information on the checkout page.

= 3.4 (08.02.2017) =

* This version has 3 enhancements.

* Enhancement - We have made our plugin compatible with the "Loco Translation" plugin. Now, all the strings from Abandoned Cart Lite for WooCommerce plugin can be translated using the Loco Translation plugin.

* Enhancement - The email template are now compatible with the "WP Better Emails" plugin. If the "WP Better Emails" plugin is activated on the site then CSS style of this plugin will be applied to the test email and abandoned cart reminder email. 

* Enhancement - When the email sending script is executed on the server, it was running a large number of MySQL queries for guest users. That was causing an increase in the server load. In this version, we have optimised the queries. Now, the plugin will not fetch all the guest users record as it will fetch the necessary record which needs to be checked. This will improve the site performance.

= 3.3 (22.11.2016) =

* This version has 2 bug fixes.

* Bug fixed: If the store has the wrong guest user id in the database then it was creating issues where guest users cart was not populating the correct cart data while recovering their cart from reminder email received. This has been fixed.

* Bug fixed: If plugin is being used with PHP7, it was showing an error "Bitwise shifts by negative number will throw an ArithmeticError in PHP 7.0". This has been fixed.

= 3.2 (19.10.2016) =

* This version has 3 enhancements along with 2 bug fixes.

* Enhancements - The plugin will now display the user's billing first name & billing last name for the "Customer Details" column of the abandoned orders tab.

* Enhancements - Earlier rounding off of the decimal values was hardcoded in the plugin. Now, it will round the decimal values based on the WooCommerce setting named "Number of Decimals" which is located at WooCommerce -> Settings menu.

* Enhancements - Earlier plugin was sending the abandoned cart reminder to the customer if the email body content was blank. Now, the plugin will not consider the blank email body template for sending the abandoned cart reminder emails.

* Bug Fixed - If "Send the Abandoned cart emails to the admin" setting is enabled and Admin of the store create manual order from WooCommerce -> Orders page then abandoned cart recovered email was getting sent to the Admin. This has been fixed.

* Bug Fixed - The count of abandoned carts on Recovered Orders tab was updated before "Cart abandoned cut-off time" limit is passed. This has been fixed.

= 3.1 (15.09.2016) =

* New Feature - The email sending logic has been changed. Due to some reason, the abandoned cart remainder emails are not sent to the customers at the time they are intended to be sent, and if after some time, the emails start sending, then multiple email templates whose interval has been reached were all sent together. Now, the plugin will also consider the last abandoned cart reminder email sent time and will keep a time gap between the last email template that was sent & the current email template's time, thereby ensuring emails are not sent together.

* Bug Fixed - The "Alter" table queries have been changed in the plugin. Instead of using get_results(), it now uses the query().

* Bug Fixed - We have used the encryption libraries mcrypt_encrypt, MCRYPT_MODE_CBC, MCRYPT_RIJNDAEL_256 to generate the cart links sent in the abandoned cart reminder emails. Due to this on some servers where these libraries were not enabled, the abandoned cart email notification were not sent to the recipients. This has been fixed. 

* Bug Fixed - Earlier the "InnoDB" table engine type was hardcoded in plugin queries. As a result, the plugins tables were not created where the engine type was other than "InnoDB". This has been fixed.

* Bug Fixed - Some warnings were displayed in the Debug log file. This has been fixed.

= 3.0 (26.07.2016) =

* Enhancements - In this version, the code has been refined throughout the plugin & the folder structure has also been modified.

* Enhancements - Earlier rounding off of the decimal values was hardcoded in the plugin. Now, it will round the decimal values based on the WooCommerce setting named "Number of Decimals" which is located at WooCommerce -> Settings menu.

* Bugs Fixed - When setting "Email admin On Order Recovery" is enabled & order is recovered from the abandoned cart reminder notifications using "Cash On Delivery" payment gateway. Then the order is not considered as recovered & the order was not displayed in the "Recovered Orders" tab. This has been fixed.

* Bugs Fixed - Earlier the abandoned cart reminder emails & abandoned orders details page was not displaying the selected attributes for the variable products. This has been fixed.

* Bugs Fixed - The "Abandoned Date" column of the abandoned orders tab was not considering the time for sorting. This has been fixed.

= 2.9 (05.07.2016) =

* New Feature - New merge tag {{cart.unsubscribe}} has been added for email templates. This merge tag allows user to stop receiving further abandoned cart reminder email notifications. This merge tag has been added to comply with email sending laws in different countries.

* Bugs Fixed - Earlier if any user came from abandoned cart reminder email and place the order using PayPal payment gateway and do not reach the order received page. Then plugin was not considering that order as a recovered order. From now onwards if the user came from the abandoned cart reminder email and place the order using PayPal and does not reach the order received the page. Then plugin will consider that cart as a recovered order.

* Bugs Fixed - When the cart is abandoned as a guest & product have the special character in the attributes name, then it was displaying a blank row with only a checkbox on the Abandoned Orders tab. This has been fixed.

* Tweak - If the order is recovered from the abandoned cart reminder email then it will add a note "This order was abandoned & subsequently recovered." for the order.

= 2.8 (18.05.2016) =

* We have changed the encryption for the links that are sent in the Abandoned cart email notifications. Earlier we were using the mcrypt library for encoding the data. If mcrypt library was not installed on the server, then abandoned cart email notifications were not sent. Now we have used different functions for encoding the string. We have used microtime function & a security key. Using this security key, and after applying an algorithm on it, we generate the encoded string.

* The session now starts only on required pages of the store. Earlier it was started globally. This will help to improve the site performance.

* If billing email address of the logged-in user is not set then it was showing blank space on the abandoned orders list. This has been fixed. Now it will show the email address which was used while registering to the store.

* Earlier if email body was blank and we send the test email then blank email was sent. This has been fixed. Now if email body is blank then test email will not be sent.

* Tweak - Earlier we were populating the guest cart information by looping into the global WooCommerce cart. Now we are not looping & instead using the WooCommerce session object itself.

* Tweak - Earlier if the 'wp-content' directory has been renamed, then wp-load.php file was not included and abandoned cart reminder email was not sent.  Now, we have changed the way of including the wp-load.php file for sending the abandoned cart reminder notifications.

* Tweak - Earlier when {{products.cart}} merge tag is used in abandoned cart email template, then on click of the product name and product image, it was redirecting to the product page. Now it will redirect the user to the cart page.

* Tweak - We are now rounding off the prices with the 'round' function.

= 2.7 (14.04.2016) =

* New setting named as "Email Template Header Text" is added in Add / Edit template page. It will allow to change the header text of the email which have WooCommerce template style setting enabled for the template.

* From this version, the email sending process will run every 15 minutes instead of every 5 minutes. This will result in improved overall performance of the website.

* When Lite version of the plugin is activated on the site then it was not allowing to activate the PRO version of the plugin. This has been fixed.

* When templates are created / updated and if it has the same duration as one of the existing templates, then new template was not saved. This has been fixed.

= 2.6 (02.03.2016) =

* The plugin is now using the TinyMCE editor available in WordPress core for the email body content. The external TinyMCE library is removed from the plugin.

* The plugin is made compatible with Huge IT Image Gallery plugin. The test email was not sent to the user when Huge IT Image Gallery plugin was activated.

* The Product Report tab has been redesigned to look consistent with the WordPress style.

= 2.5.2 (12.02.2016) =

* Abandoned Orders tab has been redesigned with the WordPress WP list tables. "Action" column has been removed. The "Delete" link has been added in the abandoned orders tab. It is capable of deleting the abandoned orders when hovering the abandoned order in the list. It is also capable of deleting abandoned orders in bulk from the "Abandoned orders" tab.

* Email Templates tab has been re-designed to be consistent with the WordPress styling. It is now capable of deleting email templates in bulk. Action column in the Email templates tab has been removed. User can Edit & Delete template using hover affect on the template. This update allows you to activate / deactivate email template from the "Email Templates" page itself without having to edit the template & set it as "Active" by checking the checkbox.

* Recover Orders tab has been re-designed to be consistent with the WordPress style tables. "View Details" column in the Recovered Orders tab has been removed. User can view Details of the recovered cart using the link 'View Details' on the hover affect on 'User name' column.

* New setting named as "Send From This Email Address:" is added in Add / Edit template page. It will allow to change the From Email address of abandoned cart notification.

* New setting named as "Send Reply Emails to:" is added in Add / Edit template page. It will allow the user to change the Reply to email address of the abandoned cart notification.

* If the "Wp-Content" folder name is changed using iThemes Security (formerly Better WP Security) plugin then abandoned cart email notifications were not sent. This has been fixed.

* If the cart has been empty and we have tried to recover the order via email then it was displaying wrong Cart Recovery date. It has been fixed.

* In some cases, when cart or checkout link was clicked from the abandoned cart email notification, it was displaying "Link Expired" in the browser. This has been fixed.

* If the user has emptied their cart before the abandoned cut-off time is reached, for such carts the record in the DB will become blank. Such cart records were displayed in the abandoned orders list. This has been fixed.

= 2.5.1 (05.01.2016) =
* Some warnings were displayed on Email Templates tab. These have been fixed.

= 2.5 (05.01.2016) =

* The Settings page for the plugin has been redone. We are now using the WordPress Settings API for all the options on that page.
* When the plugin is installed & activated for the first time, 1 default email template will also be created. The template will be inactive when the plugin is activated.
* A new setting is added on the Add/Edit Template page named as "Active". If this setting is checked, only then the abandoned cart reminder email will be sent to the customers. If this setting is unchecked, then the email template won't be sent to the customers, but still you can keep it in the plugin. By default, this is unchecked.
* A new setting is added on the Add/Edit Template page named as "Use WooCommerce Template Style". If this setting is checked then abandoned cart reminder email will use the WooCommerce style (header, footer, background, etc.) for the notifications. If it is not checked then the regular email will be sent to the customer as per the formatting that is set in the template editor.
For existing users, this setting will remain unchecked. For new users of the plugin, the setting will be enabled for the existing default email template that is provided with the plugin.
* Abandoned cart email notification will be sent to the client's billing address entered on checkout instead of on the email address added by the user while registering to the website. This applies only for logged in users.
* New shortcode "{{cart.abandoned_date}}" has been introduced in this version. It will display the date and time when the cart was abandoned in the abandoned cart email notification.
* When a customer places an order within the abandoned cart cut off time, then the order received page was displaying a warning. This has been fixed.
* Abandoned Orders tab was not sorting according to the "Date" column. Same way, Recovered Orders tab was not sorting according to "Created On" & "Recovered Date" column. This has been fixed.
* Some warnings were displayed on the Abandoned Orders, Recovered Orders and Product Report tab. These have been fixed.
* The 'mailto' link was not working on the Abandoned Order details page. This has been fixed.
* Tweak - Removed the background white color for the add / edit template page.
* Tweak - Abandoned Orders tab will display the user's billing address using which the cart was abandoned. This applies only for logged in users.

= 2.4 (10.12.2015) =
* Abandon Cart record was not being deleted for users, when they do not reach the order received page but the payment for the order is already done. Also user was receiving the abandoned cart notification for such orders. This has been fixed.

= 2.3 (04.12.2015) =
* A new setting has been added "Email admin on order recovery". It will send an email to the admin's email address when an abandoned cart is recovered by the customer. This will work only for registered users.
* A new tab "Product Report" has been added. It will list the product name, number of times product has been abandoned and the number of times product has been recovered.

= 2.2 (05.11.2015) =
* The plugin now captures abandoned guest carts. A guest user's cart will be captured on the Checkout page, if it is abandoned after entering the email address.
* A new shortcode "{{cart.link}}" is added, which will include the cart URL of your shop.
* Fixed some warnings being displayed in the Settings tab.

= 2.1 (14.10.2015) =
* From this version, you can view the abandoned order details, which includes product details, billing & shipping address, under the Abandoned Orders tab.

= 2.0.1 (20.08.2015) =
* Applied fix for warning displayed on the abandoned orders page.

= 2.0 (20.08.2015) =
* The image link was coming broken while creating or editing the template if the image is present on the same server. This is fixed now.
* Added translations file for Hebrew which was contributed by a user.

= 1.9 (24.07.2015) =
* Fixed security issues pointed out by Wordpress.org review team.

= 1.8 (16.07.2015) =
* The strings for the products table, added using the shortcode {{products.cart}} in email templates have been added to the .pot, .po and .mo files of the plugin. Now the cart data will be translated to the respective language in the reminder emails as well as the test emails.

= 1.7 (07.07.2015) =
* Merge fields like {{products.cart}}, {{customer.firstname}}, etc. will be replaced with dummy data in the test emails that are sent from the template add / edit page. This ensures that you get a very close approximation of the actual email that will be delivered to your customers.
* Product image size in the abandon cart notification emails is set to a fixed height & width now.
* On WordPress Multisite, incorrect table prefix was used due to which the plugin was not functioning correctly on multisite installs. This is fixed now.

= 1.6 (24.06.2015) = 
* We have included .po, .pot and .mo files in the plugin. The plugin strings can now be translated to any language using these files.

= 1.5 (25.05.2015) =
* A shortcode {{products.cart}} can now be added in the abandoned cart notification emails. It will add the product information in the email like Product image, Product name, Quantity, Price & Total. The shortcode needs to be added from the AC menu from the template editor.
* The default value of the field "Cart abandoned cut-off time" in Settings tab was blank when the plugin is installed. This is now set to 60 minutes upon plugin activation.

= 1.4 (14.05.2015) =
* The abandoned cart emails were being sent multiple times for a single email template due to a bug. This is fixed.
* The plugin will now work on WordPress Multisite too.

= 1.3 (01.04.2015) =
* The abandoned cart email notifications are now sent out automatically without the necessity of having to set up a cron job manually.

= 1.2 (02.12.2014) =
* The test emails were not getting sent.
* Warnings fixed for some of the plugin setting pages.
* The image urls in the email were coming broken, this is fixed.

= 1.1 (25.04.2013) =
* Compatibility with WooCommerce 2.x versions
* Fixed 404 errors with images & other files

= 1.0 (18.02.2013) =
* Initial release.