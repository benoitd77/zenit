<?php

/**
 * Builds a select dropdpown
 * @param string $name Name
 * @param string $id ID
 * @param string $class Class
 * @param array $options Options
 * @param string|array $selected Selected value
 * @param bool $multiple Can select multiple values
 * @return string HTML code
 */
function get_wad_html_select($name, $id, $class, $options, $selected = '', $multiple = false, $required = false) {
    ob_start();
    if ($multiple && !is_array($selected))
        $selected = array();
    ?>
    <select name="<?php echo $name; ?>" <?php echo ($id) ? "id=\"$id\"" : ""; ?> <?php echo ($class) ? "class=\"$class\"" : ""; ?> <?php echo ($multiple) ? "multiple" : ""; ?> <?php echo ($required) ? "required" : ""; ?> >
        <?php
        if (is_array($options) && !empty($options)) {
            foreach ($options as $value => $label) {
                if (!$multiple && $value == $selected) {
                    ?> <option value="<?php echo $value ?>"  selected="selected" > <?php echo $label; ?></option> <?php
                } else if ($multiple && in_array($value, $selected)) {
                    ?> <option value="<?php echo $value ?>"  selected="selected" > <?php echo $label; ?></option> <?php
                } else {
                    ?> <option value="<?php echo $value ?>"> <?php echo $label; ?></option> <?php
                }
            }
        }
        ?>
    </select>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

/**
 * Remove everything the plugin stores in the cache
 * @global type $wpdb
 */
function wad_remove_transients() {
    global $wpdb;
    $sql = "delete from $wpdb->options where option_name like '%_orion_wad_%transient_%'";
    $wpdb->query($sql);
}

/**
 * Checks if the current page is the checkout page
 * @return boolean
 */
function wad_is_checkout() {
    $is_checkout = false;
    if (!is_admin() && function_exists('is_checkout') && is_checkout())
        $is_checkout = true;

    return $is_checkout;
}

/**
 * Returns the logged user role
 * @global object $wpdb
 * @return string
 */
function wad_get_user_role() {
    if(!is_user_logged_in())
        return 'not-logged-in';
    $uid = get_current_user_id();
    global $wpdb;
    $role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = '{$wpdb->prefix}capabilities' AND user_id = {$uid}");
    if (!$role)
        return 'non-user';
    $rarr = unserialize($role);
    $roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
    return $roles[0];
}

/**
 * Returns the product price AFTER all plugins except ours modified it.
 * @global bool $wad_ignore_product_prices_calculations Make sure our callbacks for woocommerce_get_price are ignored
 * @param WC_Product $product_obj Product object
 * @return float
 */
function wad_get_product_price($product_obj) {
    global $wad_ignore_product_prices_calculations;
    $wad_ignore_product_prices_calculations = true;
    $price = $product_obj->get_price();
    $wad_ignore_product_prices_calculations = false;
    return $price;
}

/**
 * Returns the number of products in the cart grouped by product or not
 * @global type $woocommerce
 * @param bool $by_products whether or not the return the result by product or the sum of the quantities in the cart
 * @param bool $product_id Product ID to limit the search to
 * @return int|array
 */
function wad_get_cart_products_count($by_products = false, $product_id=false) {
    global $woocommerce;
    $count = array();
    if (!empty($woocommerce->cart->cart_contents)) {
        foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item_data) {
            if($product_id && $cart_item_data["variation_id"]!=$product_id && $cart_item_data["product_id"]!=$product_id)
                continue;
            //We add the variations too in case the customer is checking against a variation
            if (isset($cart_item_data["variation_id"]) && !empty($cart_item_data["variation_id"])) {
                if (!isset($count[$cart_item_data["variation_id"]]))
                    $count[$cart_item_data["variation_id"]] = 0;
                $count[$cart_item_data["variation_id"]] += $cart_item_data["quantity"];
            } else {
                if (!isset($count[$cart_item_data["product_id"]]))
                    $count[$cart_item_data["product_id"]] = 0;
                $count[$cart_item_data["product_id"]] += $cart_item_data["quantity"];
            }
        }
    }

    if ($by_products)
        return $count;
    else
        return array_sum($count);
}

/**
 * Returns the list of user roles in the current installation
 * @global type $wp_roles
 * @return array
 */
function wad_get_existing_user_roles() {
    global $wp_roles;
    $roles_arr = array();
    $all_roles = $wp_roles->roles;
    $roles_arr["not-logged-in"]=__("Not logged in", "wad");
    foreach ($all_roles as $role_key => $role_data) {
        $roles_arr[$role_key] = $role_data["name"];
    }
    return $roles_arr;
}
    
/**
 * Returns the list of users in the current installation
 * @return array
 */
