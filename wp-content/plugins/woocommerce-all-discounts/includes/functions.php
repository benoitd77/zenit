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
 * Callback to get the ordered products related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_order_products($var) {
    return $var["condition"] == "order-products";
}

/**
 * Callback to get the order items count related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_order_item_count($var) {
    return $var["condition"] == "order-item-count";
}

/**
 * Callback to get the previously ordered products count rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_previously_ordered_products_count($var) {
    return $var["condition"] == "previously-ordered-products-count";
}

/**
 * Callback to get the previously ordered products in list related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_previously_ordered_products_in_list($var) {
    return $var["condition"] == "previously-ordered-products-in-list";
}

/**
 * Callback to get the previous orders count related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_previous_order_count($var) {
    return $var["condition"] == "previous-order-count";
}

/**
 * Callback to get the total spent on shop related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_total_spent_on_shop($var) {
    return $var["condition"] == "total-spent-on-shop";
}

/**
 * Callback to get the order subtotal related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_order_subtotal($var) {
    return $var["condition"] == "order-subtotal";
}

/**
 * Callback to get the products reviews related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_product_review($var) {
    return (($var["condition"] == "customer-reviewed-product")||($var["condition"] == "customer-reviewed-product-only"));
}

/**
 * Callback to get the single product reviews related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_product_review_only($var) {
    return (($var["condition"] == "customer-reviewed-product-only"));
}

/**
 * Callback to get the products shares related rules in a rules group
 * @param array $var
 * @return type
 */
