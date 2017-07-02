<?php
    class WbsBucketRates
    {
        /** @var WbsBucketRate[] */
        private $rates;

        public function __construct()
        {
            $this->rates = array();
        }

        public function add(WbsBucketRate $rate)
        {
            $this->rates[$rate->getId()] = $rate;
        }

        public function findById($class)
        {
            return @$this->rates[$class];
        }

        public function listAll()
        {
            return $this->rates;
        }
    }
?>