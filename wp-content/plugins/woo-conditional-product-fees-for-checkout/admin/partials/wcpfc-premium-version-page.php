<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
require_once('header/plugin-header.php');
?>
<div id="main-tab">
    <div class="wrapper">
        <div class="tab-dot">
            <div class="wcpfc-main-table res-cl key-featured">
                <h2>Free vs Pro </h2>
                <table class="table-outer premium-free-table" align="center">
                    <thead>
                        <tr class="blue">
                            <th>KEY FEATURES LIST</th>
                            <th>FREE</th>
                            <th>PRO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="dark">
                            <td class="pad"><?php _e('Create Extra Fees based on combination of multiple conditions'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Enable and Disable Conditional Fee'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Apply Fee for each quantity'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Schedule Fee between a time period'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Customers Country'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Customers Postcode'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>

                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Shipping Zone'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Product'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Product Category'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Product Tag'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Logged in User'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Logged in Users Role'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Cart Subtotal (Before Discount)'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Cart Subtotal (After Discount)'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Total Prouduct Quantity in Cart'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Total Product Weight in Cart'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Discount Coupon applied'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Shipping Class'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="dark">
                            <td class="pad"><?php _e('Conditional Fees based on Payment Method selected in checkout'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr>
                            <td class="pad"><?php _e('Conditional Fees based on Shipping Method selected in checkout'); ?></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/trash.png'; ?>"></td>
                            <td><img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/check-mark.png'; ?>"></td>
                        </tr>
                        <tr class="pad radius-s">
                            <td class="pad"></td>
                            <td></td>
                            <td class="green red"><a href="https://store.multidots.com/woocommerce-conditional-product-fees-checkout/" target="_blank">UPGRADE TO <br> PREMIUM VERSION </a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php require_once('header/plugin-sidebar.php'); ?>
    </div>
</div>


