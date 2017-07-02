<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once('header/plugin-header.php');
global $wpdb;
$current_user = wp_get_current_user();
if (!get_option('wcpfc_free_plugin_notice_shown')) {
    ?>
    <div id="wcpfc_free_dialog">
        <p><?php _e('Subscribe for latest plugin update and get notified when we update our plugin and launch new products for free!', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></p>
        <p><input type="text" id="txt_user_sub_wcpfc_free" class="regular-text" name="txt_user_sub_wcpfc_free" value="<?php echo $current_user->user_email; ?>"></p>
    </div>
<?php } ?>

<div class="wcpfc-main-table res-cl">
    <h2><?php _e('Thanks For Installing WooCommerce Conditional Product Fees for Checkout', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></h2>
    <table class="table-outer">
        <tbody>
            <tr>
                <td class="fr-2">
                    <p class="block gettingstarted"><strong><?php _e('Getting Started', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?> </strong></p>
                    <p class="block textgetting">
                        <?php _e('The plugin is for store owners can setup conditional rules where product fees will be added to the Cart based on what is in the cart, who is buying it,what is cart quantity / weight , which coupon used or where the products are being shipped.', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
                    </p>
                    <p class="block textgetting">
                        <?php _e('It is a valuable tool for store owners for creating and managing complex fee rules in their store without the help of a developer!', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
                    </p>					
                    <p class="block textgetting">
                        <strong><?php _e('Step 1', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?> :</strong> <?php _e('Add conditional product fees for checkout ', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
                    <p class="block textgetting"><?php _e('Add product fees title, cost / fee, and set conditional product fees rules as per your requirement.', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
                    </p>
                    <span class="gettingstarted">
                        <img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/Getting_Started_01.png'; ?>">										
                    </span>
                    </p>
                    <p class="block gettingstarted textgetting">
                        <strong><?php _e('Step 2', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?> :</strong> <?php _e('All Conditional product fees method display as per below.', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
                        <span class="gettingstarted">
                            <img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/Getting_Started_02.png'; ?>">
                        </span>
                    </p>
                    <p class="block gettingstarted textgetting">
                        <strong><?php _e('Step 3', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?> :</strong> <?php _e('View conditional product fees on checkout page as per your rules', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
                        <span class="gettingstarted">
                            <img src="<?php echo WCPFC_PLUGIN_URL . 'admin/images/Getting_Started_03.png'; ?>">
                        </span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php require_once('header/plugin-sidebar.php'); ?>