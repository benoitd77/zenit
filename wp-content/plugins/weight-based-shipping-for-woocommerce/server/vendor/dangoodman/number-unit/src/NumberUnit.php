<?php
namespace WbsVendors\Dgm\NumberUnit;

use Dgm\Comparator\NumberComparator;


class NumberUnit extends \WbsVendors\Dgm\Comparator\NumberComparator
{
    /** @var self */
    static $ASIS;

    /** @var self */
    static $INT;


    public function ceil($value)
    {
        return ceil($this->normalize($value));
    }

    public function floor($value)
    {
        return floor($this->normalize($value));
    }
}

\WbsVendors\Dgm\NumberUnit\NumberUnit::$ASIS = new \WbsVendors\Dgm\NumberUnit\NumberUnit(null);
\WbsVendors\Dgm\NumberUnit\NumberUnit::$INT = new \WbsVendors\Dgm\NumberUnit\NumberUnit(1);
