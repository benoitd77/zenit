<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
require_once('header/plugin-header.php');

if (isset($_POST['submitFee']) && $_POST['submitFee'] == 'Submit') {

    $post = $_POST;
    $retrieved_nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($retrieved_nonce, 'feefrm'))
        die('Failed security check');

    $this->wcpfc_fees_conditions_save($post);
} elseif (isset($_POST['submitFee']) && $_POST['submitFee'] == 'Update') {
    $post = $_POST;
    $retrieved_nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($retrieved_nonce, 'feefrm'))
        die('Failed security check');
    $this->wcpfc_fees_conditions_save($post);
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
    $retrieved_nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($retrieved_nonce, 'wppfcnonce'))
        die('Failed security check');
    $post_id = $_REQUEST['id'];

    $btnValue = __('Update', WOO_CONDITIONAL_FEE_TEXT_DOMAIN);
    $fee_title = __(get_the_title($post_id), WOO_CONDITIONAL_FEE_TEXT_DOMAIN);
    $getFeesCost = __(get_post_meta($post_id, 'fee_settings_product_cost', true), WOO_CONDITIONAL_FEE_TEXT_DOMAIN);
    $getFeesPerQty = get_post_meta($post_id, 'fee_chk_qty_price', true);
    $getFeesType = __(get_post_meta($post_id, 'fee_settings_select_fee_type', true), WOO_CONDITIONAL_FEE_TEXT_DOMAIN);
    $getFeesStartDate = get_post_meta($post_id, 'fee_settings_start_date', true);
    $getFeesEndDate = get_post_meta($post_id, 'fee_settings_end_date', true);
    $getFeesTaxable = __(get_post_meta($post_id, 'fee_settings_select_taxable', true), WOO_CONDITIONAL_FEE_TEXT_DOMAIN);
    $getFeesStatus = get_post_meta($post_id, 'fee_settings_status', true);
    $productFeesArray = get_post_meta($post_id, 'product_fees_metabox', true);
    $getFeesOptional = __(get_post_meta($post_id, 'fee_settings_optional_gift', true), WOO_CONDITIONAL_FEE_TEXT_DOMAIN);
    $byDefaultChecked = get_post_meta($post_id, 'by_default_checkbox_checked', true);
} else {
    $post_id = '';
    $btnValue = __('Submit', WOO_CONDITIONAL_FEE_TEXT_DOMAIN);
    $fee_title = '';
    $getFeesCost = '';
    $getFeesPerQty = '';
    $getFeesType = '';
    $getFeesStartDate = '';
    $getFeesEndDate = '';
    $getFeesTaxable = '';
    $getFeesStatus = '';
    $productFeesArray = array();
    $getFeesOptional = '';
    $byDefaultChecked = '';
}
if ($getFeesOptional == 'yes') {
    $style_display = 'display:block;';
} else {
    $style_display = 'display:none;';
}
?>
<div class="text-condtion-is">
    <select class="text-condition">
        <option value="is_equal_to"><?php _e('Equal to ( = )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
        <option value="less_equal_to"><?php _e('Less or Equal to ( <= )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
        <option value="less_then"><?php _e('Less than ( < )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
        <option value="greater_equal_to"><?php _e('Greater or Equal to ( >= )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
        <option value="greater_then"><?php _e('Greater than ( > )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
        <option value="not_in"><?php _e('Not Equal to ( != )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>	
    </select>
    <select class="select-condition">
        <option value="is_equal_to"><?php _e('Equal to ( = )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
        <option value="not_in"><?php _e('Not Equal to ( != )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>	
    </select>
</div>
<div class="default-country-box">
    <?php echo $this->get_country_list(); ?>
</div>
<div class="wcpfc-main-table res-cl">
    <h2><?php _e('Fee: Basic Configuration', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></h2>
    <form method="POST" name="feefrm" action="">
        <?php wp_nonce_field('feefrm'); ?>
        <input type="hidden" name="post_type" value="wc_conditional_fee">
        <input type="hidden" name="fee_post_id" value="<?php echo $post_id ?>">
        <table class="form-table table-outer product-fee-table">
            <tbody>
                <tr valign="top">
                    <th class="titledesc" scope="row"><label for="fee_settings_product_fee_title"><?php _e('Product Fee Title', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?><span class="required-star">*</span></label></th>
                    <td class="forminp mdtooltip">
                        <input type="text" name="fee_settings_product_fee_title" class="text-class" id="fee_settings_product_fee_title" value="<?php echo isset($fee_title) ? $fee_title : ''; ?>" required="1" placeholder="<?php _e('Enter product fees title', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>">
                        <span class="woocommerce_conditional_product_fees_checkout_tab_descirtion"><i class="fa fa-question-circle " aria-hidden="true"></i></span>
                        <p class="description"><?php _e('This product fees title is visible to the customer at the time of checkout.', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></p>
                    </td>

                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row">	
                        <label for="fee_settings_select_fee_type"><?php _e('Select Fee Type', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></label>
                    </th>
                    <td class="forminp mdtooltip">
                        <select name="fee_settings_select_fee_type" id="fee_settings_select_fee_type" class="">
                            <option value="fixed" <?php echo isset($getFeesType) && $getFeesType == 'fixed' ? 'selected="selected"' : '' ?>><?php _e('Fixed', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                            <option value="percentage" <?php echo isset($getFeesType) && $getFeesType == 'percentage' ? 'selected="selected"' : '' ?>><?php _e('Percentage', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                        </select>
                        <span class="woocommerce_conditional_product_fees_checkout_tab_descirtion"><i class="fa fa-question-circle " aria-hidden="true"></i></span>
                        <p class="description"><?php _e('you can charges extra fees on fixed price or percentage wise.', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><label for="fee_settings_product_cost"><?php _e('Fees&nbsp;(' . get_woocommerce_currency_symbol() . ') ', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?><span class="required-star">*</span></label></th>
                    <td class="forminp mdtooltip">
                        <div class="product_cost_left_div">
                            <input type="text" name="fee_settings_product_cost" required="1" class="text-class" id="fee_settings_product_cost" value="<?php echo isset($getFeesCost) ? $getFeesCost : ''; ?>" placeholder="<?php echo get_woocommerce_currency_symbol(); ?>">
                            <span class="woocommerce_conditional_product_fees_checkout_tab_descirtion"><i class="fa fa-question-circle " aria-hidden="true"></i></span>
                            <p class="description">
                                <?php _e('If you select fixed fees type then : you have add fixed cost/fees (Eg. 10, 20) <br/>	
                                If you select percentage wise fees type then : you have add percentage (Eg. 10, 15.20)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><label for="fee_settings_start_date"><?php _e('Start Date', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></label></th>
                    <td class="forminp mdtooltip">
                        <input type="text" name="fee_settings_start_date" class="text-class" id="fee_settings_start_date" value="<?php echo isset($getFeesStartDate) ? $getFeesStartDate : ''; ?>" placeholder="<?php _e('Select start date', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>">
                        <span class="woocommerce_conditional_product_fees_checkout_tab_descirtion"><i class="fa fa-question-circle " aria-hidden="true"></i></span>
                        <p class="description"><?php _e('Select start date which date product fee method will enable on website.', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><label for="fee_settings_end_date"><?php _e('End Date', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></label></th>
                    <td class="forminp mdtooltip">
                        <input type="text" name="fee_settings_end_date" class="text-class" id="fee_settings_end_date" value="<?php echo isset($getFeesEndDate) ? $getFeesEndDate : ''; ?>" placeholder="<?php _e('Select end date', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>">
                        <span class="woocommerce_conditional_product_fees_checkout_tab_descirtion"><i class="fa fa-question-circle " aria-hidden="true"></i></span>
                        <p class="description"><?php _e('Select ending date which date product fee method will disable on website', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th class="titledesc" scope="row"><label for="onoffswitch"><?php _e('Status', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></label></th>
                    <td class="forminp mdtooltip">
                        <label class="switch">
                            <input type="checkbox" name="fee_settings_status" value="on" <?php echo (isset($getFeesStatus) && $getFeesStatus == 'off') ? '' : 'checked'; ?>>
                            <div class="slider round"></div>
                        </label>
                    </td>
                </tr>	
                <tr valign="top">
                    <th class="titledesc" scope="row">	
                        <label for="fee_settings_select_taxable"><?php _e('Is Amount Taxable ?', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></label>
                    </th>
                    <td class="forminp mdtooltip">
                        <select name="fee_settings_select_taxable" id="fee_settings_select_taxable" class="">
                            <option value="no" <?php echo isset($getFeesTaxable) && $getFeesTaxable == 'no' ? 'selected="selected"' : '' ?>><?php _e('No', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                            <option value="yes" <?php echo isset($getFeesTaxable) && $getFeesTaxable == 'yes' ? 'selected="selected"' : '' ?>><?php _e('Yes', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                        </select>                        
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="sub-title">
            <h2><?php _e('Conditional Fee Rule', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></h2>
            <div class="tap"><a id="fee-add-field" class="button button-primary button-large" href="javascript:;"><?php _e('+ Add Row', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></a> </div>
        </div>
        <div class="tap">
            <table id="tbl-product-fee" class="tbl_product_fee table-outer tap-cas form-table product-fee-table">
                <tbody>
                    <?php
                    if (isset($productFeesArray) && !empty($productFeesArray)) {
                        $i = 2;
                        foreach ($productFeesArray as $key => $productfees) {
                            $fees_conditions = isset($productfees['product_fees_conditions_condition']) ? $productfees['product_fees_conditions_condition'] : '';
                            $condition_is = isset($productfees['product_fees_conditions_is']) ? $productfees['product_fees_conditions_is'] : '';
                            $condtion_value = isset($productfees['product_fees_conditions_values']) ? $productfees['product_fees_conditions_values'] : array();
                            ?>
                            <tr id="row_<?php echo $i; ?>" valign="top">
                                <th class="titledesc th_product_fees_conditions_condition" scope="row">
                                    <select rel-id="<?php echo $i; ?>" id="product_fees_conditions_condition_<?php echo $i; ?>" name="fees[product_fees_conditions_condition][]" id="product_fees_conditions_condition" class="product_fees_conditions_condition">
                                        <optgroup label="<?php _e('Location Specific', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>">
                                            <option value="country" <?php echo ($fees_conditions == 'country' ) ? 'selected' : '' ?>><?php _e('Country', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="state" <?php echo ($fees_conditions == 'state' ) ? 'selected' : '' ?> disabled><?php _e('State (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="postcode" <?php echo ($fees_conditions == 'postcode' ) ? 'selected' : '' ?> disabled><?php _e('Postcode (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="zone" <?php echo ($fees_conditions == 'zone' ) ? 'selected' : '' ?>disabled><?php _e('Zone (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        </optgroup>
                                        <optgroup label="<?php _e('Product Specific', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>">
                                            <option value="product" <?php echo ($fees_conditions == 'product' ) ? 'selected' : '' ?>><?php _e('Product', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="category" <?php echo ($fees_conditions == 'category' ) ? 'selected' : '' ?>disabled><?php _e('Category (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="tag" <?php echo ($fees_conditions == 'tag' ) ? 'selected' : '' ?>disabled><?php _e('Tag (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        </optgroup>	
                                        <optgroup label="<?php _e('User Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>" disabled>
                                            <option value="user"><?php _e('User', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="user_role"><?php _e('User Role', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        </optgroup>
                                        <optgroup label="<?php _e('Cart Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>" disabled>
                                            <?php
                                            $currency_symbol = get_woocommerce_currency_symbol();
                                            $currency_symbol = !empty($currency_symbol) ? '(' . $currency_symbol . ')' : '';

                                            $weight_unit = get_option('woocommerce_weight_unit');
                                            $weight_unit = !empty($weight_unit) ? '(' . $weight_unit . ')' : '';
                                            ?>	
                                            <option value="cart_total" <?php echo ($fees_conditions == 'cart_total' ) ? 'selected' : '' ?>><?php _e('Cart Subtotal (Before Discount) ', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?><?php echo $currency_symbol; ?></option>
                                            <option value="cart_totalafter" <?php echo ($fees_conditions == 'cart_totalafter' ) ? 'selected' : '' ?>><?php _e('Cart Subtotal (After Discount) ', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?><?php echo $currency_symbol; ?></option>
                                            <option value="quantity" <?php echo ($fees_conditions == 'quantity' ) ? 'selected' : '' ?>><?php _e('Quantity', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="weight" <?php echo ($fees_conditions == 'weight' ) ? 'selected' : '' ?>><?php _e('Weight ', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?><?php echo $weight_unit; ?></option>
                                            <option value="coupon" <?php echo ($fees_conditions == 'coupon' ) ? 'selected' : '' ?>><?php _e('Coupon', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="shipping_class" <?php echo ($fees_conditions == 'shipping_class' ) ? 'selected' : '' ?>><?php _e('Shipping Class', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        </optgroup>
                                        <optgroup label="<?php _e('Payment Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>" disabled>
                                            <option value="payment" <?php echo ($fees_conditions == 'payment' ) ? 'selected' : '' ?>><?php _e('Payment Gateway', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        </optgroup>
                                        <optgroup label="<?php _e('Shipping Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>"disabled>
                                            <option value="shipping_method" <?php echo ($fees_conditions == 'shipping_method' ) ? 'selected' : '' ?>><?php _e('Shipping Method', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        </optgroup>
                                    </select>
                                </th>	
                                <td class="select_condition_for_in_notin">
                                    <?php if ($fees_conditions == 'cart_total' || $fees_conditions == 'cart_totalafter' || $fees_conditions == 'quantity' || $fees_conditions == 'weight') { ?>
                                        <select name="fees[product_fees_conditions_is][]" class="product_fees_conditions_is_<?php echo $i; ?>">
                                            <option value="is_equal_to" <?php echo ($condition_is == 'is_equal_to' ) ? 'selected' : '' ?>><?php _e('Equal to ( = )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="less_equal_to" <?php echo ($condition_is == 'less_equal_to' ) ? 'selected' : '' ?>><?php _e('Less or Equal to ( <= )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="less_then" <?php echo ($condition_is == 'less_then' ) ? 'selected' : '' ?>><?php _e('Less than ( < )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="greater_equal_to" <?php echo ($condition_is === 'greater_equal_to' ) ? 'selected' : '' ?>><?php _e('Greater or Equal to ( >= )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="greater_then" <?php echo ($condition_is == 'greater_then' ) ? 'selected' : '' ?>><?php _e('Greater than ( > )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="not_in" <?php echo ($condition_is == 'not_in' ) ? 'selected' : '' ?>><?php _e('Not Equal to ( != )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        </select>
                                    <?php } else { ?>
                                        <select name="fees[product_fees_conditions_is][]" class="product_fees_conditions_is_<?php echo $i; ?>">
                                            <option value="is_equal_to" <?php echo ($condition_is == 'is_equal_to' ) ? 'selected' : '' ?>><?php _e('Equal to ( = )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                            <option value="not_in" <?php echo ($condition_is == 'not_in' ) ? 'selected' : '' ?>><?php _e('Not Equal to ( != )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?> </option>
                                        </select>
                                    <?php } ?>
                                </td>
                                <td class="condition-value" id="column_<?php echo $i; ?>">
                                    <?php
                                    $html = '';
                                    if ($fees_conditions == 'country') {
                                        $html .= $this->get_country_list($i, $condtion_value);
                                    } elseif ($fees_conditions == 'product') {
                                        $html .= $this->get_product_list($i, $condtion_value);
                                    }
                                    echo $html;
                                    ?>
                                    <input type="hidden" name="condition_key[<?php echo 'value_' . $i; ?>]" value="">
                                </td>
                                <td><a id="fee-delete-field" rel-id="<?php echo $i; ?>" class="delete-row" href="javascript:;" title="Delete"><i class="fa fa-trash"></i></a></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                        <?php
                    } else {
                        $i = 1;
                        ?>	
                        <tr id="row_1" valign="top">
                            <th class="titledesc th_product_fees_conditions_condition" scope="row">
                                <select rel-id="1" id="product_fees_conditions_condition_1" name="fees[product_fees_conditions_condition][]" id="product_fees_conditions_condition" class="product_fees_conditions_condition">
                                    <optgroup label="<?php _e('Location Specific', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>">
                                        <option value="country"><?php _e('Country', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="state" disabled ><?php _e('State (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="postcode" disabled><?php _e('Postcode (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="zone" disabled><?php _e('Zone (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                    </optgroup>
                                    <optgroup label="<?php _e('Product Specific', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>">
                                        <option value="product"><?php _e('Product', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="category" disabled><?php _e('Category (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="tag" disabled><?php _e('Tag (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                    </optgroup>	
                                    <optgroup label="<?php _e('User Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>" disabled>
                                        <option value="user"><?php _e('User', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="user_role"><?php _e('User Role', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                    </optgroup>
                                    <optgroup label="<?php _e('Cart Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>" disabled>
                                        <?php
                                        $currency_symbol = get_woocommerce_currency_symbol();
                                        $currency_symbol = !empty($currency_symbol) ? '(' . $currency_symbol . ')' : '';

                                        $weight_unit = get_option('woocommerce_weight_unit');
                                        $weight_unit = !empty($weight_unit) ? '(' . $weight_unit . ')' : '';
                                        ?>
                                        <option value="cart_total"><?php _e('Cart Subtotal (Before Discount) ', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?><?php echo $currency_symbol; ?></option>
                                        <option value="cart_totalafter"><?php _e('Cart Subtotal (After Discount) ', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?><?php echo $currency_symbol; ?></option>
                                        <option value="quantity"><?php _e('Quantity', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="weight"><?php _e('Weight', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?> <?php echo $weight_unit; ?></option>
                                        <option value="coupon"><?php _e('Coupon', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                        <option value="shipping_class"><?php _e('Shipping Class', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                    </optgroup>
                                    <optgroup label="<?php _e('Payment Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>" disabled>
                                        <option value="payment"><?php _e('Payment Gateway', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                    </optgroup>
                                    <optgroup label="<?php _e('Shipping Specific (Available in Pro Version)', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?>" disabled>
                                        <option value="shipping_method"><?php _e('Shipping Method', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                    </optgroup>
                                </select>
                                </td>		
                            <td class="select_condition_for_in_notin">
                                <select name="fees[product_fees_conditions_is][]" class="product_fees_conditions_is product_fees_conditions_is_1">
                                    <option value="is_equal_to"><?php _e('Equal to ( = )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                    <option value="not_in"><?php _e('Not Equal to ( != )', WOO_CONDITIONAL_FEE_TEXT_DOMAIN); ?></option>
                                </select>
                            </td>
                            <td id="column_1" class="condition-value">
                                <?php echo $this->get_country_list(1); ?>
                                <input type="hidden" name="condition_key[value_1][]" value="">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <input type="hidden" name="total_row" id="total_row" value="<?php echo $i; ?>">
        </div>
        <p class="submit"><input type="submit" name="submitFee" class="button button-primary button-large" value="<?php echo $btnValue; ?>"></p>
    </form>
</div>

<?php
require_once('header/plugin-sidebar.php');
?>