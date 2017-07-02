<?php
    class WbsBucketRate
    {
        public function __construct($id, $flatRate, WbsProgressiveRate $progressiveRate)
        {
            $this->setId($id);
            $this->setFlatRate($flatRate);
            $this->setProgressiveRate($progressiveRate);
        }

        public function rate($amount)
        {
            return $this->flatRate + $this->progressiveRate->rate($amount);
        }

        public function getId()
        {
            return $this->id;
        }

        public function getFlatRate()
        {
            return $this->flatRate;
        }

        public function getProgressiveRate()
        {
            return $this->progressiveRate;
        }


        private $id;
        private $flatRate;
        /** @var WbsProgressiveRate */
        private $progressiveRate;

        private function setId($id)
        {
            if (empty($id)) {
                throw new InvalidArgumentException("Please provide id for bucket rate");
            }

            $this->id = $id;
        }

        private function setFlatRate($rate)
        {
            $this->flatRate = (float)$rate;
        }

        private function setProgressiveRate(WbsProgressiveRate $rate)
        {
            $this->progressiveRate = $rate;
        }
    }
?>