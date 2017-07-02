<?php
class WBS_Shipping_Rate_Override
{
    private $class;
    private $fee;
    private $rate;
    private $weightStep;

    public function getWeightStep()
    {
        return $this->weightStep;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getFee()
    {
        return $this->fee;
    }
}

class WBS_Shipping_Class_Override_Set
{
    private $overrides;

    public function getOverrides()
    {
        return $this->overrides;
    }
}