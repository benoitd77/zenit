<?php
    class WbsItemBucket
    {
        /** @var WbsBucketRate */
        private $rate;
        private $quantity;

        public function __construct($quantity, WbsBucketRate $rate)
        {
            $this->rate = $rate;
            $this->quantity = (float)$quantity;
        }

        public function calculate()
        {
            return $this->rate->rate($this->quantity);
        }

        public function add($quantity)
        {
            $this->quantity += $quantity;
        }

        public function getRate()
        {
            return $this->rate;
        }

        public function getQuantity()
        {
            return $this->quantity;
        }
    }
?>