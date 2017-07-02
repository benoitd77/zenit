<?php
namespace WbsVendors\Dgm\Shengine\Woocommerce\Converters;

use Dgm\Shengine\Attributes\ProductVariationAttribute;
use Dgm\Shengine\Grouping\AttributeGrouping;
use Dgm\Shengine\Interfaces\IItem;
use Dgm\Shengine\Interfaces\IPackage;
use Dgm\Shengine\Model\Address;
use Dgm\Shengine\Model\Customer;
use Dgm\Shengine\Model\Destination;
use Dgm\Shengine\Model\Dimensions;
use Dgm\Shengine\Model\Package;
use Dgm\Shengine\Model\Price;
use Dgm\Shengine\Woocommerce\Model\Item\WoocommerceItem;
use Dgm\Shengine\Woocommerce\Model\Item\WpmlAwareItem;
use WC_Cart;
use WC_Product;
use WC_Product_Variation;


class PackageConverter
{
    public static function fromCoreToWoocommerce(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        $wcpkg = array();
        $wcpkg['contents'] = self::makeWcItems($package);
        $wcpkg['contents_cost'] = self::calcContentsCostField($wcpkg['contents']);
        $wcpkg['applied_coupons'] = $package->getCoupons();
        $wcpkg['user']['ID'] = self::getCustomerId($package);
        $wcpkg['destination'] = self::getDestination($package);
        return $wcpkg;
    }

    public static function fromWoocommerceToCore(array $_package)
    {
        $items = array();

        foreach ((array)@$_package['contents'] as $_item) {

            /** @var WC_Product $product */
            $product = $_item['data'];
            $quantity = $_item['quantity'];

            if ($quantity == 0 || !$product->needs_shipping()) {
                continue;
            }

            $price = new \WbsVendors\Dgm\Shengine\Model\Price(
                $_item['line_subtotal'] / $quantity,
                $_item['line_subtotal_tax'] / $quantity,
                ($_item['line_subtotal'] - $_item['line_total']) / $quantity,
                ($_item['line_subtotal_tax'] - $_item['line_tax']) / $quantity
            );

            $variationAttributes = array();
            foreach ((@$_item['variation'] ?: array()) as $attr => $value) {
                if (substr_compare($attr, 'attribute_', 0, 10) == 0) {
                    $variationAttributes[substr($attr, 10)] = $value;
                }
            }
            
            while ($quantity--) {
                $item = new \WbsVendors\Dgm\Shengine\Woocommerce\Model\Item\WpmlAwareItem();
                $item->setPrice($price);
                $item->setDimensions(self::getDimensions($product));
                $item->setProductId((string)self::getProductAttr($product, 'id'));
                $item->setWeight((float)$product->get_weight());
                $item->setOriginalProductObject($product);
                $item->setProductVariationId(self::getProductAttr($product, 'variation_id'));
                $item->setVariationAttributes($variationAttributes);
                $items[] = $item;
            }
        }

        $destination = null;
        if (($dest = @$_package['destination']) && @$dest['country']) {

            $destination = new \WbsVendors\Dgm\Shengine\Model\Destination(
                $dest['country'],
                @$dest['state'],
                @$dest['postcode'],
                @$dest['city'],
                new \WbsVendors\Dgm\Shengine\Model\Address(@$dest['address'], @$dest['address_2'])
            );
        }

        $customer = null;
        if (isset($_package['user']['ID'])) {
            $customer = new \WbsVendors\Dgm\Shengine\Model\Customer($_package['user']['ID']);
        }

        $coupons = array();
        if (!empty($_package['applied_coupons'])) {
            $coupons = array_map('strval', $_package['applied_coupons']);
        }

        return new \WbsVendors\Dgm\Shengine\Model\Package($items, $destination, $customer, $coupons);
    }