function wad_filter_product_shares($var) {
    return (($var["condition"] == "customer-share-product"));
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
 * Returns the products targeted by a discount rule for an order
 * @param array $rule Rule to evaluate
 * @param int $product_id Product ID to check the rule only against if needed
 * @return array
 */
function wad_get_order_products_in_list($rule, $product_id=false) {

    $order_items_counts_by_products = wad_get_cart_products_count(true, $product_id);
    $list_products_ids = $rule["order-product"]["list"];
    if(is_a($list_products_ids, 'WAD_Products_List'))
    {
        $list_products_ids=$list_products_ids->get_products();
    }

    if ($rule["order-product"]["operator"] == "IN") {
        $to_count = array_intersect_key($order_items_counts_by_products, array_flip($list_products_ids));
    } else {
        $to_count = array_diff_key($order_items_counts_by_products, array_flip($list_products_ids));
    }

    return $to_count;
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

function wad_get_mailchimp_lists() {
    global $wad_settings;
    $api_key_mc = get_proper_value($wad_settings, "mailchimp-api-key", false);
    if (!($api_key_mc))
        return array();
    $MailChimp = new \Drewm\MailChimp($api_key_mc);
    if (isset($MailChimp)) {
        $results_list = $MailChimp->call("lists/list");
        if (!is_array($results_list))
            return array();
        if (!in_array('Invalid_ApiKey', $results_list)) {
            $global_DATA = $results_list['data'];
            if (isset($global_DATA) AND ! empty($global_DATA)) {
                foreach ($global_DATA as $content) {
                    $content_id[] = $content['id'];
                    $content_name[] = $content['name'];
                }
                return array_combine($content_id, $content_name);
            }
        }
    }
}

function wad_get_sendinblue_lists() {
    global $wad_settings;
    $api_key_sb = get_proper_value($wad_settings, "sendinblue-api-key", false);
    if (!($api_key_sb))
        return array();
    $mailin = new Mailin('https://api.sendinblue.com/v2.0', $api_key_sb);
    $data = array(
        "page" => 1,
        "page_limit" => 50
    );
    if (isset($mailin)) {
        $content_list = $mailin->get_lists($data);
        if (!in_array('Key Not Found In Database', $content_list)) {
            $global_DATA = $content_list['data']['lists'];
            if (isset($global_DATA) AND ! empty($global_DATA)) {
                foreach ($global_DATA as $key => $content) {
                    $content_id[] = $content['id'];
                    $content_name[] = $content['name'];
                }
                return array_combine($content_id, $content_name);
            }
        }
    }
}

function wad_is_user_following_affiliation_link($affiliate_ID_to_check) {
    if (class_exists("Affiliate_WP_Tracking")) {
        $DATA_visite_url = new Affiliate_WP_Tracking();
    }
    if (isset($DATA_visite_url) AND ! empty($DATA_visite_url)) {
        $affiliate_id_current_url = $DATA_visite_url->get_affiliate_id();
    }

    switch ($affiliate_ID_to_check) {

        case '*':

            if (isset($affiliate_id_current_url) AND ! empty($affiliate_id_current_url)) {
                return true;
            } else {
                return false;
            }
            break;

        default:
            $affiliate_id_current_url_to_int = intval($affiliate_id_current_url);
            $affiliate_ID_to_check_to_int = intval($affiliate_ID_to_check);
            if ($affiliate_id_current_url_to_int == $affiliate_ID_to_check_to_int) {
                return true;
            } else {
                return false;
            }
            break;
    }
}

/**
 * Check if the logged in user is subscribed to a specific mailchimp list
 * @global type $mailchimp_user_lists
 * @param type $list_to_check Mailchimp list
 * @return boolean
 */
function wad_is_user_subscribed_to_mailchimp($list_to_check) {
    global $mailchimp_user_lists;

    if (isset($mailchimp_user_lists) AND ! empty($mailchimp_user_lists)) {
        return in_array($list_to_check, $mailchimp_user_lists);
    } else {
        return false;
    }
}

/**
 * Check if the logged in user is subscribed to a specific sendinblue list
 * @global type $sendinblue_user_lists
 * @param type $list_to_check Sendinblue list
 * @return boolean
 */
function wad_is_user_subscribed_to_sendinblue($list_to_check) {
    global $sendinblue_user_lists;

    if (isset($sendinblue_user_lists) AND ! empty($sendinblue_user_lists)) {
        return in_array($list_to_check, $sendinblue_user_lists);
    } else {
        return false;
    }
}

/**
 * Returns the list of URLs shared by the current user
 * @return array
 */
function wad_get_customer_facebook_shared_links() {
    $links = array();
    if (isset($_SESSION["social_data"]["facebook"]["likes"]["data"]))
        $links = array_map(create_function('$o', 'return $o["link"];'), $_SESSION["social_data"]["facebook"]["likes"]["data"]);
    return $links;
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
 * Returns the list of states in woocommerce
 * @return array
 */
function wad_get_all_states() {
        $states = array();
        foreach (WC()->countries->states as $country_code => $country_states) {
            if (!empty($country_states)) {
                foreach ($country_states as $state_code => $country_state) {
                    $states["$country_code|$state_code"] = $country_state;
                }
            }
        }
        asort($states);
        return $states;
}

/**
 * Returns the list of shipping methods
 * @return array
 */
function wad_get_all_shipping_methods() {
    $wc_shipping = WC_Shipping::instance();
    $methods = array();
//    var_dump($wc_shipping->get_shipping_methods());
    foreach ( $wc_shipping->get_shipping_methods() as $method_id => $method ) {
//        if (!empty($country_states)) {
//            foreach ($country_states as $state_code => $country_state) {
                $methods[$method_id] = $method->method_title;
//            }
//        }
    }
    asort($methods);
    return $methods;
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
 * Returns the list of available payment gateways
 * @return array
 */
function wad_get_available_payment_gateways() {
    $available_gateways_arr = array();
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    foreach ($available_gateways as $gateway) {
        $available_gateways_arr[$gateway->id] = $gateway->get_title();
    }
    return $available_gateways_arr;
}

/**
 * Returns the statuses to consider as completed when retrieving the orders
 * @global array $wad_settings
 * @return array
 */
function wad_get_completed_orders_statuses() {
    global $wad_settings;
    $statuses = get_proper_value($wad_settings, "completed-order-statuses", array());
    if (empty($wad_settings["completed-order-statuses"]))
        $statuses = array('wc-processing', 'wc-completed', 'wc-on-hold');
    return $statuses;
}

/**
 * Returns the logged in customer orders
 * @return array
 */
function wad_get_customer_orders() {
    if (!is_user_logged_in())
        return array();
    $current_user = wp_get_current_user();
    $args = array(
        "post_type" => "shop_order",
        "post_status" => wad_get_completed_orders_statuses(),
        "meta_key" => "_customer_user",
        "meta_value" => $current_user->ID,
        "nopaging" => true,
    );
    $orders = get_posts($args);
    return $orders;
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
 * Checks if an email is subscribed to the Newsletter plugin 
 * @global type $newsletter
 * @param string $email
 * @return boolean
 */
function wad_is_user_subscribed_to_newsletterplugin($email) {
    global $newsletter;
    if (function_exists('newsletter_form')) {
        $user = $newsletter->get_user($email);
        if ($user && $user->status == "C")
            return TRUE;
    }
    return FALSE;
}

/**
 * Returns the number of previously ordered products by the logged in customer
 * @global type $wpdb
 * @return int
 */
function wad_get_customer_previous_orders_products_count() {
    global $wpdb;
    if(!is_user_logged_in())
        return 0;
    $customer_id = get_current_user_id();

    $statuses = wad_get_completed_orders_statuses();
    $statuses_str = "('" . implode("','", $statuses) . "')";

    $querystr = "
select
sum(im.meta_value) as quantity
from 
    {$wpdb->prefix}posts as p,
    {$wpdb->prefix}postmeta as pm,
    {$wpdb->prefix}woocommerce_order_items as i,
    {$wpdb->prefix}woocommerce_order_itemmeta as im
where
    p.post_type = 'shop_order'
    and p.ID = pm.post_id
    and p.ID = i.order_id
    and i.order_item_id = im.order_item_id
    and p.post_status in $statuses_str
    and pm.meta_key = '_customer_user'
    and pm.meta_value = $customer_id
    and im.meta_key = '_qty' 

";

    $count = $wpdb->get_var($querystr);
    if(empty($count))
        $count=0;
//        var_dump($count);

    return $count;
}

/**
 * Returns the number of previously ordered products within the list
 * @global type $wpdb
 * @param type $list List to check the products against
 * @return array
 */
function wad_get_customer_previous_orders_products_in_list($list) {
    global $wpdb;
    if(!is_user_logged_in())
        return 0;
    $customer_id = get_current_user_id();
    $numberBought = 0;

    if(is_array( $list))
        $list_products_ids=$list;
    else
    {
        $list_object = new WAD_Products_List($list);
        $list_products_ids = $list_object->get_products();
    }

    $statuses = wad_get_completed_orders_statuses();
    $statuses_str = "('" . implode("','", $statuses) . "')";

    $querystr = "
select
im2.meta_value as product_id,
im3.meta_value as variation_id,
sum(im.meta_value) as quantity
from 
    {$wpdb->prefix}posts as p,
    {$wpdb->prefix}postmeta as pm,
    {$wpdb->prefix}woocommerce_order_items as i,
    {$wpdb->prefix}woocommerce_order_itemmeta as im,
    {$wpdb->prefix}woocommerce_order_itemmeta as im2,
    {$wpdb->prefix}woocommerce_order_itemmeta as im3
where
    p.post_type = 'shop_order'
    and p.ID = pm.post_id
    and p.ID = i.order_id
    and i.order_item_id = im.order_item_id
    and i.order_item_id = im2.order_item_id
    and i.order_item_id = im3.order_item_id
    and p.post_status in $statuses_str
    and pm.meta_key = '_customer_user'
    and pm.meta_value = $customer_id
    and im.meta_key = '_qty' 
    and im2.meta_key ='_product_id'
    and im3.meta_key ='_variation_id'
    group by product_id, variation_id
";

    $results = $wpdb->get_results($querystr, OBJECT);

    //checking if products are in the given list
    foreach ($list_products_ids as $list_products_id) {
        foreach ($results as $result) {
            if ($result->variation_id == $list_products_id || $result->product_id == $list_products_id)
                $numberBought += $result->quantity;
        }
    }
    return strval($numberBought);
}

/**
 * Returns the groups created by the groups plugin
 * @global type $wpdb
 * @return array
 */
function wad_get_available_groups() {
    global $wpdb;
    if (!function_exists("_groups_get_tablename"))
        return array();
    $group_table = _groups_get_tablename('group');
    $query = "SELECT distinct group_id, name FROM $group_table ORDER BY group_id asc";

    $results = $wpdb->get_results($query);
    $groups = array();
    foreach ($results as $result) {
        $groups[$result->group_id] = $result->name;
    }
    return $groups;
}

function wad_get_all_discounts() {
    global $wpdb;
    $sql="select ID, post_title from $wpdb->posts where post_type='o-discount' and post_status='publish'";
    $results=$wpdb->get_results($sql);
    return $results;
}

/**
 * Returns the list of active discounts
 * @global object $wpdb
 * @param bool $group_by_types to group the list by discount types (order | product) or not
 * @return array
 */
function wad_get_active_discounts($group_by_types = false) {
    global $wpdb;
    if ($group_by_types)
        $valid_discounts = array(
            "product" => array(),
            "order" => array(),
        );
    else
        $valid_discounts = array();
    
//    if( !get_option('wad-license-key') )
//            return $valid_discounts;
    
    $date_format='Y-m-d H:i';
    $raw_today = current_time($date_format, false);
    $today = date($date_format, strtotime($raw_today));
    $product_based_actions = wad_get_product_based_actions();
    
    $all_discounts=  wad_get_all_discounts();
    foreach ($all_discounts as $discount)
    {
        $metas = get_post_meta($discount->ID, "o-discount", true);
        if(!isset($metas["start-date"]))
            $metas["start-date"]="";
        if(!isset($metas["end-date"]))
            $metas["end-date"]="";
        if(!isset($metas["period"]))
            $metas["period"]="";
        if(!isset($metas["period-type"]))
            $metas["period-type"]="d";

        //We make sure empty dates are marked as active
        if (empty($metas["start-date"]))
            $start_date = $today;
        else
            $start_date = date($date_format, strtotime($metas["start-date"]));

        if (empty($metas["end-date"]))
            $end_date = $today;
        else
            $end_date = date($date_format, strtotime($metas["end-date"]));
            
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
 * Return the Hybrid Auth connection URL for a social network
 * @param string $network
 * @return string
 */
function wad_get_social_login_url($network) {
    $url = $_SERVER["REQUEST_URI"];

    $url_parts = parse_url($url);
    if (!isset($url_parts['query']))
        $url_parts['query'] = "";
    parse_str($url_parts['query'], $params);

    $params['social-login-wad'] = $network;

    $output_url = "?";
    $count = 1;
    foreach ($params as $key => $value) {
        $output_url.="$key=$value";
        if ($count < count($params))
            $output_url.="&";
    }


    return $output_url;
}

/**
 * Returns the groups the current user belongs to (groups created by the groups plugin)
 * @global object $wpdb
 * @return array
 */
function wad_get_user_groups() {
    global $wpdb;
    global $wad_user_groups;
    if($wad_user_groups!==FALSE)
        return $wad_user_groups;
    if (!function_exists("_groups_get_tablename") || !is_user_logged_in())
        return array();
    $user_id = get_current_user_id();
    $user_group_table = _groups_get_tablename('user_group');
    $query = "SELECT distinct group_id FROM $user_group_table where user_id=$user_id ORDER BY group_id asc";

    $results = $wpdb->get_results($query);
    $groups = array_map(create_function('$o', 'return $o->group_id;'), $results);
    $wad_user_groups=$groups;
    return $groups;
}

/**
 * Returns the reviews on a product by a customer
 * @param array $products_to_check_against Products IDs to check against
 * @param int $review_author Review author ID
 * @return boolean
 */
function wad_check_if_customer_reviewed_any_of_these_products($products_to_check_against, $review_author) {
    $wad_reviewed_products_by_customer=  wad_get_reviewed_products_by_customer($review_author);    
    $intersection=  array_intersect($products_to_check_against, $wad_reviewed_products_by_customer);
    return (!empty($intersection));
}

/**
 * Returns the IDs of the products reviewed by the customer
 * @param int $review_author
 * @return array
 */
function wad_get_reviewed_products_by_customer($review_author) {
    global $wad_reviewed_products_by_customer;
    if($wad_reviewed_products_by_customer!==FALSE)
        return $wad_reviewed_products_by_customer;
    $args = array(
//                'comment_post_ID' => $product_id,
        'author__in' => array($review_author),
        'post_status' => 'publish',
        'post_type' => 'product'
    );
    $commented_products = get_comments($args);
    $reviewed_products_ids = array_map(create_function('$o', 'return $o->comment_post_ID;'), $commented_products);
    $wad_reviewed_products_by_customer=$reviewed_products_ids;
    return $reviewed_products_ids;
}

/**
 * Returns the WP affiliates lists
 * @return array
 */
function wad_get_affiliate_lists() {

    if (class_exists("AffWP_Affiliates_Table")) {
        $affiliates_table = new AffWP_Affiliates_Table();
    }

    if (isset($affiliates_table) AND ! empty($affiliates_table)) {
        $DATA_all_affiliate = json_decode(json_encode($affiliates_table->affiliate_data()), true);

        foreach ($DATA_all_affiliate as $to_affiliate_data) {
            $list_all_affiliate_id[] = $to_affiliate_data["affiliate_id"];
            $all_users = json_decode(json_encode(get_userdata($to_affiliate_data["user_id"])), true);
            $DATA_user = get_proper_value($all_users, "data", false);
            $list_all_affiliate_name[] = get_proper_value($DATA_user, "display_name", false);
        }
        array_push($list_all_affiliate_id, '*');
        array_push($list_all_affiliate_name, 'ANY');

        return array_combine($list_all_affiliate_id, $list_all_affiliate_name);
    }
}

/**
 * Returns the list of products based actions
 * @return array
 */
function wad_get_product_based_actions()
{
    return array("percentage-off-pprice", "fixed-amount-off-pprice", "fixed-pprice");
}

/**
 * Returns the new unit price for free gifts in quantity based discounts
 * @param float $original_price Normal product price
 * @param int $purchased_quantity Purchased quantity
 * @param int $quantity_to_give Quantity to give for free
 * @return float
 */
function wad_get_product_free_gift_price($original_price, $purchased_quantity, $quantity_to_give)
{
    $estimated_subtotal=$purchased_quantity*$original_price;
    $estimated_subtotal_with_gift=$estimated_subtotal-($original_price* $quantity_to_give);
    $normal_price=$estimated_subtotal_with_gift/$purchased_quantity;
    
    return $normal_price;
}

/**
* Return the cart sub total
* @global type $woocommerce
* @param Bool $inc_taxes Including taxes
* @return type
*/
function wad_get_cart_total($inc_taxes = false) {
   global $woocommerce;
   global $wad_cart_total_inc_taxes;
   global $wad_settings;
   $inc_shipping_in_taxes = get_proper_value($wad_settings, 'inc-shipping-in-taxes', 'Yes');
   $cart_total = 0;
   $wc_version = WC()->version;
   
   if(!is_object( $woocommerce->cart))
       return $cart_total;

//   if(!did_action('template_redirect'))
//       return 0;
   if (!$inc_taxes)
       $cart_total = $woocommerce->cart->subtotal_ex_tax;
   else {
       //Optimization: We don't need to recalculate everytime if it has already been calculated once
       if ($wad_cart_total_inc_taxes !== FALSE && $wad_cart_total_inc_taxes !== 0 && !is_null($wad_cart_total_inc_taxes))
           $cart_total = $wad_cart_total_inc_taxes;
       else {
           
           if( version_compare( $wc_version, "3.2.1", "<" ) )
                $taxes=$woocommerce->cart->taxes;
           else
               $taxes=$woocommerce->cart->get_cart_contents_taxes();
           
                $cart_total = $woocommerce->cart->subtotal_ex_tax + array_sum($taxes);
                if(isset($woocommerce->cart->tax_total) && $woocommerce->cart->tax_total>0 && empty($taxes))
                {
                    $cart_total+=$woocommerce->cart->tax_total;
                }
                if ($inc_shipping_in_taxes == 'Yes')
                    $cart_total += $woocommerce->cart->shipping_total;
           }
               
       }
   return $cart_total;
}

/**
     * Return the cart sub total
     * @global type $woocommerce
     * @param Bool $inc_taxes Including taxes
     * @return type
     */
    function wad_get_calculated_cart_total($inc_taxes = false) {
        global $woocommerce;
        global $wad_settings;
        $cart_total = 0;
        $inc_shipping_in_taxes = get_proper_value($wad_settings, 'inc-shipping-in-taxes', 'Yes');
        foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) {
            $cart_total+=$cart_item['line_total'];
            if($inc_taxes)
                $cart_total+=$cart_item['line_subtotal_tax'];
        }

        return $cart_total;
    }