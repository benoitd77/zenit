<?php
namespace WbsVendors\Dgm\Shengine\Interfaces;

use Dgm\Shengine\Model\Price;


interface IItemAggregatables
{
    const TAXONOMY_TAG = 'tag';
    const TAXONOMY_SHIPPING_CLASS = 'shipping_class';
    const TAXONOMY_CATEGORY = 'category';

    /**
     * @param int $flags
     * @return float
     */
    function getPrice($flags = \WbsVendors\Dgm\Shengine\Model\Price::BASE);

    /**
     * @return float
     */
    function getWeight();
    
    /**
     * @return string[]
     */
    function getTerms($taxonomy);
}