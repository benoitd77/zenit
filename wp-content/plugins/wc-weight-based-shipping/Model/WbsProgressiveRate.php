<?php
class WbsProgressiveRate
{
    public function __construct($cost = 0, $step = 0, $skip = 0)
    {
        $cost = isset($cost) ? $cost : 0;
        $step = isset($step) ? $step : 0;
        $skip = isset($skip) ? $skip : 0;

        if (!is_numeric($cost) || !is_numeric($step) || !is_numeric($skip)) {
            throw new InvalidArgumentException(sprintf(
                "%s: invalid argument value(s): '%s', '%s', '%s'.",
                get_class(), var_export($cost, true), var_export($step, true), var_export($skip, true)
            ));
        }

        $this->cost = $cost;
        $this->step = $step;
        $this->skip = $skip;
    }

    public static function fromArray(array $input)
    {
        return new self(
            @$input['cost'],
            @$input['step'],
            @$input['skip']
        );
    }

    public function toArray()
    {
        return array(
            'cost' => $this->cost,
            'step' => $this->step,
            'skip' => $this->skip,
        );
    }

    public function rate($amount)
    {
        $amount = max(0, $amount - $this->skip);

        if ($this->step != 0) {
            $amount = ceil(round($amount / $this->step, 5));
        }

        return $amount * $this->cost;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function getStep()
    {
        return $this->step;
    }

    public function getSkip()
    {
        return $this->skip;
    }

    private $cost;
    private $step;
    private $skip;
}