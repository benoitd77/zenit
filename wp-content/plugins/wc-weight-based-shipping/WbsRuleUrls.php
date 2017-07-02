<?php
class WbsRuleUrls
{
    public static function generic($parameters = array())
    {
        $query = build_query(self::arrayFilterNull($parameters + array(
            "page" => (version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings"),
            "tab" => "shipping",
            "section" => "wc_weight_based_shipping",
        )));

        $url = admin_url("admin.php?{$query}");

        return $url;
    }

    public static function create(array $additionals = array())
    {
        return self::genericWithProfile(WBS_Profile_Manager::instance()->new_profile_id(), $additionals);
    }

    public static function edit(WC_Weight_Based_Shipping $rule, array $parameters = array())
    {
        return self::genericWithProfile($rule->profile_id, $parameters);
    }

    public static function duplicate(WC_Weight_Based_Shipping $rule)
    {
        return self::create(array('duplicate' => $rule->profile_id));
    }

    public static function delete(WC_Weight_Based_Shipping $rule)
    {
        return self::edit($rule, array('delete' => 'yes'));
    }


    private static function genericWithProfile($profileId, array $parameters = array())
    {
        $parameters['wbs_profile'] = $profileId;
        $url = self::generic($parameters);
        return $url;
    }

    public static function arrayFilterNull($array)
    {
        foreach ($array as $key => $value) {
            if ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}