function wad_get_existing_users() {
    $users = get_users(array('fields' => array('ID', 'display_name', 'user_email')));
    $users_arr = array();
    foreach ($users as $user) {
        $users_arr[$user->ID] = "$user->display_name($user->user_email)";
    }

    return $users_arr;
}

/**
 * Returns the list of products currently in the cart
 * @global type $woocommerce
 * @return array
 */
function wad_get_cart_products() {
    global $woocommerce;
    $products = array();
    if (empty($woocommerce->cart->cart_contents))
        return $products;

    foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item_data) {
        array_push($products, $cart_item_data["product_id"]);
        //We add the variations too in case the customer is checking against a variation
        if (isset($cart_item_data["variation_id"]) && !empty($cart_item_data["variation_id"]))
            array_push($products, $cart_item_data["variation_id"]);
    }
    return $products;
}

/**
 * Returns the list of active discounts
 * @global object $wpdb
 * @param bool $group_by_types to group the list by discount types (order | product) or not
 * @return array
 */
function wad_get_active_discounts($group_by_types = false) {
    global $wpdb;
    $args = array(
        "post_type" => "o-discount",
        "post_status" => "publish",
        "nopaging" => true,
    );
    if ($group_by_types)
        $valid_discounts = array(
            "product" => array(),
            "order" => array(),
        );
    else
        $valid_discounts = array();
    $discounts = get_posts($args);
    $today = date('Y-m-d');
    $today = date('Y-m-d', strtotime($today));
    $product_based_actions = wad_get_product_based_actions();
    foreach ($discounts as $discount) {
        $metas = get_post_meta($discount->ID, "o-discount", true);

        //We make sure empty dates are marked as active
        if (empty($metas["start-date"]))
            $start_date = date('Y-m-d');
        else
            $start_date = date('Y-m-d', strtotime($metas["start-date"]));

        if (empty($metas["start-date"]))
            $end_date = date('Y-m-d');
        else
            $end_date = date('Y-m-d', strtotime($metas["end-date"]));
        //We check the limit if needed
        $limit = get_proper_value($metas, "users-limit");
        if ($limit) {
            //How many times has this discount been used?
            $sql = "SELECT count(*) FROM $wpdb->postmeta where meta_key='wad_used_discount' and meta_value=$discount->ID";
            $nb_used = $wpdb->get_var($sql);
            if ($nb_used >= $limit)
                continue;
        }

        if (
                (($today >= $start_date) && ($today <= $end_date)) ||
                wad_is_discount_in_valid_period($metas["start-date"], $metas["end-date"], $metas["period"], $metas["period-type"])
        ) {
            if ($group_by_types) {
                if (in_array($metas["action"], $product_based_actions))
                    array_push($valid_discounts["product"], $discount->ID);
                else
                    array_push($valid_discounts["order"], $discount->ID);
            } else
                array_push($valid_discounts, $discount->ID);
        }
    }
    return $valid_discounts;
}

/**
 * Checks if a discount is in the validity period
 * @param string $start Start date
 * @param string $end End date
 * @param int $period
 * @param string $period_type
 * @return boolean
 */
function wad_is_discount_in_valid_period($start, $end, $period, $period_type) {
    if (empty($period)) {
        return false;
    }

    $begin_date = new DateTime($start);
    $end_date = new DateTime($end);

    $today = new DateTime();
    //We make sure the today does not includes the time otherwise it may interfere with the comparison
    $today->setTime(0, 0, 0);

    $nb_elapsed = $today->diff($begin_date);

    $nb_days_elapsed = $nb_elapsed->format("%$period_type");

    $nb_periods_elapsed = intval($nb_days_elapsed / $period);

    if ($period_type == "d") {
        $period_type_str = "day";
    } elseif ($period_type == "m") {
        $period_type_str = "month";
    } else if ($period_type == "y") {
        $period_type_str = "year";
    }

    $last_period_begin_date = $begin_date->modify("+" . ($nb_periods_elapsed * $period) . " $period_type_str");
    $last_period_end_date = $end_date->modify("+" . ($nb_periods_elapsed * $period) . " $period_type_str");

    return (($today >= $last_period_begin_date) && ($today <= $last_period_end_date));
}

/**
 * Returns the product id to use in order to apply the discounts
 * @param type $product Product to check
 * @return int
 */
function wad_get_product_id_to_use($product) {
    $product_class = get_class($product);

    if ($product_class == "WC_Product_Variation") {
        $pid = $product->get_id();
    } else
        $pid = $product->get_id();

    return $pid;
}

/**
 * Returns the list of products based actions
 * @return array
 */
function wad_get_product_based_actions()
{
    return array("percentage-off-pprice", "fixed-amount-off-pprice", "fixed-pprice");
}