<?php

/**
 * @property-read float $min
 * @property-read float $max
 * @property-read bool $minInclusive
 * @property-read bool $maxInclusive
 */
class WbsRange
{
    public function __construct($min = null, $max = null, $minInclusive = true, $maxInclusive = false)
    {
        $this->min = isset($min) ? (float)$min : null;
        $this->max = isset($max) ? (float)$max : null;
        $this->minInclusive = isset($minInclusive) ? !!$minInclusive : true;
        $this->maxInclusive = isset($maxInclusive) ? !!$maxInclusive : false;
    }

    public static function fromArray(array $range)
    {
        return new self(
            @$range['min']['value'],
            @$range['max']['value'],
            @$range['min']['inclusive'],
            @$range['max']['inclusive']
        );
    }

    public function toArray()
    {
        return array(
            'min' => array(
                'value' => $this->min,
                'inclusive' => $this->minInclusive,
            ),
            'max' => array(
                'value' => $this->max,
                'inclusive' => $this->maxInclusive,
            ),
        );
    }

    public function includes($value)
    {
        if (isset($this->min) && ($value == $this->min && !$this->minInclusive || $value < $this->min) ||
            isset($this->max) && ($value == $this->max && !$this->maxInclusive || $value > $this->max) ) {
            return false;
        }

        return true;
    }

    public function clamp($value)
    {
        if (isset($this->min) && $value < $this->min) {
            $value = $this->min;
        }

        if (isset($this->max) && $value > $this->max) {
            $value = $this->max;
        }

        return $value;
    }

    public function __get($property)
    {
        return $this->{$property};
    }

    public function __isset($property)
    {
        return isset($this->{$property});
    }

    private $min;
    private $max;
    private $minInclusive;
    private $maxInclusive;
}