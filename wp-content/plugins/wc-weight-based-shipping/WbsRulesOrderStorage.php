<?php
class WbsRulesOrderStorage
{
    public function __construct($storageKeyName)
    {
        $this->storageKeyName = $storageKeyName;
    }

    public function sort(array $profiles)
    {
        $profilesWithDefinedOrder = array();
        $profilesWithNoDefineOrder = array();

        $weights = $this->getProfilesSortWeights();
        foreach ($profiles as $profile) {
            if (isset($weights[$profile])) {
                $profilesWithDefinedOrder[$weights[$profile]] = $profile;
            } else {
                $profilesWithNoDefineOrder[] = $profile;
            }
        }

        ksort($profilesWithDefinedOrder);

        $sortedProfiles = array_merge($profilesWithDefinedOrder, $profilesWithNoDefineOrder);

        return $sortedProfiles;
    }

    public function add($profile)
    {
        $weights = $this->getProfilesSortWeights();
        $weights[$profile] = max($weights)+1;
        $this->setProfileSortWeights($weights);
    }

    public function remove($profile)
    {
        $weights = $this->getProfilesSortWeights();
        unset($weights[$profile]);
        $this->setProfileSortWeights($weights);
    }

    public function set(array $profiles)
    {
        $this->setProfileSortWeights(array_flip(array_values($profiles)));
    }

    private $storageKeyName;

    private function setProfileSortWeights(array $profileWeights)
    {
        update_option($this->storageKeyName, $profileWeights);
    }

    private function getProfilesSortWeights()
    {
        return get_option($this->storageKeyName, array());
    }
}