    private static function makeWcItems(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        $wcItems = array();

        $lineGrouping = new \WbsVendors\Dgm\Shengine\Grouping\AttributeGrouping(new \WbsVendors\Dgm\Shengine\Attributes\ProductVariationAttribute());
        $lines = $package->split($lineGrouping);

        foreach ($lines as $line) {

            $items = $line->getItems();
            if (!$items) {
                continue;
            }

            /** @var IItem $item */
            $item = reset($items);

            $product = null; {

                if ($item instanceof \WbsVendors\Dgm\Shengine\Woocommerce\Model\Item\WoocommerceItem) {
                    /** @var WoocommerceItem $item */
                    $product = $item->getOriginalProductObject();
                }

                if (!isset($product)) {

                    $productPostId = $item->getProductVariationId();
                    if (!isset($productPostId)) {
                        $productPostId = $item->getProductId();
                    }

                    $product = wc_get_product($productPostId);
                }
            }

            $wcItem = array(); {

                $wcItem['data'] = $product;
                $wcItem['quantity'] = count($items);

                $wcItem['product_id'] = self::getProductAttr($product, 'id');
                $wcItem['variation_id'] = self::getProductAttr($product, 'variation_id');
                $wcItem['variation'] = ($product instanceof WC_Product_Variation) ? $product->get_variation_attributes() : null;

                $wcItem['line_total'] = $line->getPrice(\WbsVendors\Dgm\Shengine\Model\Price::WITH_DISCOUNT);
                $wcItem['line_tax'] = $line->getPrice(\WbsVendors\Dgm\Shengine\Model\Price::WITH_DISCOUNT | \WbsVendors\Dgm\Shengine\Model\Price::WITH_TAX) - $wcItem['line_total'];
                $wcItem['line_subtotal'] = $line->getPrice(\WbsVendors\Dgm\Shengine\Model\Price::BASE);
                $wcItem['line_subtotal_tax'] = $line->getPrice(\WbsVendors\Dgm\Shengine\Model\Price::WITH_TAX) - $wcItem['line_subtotal'];
            }

            // We don't want to have a cart instance dependency just to generate line id. generate_cart_id() method
            // is a static method both conceptually and actually, i.e. it does not (should not) depend on actual
            // cart instance. So we'd rather call it statically.
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $wcItemId = @WC_Cart::generate_cart_id($wcItem['product_id'], $wcItem['variation_id'], $wcItem['variation']);

            $wcItems[$wcItemId] = $wcItem;
        }

        return $wcItems;
    }

    private static function calcContentsCostField($wcItems)
    {
        $value = 0;

        foreach ($wcItems as $item) {

            /** @var WC_Product $product */
            $product = $item['data'];

            if ($product->needs_shipping() && isset($item['line_total'])) {
                $value += $item['line_total'];
            }
        }

        return $value;
    }

    private static function getCustomerId(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        if ($customer = $package->getCustomer()) {
            return $customer->getId();
        }

        return null;
    }

    private static function getDestination(\WbsVendors\Dgm\Shengine\Interfaces\IPackage $package)
    {
        if ($destination = $package->getDestination()) {

            $address = $destination->getAddress();

            return array_map('strval', array(
                'country' => $destination->getCountry(),
                'state' => $destination->getState(),
                'postcode' => $destination->getPostalCode(),
                'city' => $destination->getCity(),
                'address' => $address ? $address->getLine1() : null,
                'address_2' => $address ? $address->getLine2() : null,
            ));
        }

        return null;
    }

    private static function getDimensions(WC_Product $product)
    {
        return new \WbsVendors\Dgm\Shengine\Model\Dimensions(
            (float)self::getProductAttr($product, 'length'),
            (float)self::getProductAttr($product, 'width'),
            (float)self::getProductAttr($product, 'height')
        );
    }

    private static function getProductAttr(WC_Product $product, $attr)
    {
        if (version_compare(WC()->version, '3.0', '>=')) {
            switch ((string)$attr) {

                case 'id':
                    return $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();

                case 'variation_id':
                    return $product->is_type('variation') ? $product->get_id() : null;

                default:
                    return call_user_func(array(\WbsVendors_CCR::klass($product), "get_{$attr}"));
            }
        }

        return $product->{$attr};
    }
}