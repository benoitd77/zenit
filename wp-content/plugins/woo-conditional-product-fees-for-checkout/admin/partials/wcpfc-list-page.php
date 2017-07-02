<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once('header/plugin-header.php');

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
    $post_id = $_REQUEST['id'];
    $retrieved_nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($retrieved_nonce, 'wppfcnonce'))
        die('Failed security check');
    wp_delete_post($post_id);
    wp_redirect(home_url('/wp-admin/admin.php?page=wcpfc-list'));
    exit;
}
$get_all_fees = get_posts(array(
    'post_type' => 'wc_conditional_fee',
    'post_status' => 'publish',
    'posts_per_page' => -1,
        ));
wp_nonce_field('delete');
?>
<div class="wcpfc-main-table res-cl">
    <div class="product_header_title">
        <h2>
            <?php _e('Product Fees', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
            <a class="add-new-btn"  href="<?php echo home_url('/wp-admin/admin.php?page=wcpfc-add-new'); ?>"><?php _e('Add Product Fees', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></a>
            <a id="detete-conditional-fee" class="detete-conditional-fee button-primary"><?php _e('Delete (Selected)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></a>
        </h2>
    </div>
    <table id="conditional-fee-listing" class="table-outer form-table conditional-fee-listing tablesorter">
        <thead>
            <tr class="wcpfc-head">

                <th><input type="checkbox" name="check_all" class="condition-check-all"></th>
                <th><?php _e('Name', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></th>
                <th><?php _e('Amount', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></th>
                <th><?php _e('Status', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></th>
                <th><?php _e('Action', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($get_all_fees)) {
                $i = 1;
                foreach ($get_all_fees as $fees) {
                    $title = get_the_title($fees->ID) ? get_the_title($fees->ID) : 'Fee';
                    $getFeesCost = get_post_meta($fees->ID, 'fee_settings_product_cost', true);
                    $getFeesStatus = get_post_meta($fees->ID, 'fee_settings_status', true);
                    $wcpfcnonce = wp_create_nonce('wppfcnonce');
                    ?>
                    <tr>
                        <td width="10%">
                            <input type="checkbox" name="multiple_delete_fee[]" class="multiple_delete_fee" value="<?php echo $fees->ID ?>">
                        </td>
                        <td>
                            <a href="<?php echo home_url('/wp-admin/admin.php?page=wcpfc-edit-fee&id=' . $fees->ID . '&action=edit' . '&_wpnonce=' . $wcpfcnonce); ?>"><?php _e($title, WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></a>
                        </td>
                        <td>
                            <?php echo get_woocommerce_currency_symbol() . '&nbsp;' . $getFeesCost; ?>
                        </td>
                        <td>
                            <?php echo (isset($getFeesStatus) && $getFeesStatus == 'on') ? '<span class="active-status">' . _e('Enabled', WOO_CONDITIONAL_FEE_TEXT_DOMAIN) . '</span>' : '<span class="inactive-status">' . _e('Disabled', WOO_CONDITIONAL_FEE_TEXT_DOMAIN) . '</span>'; ?>
                        </td>
                        <td>
                            <a class="fee-action-button button-primary" href="<?php echo home_url('/wp-admin/admin.php?page=wcpfc-edit-fee&id=' . $fees->ID . '&action=edit' . '&_wpnonce=' . $wcpfcnonce); ?>"><?php _e('Edit', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></a>
                            <a class="fee-action-button button-primary" href="<?php echo home_url('/wp-admin/admin.php?page=wcpfc-list&id=' . $fees->ID . '&action=delete' . '&_wpnonce=' . $wcpfcnonce); ?>"><?php _e('Delete', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></a>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?>
        </tbody>
    </table>
</div>
<?php require_once('header/plugin-sidebar.php'); ?>