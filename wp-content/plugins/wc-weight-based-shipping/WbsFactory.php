<?php
class WbsFactory
{
    public static function getRulesOrderStorage()
    {
        if (!isset(self::$rulesOrderStorage)) {
            self::$rulesOrderStorage = new WbsRulesOrderStorage(WC_Weight_Based_Shipping::PLUGIN_PREFIX.'rules_order');
        }

        return self::$rulesOrderStorage;
    }

    private static $rulesOrderStorage;
}