<?php

class WbsWcTools
{
    public static function purgeWoocommerceShippingCache()
    {
        global $wpdb;

        $transients = $wpdb->get_col("
            SELECT SUBSTR(option_name, LENGTH('_transient_') + 1)
            FROM `{$wpdb->options}`
            WHERE option_name LIKE '_transient_wc_ship_%'
        ");

        foreach ($transients as $transient) {
            delete_transient($transient);
        }
    }
}