<?php

class WbsPackage
{
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public static function fromWcPackage(array $wcPackageData)
    {
        $lines = array();
        foreach ($wcPackageData['contents'] as $wcLineData) {
            $lines[] = new WbsPackageLine(
                $wcLineData['data'],
                $wcLineData['quantity'],
                $wcLineData['line_subtotal'],
                $wcLineData['line_subtotal_tax']
            );
        }

        return new self($lines);
    }

    public function getLines()
    {
        return $this->lines;
    }

    public function getPrice($withTax = true)
    {
        $price = 0;

        foreach ($this->lines as $line) {
            $price += $line->getPrice($withTax);
        }

        return $price;
    }

    public function getWeight()
    {
        $weight = 0;

        foreach ($this->lines as $line) {
            $weight += $line->getWeight();
        }

        return $weight;
    }

    /** @var WbsPackageLine[] */
    private $lines;
}