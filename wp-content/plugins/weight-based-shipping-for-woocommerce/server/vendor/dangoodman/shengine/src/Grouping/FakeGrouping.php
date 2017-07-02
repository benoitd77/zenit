<?php
namespace WbsVendors\Dgm\Shengine\Grouping;

use Dgm\Shengine\Interfaces\IGrouping;
use Dgm\Shengine\Interfaces\IItem;


class FakeGrouping implements \WbsVendors\Dgm\Shengine\Interfaces\IGrouping
{
    public function __construct(array $packageIds = array('all'))
    {
        $this->packageIds = $packageIds;
    }

    public function getPackageIds(\WbsVendors\Dgm\Shengine\Interfaces\IItem $item)
    {
        return $this->packageIds;
    }

    public function multiplePackagesExpected()
    {
        return count($this->packageIds) > 1;
    }

    private $packageIds;
}