<?php

class WbsPackageLine
{
    public function __construct(WC_Product $product, $quantity, $subtotal, $tax)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->subtotal = $subtotal;
        $this->tax = $tax;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getWeight()
    {
        return (float)$this->product->get_weight() * $this->quantity;
    }

    public function getPrice($withTax = false)
    {
        return $this->subtotal + ($withTax ? $this->tax : 0);
    }

    private $product;
    private $subtotal;
    private $tax;
}