<?php
namespace WbsVendors\Dgm\Shengine\Interfaces;


interface IGrouping
{
    /**
     * @param IItem $item
     * @return array of scalars
     */
    function getPackageIds(\WbsVendors\Dgm\Shengine\Interfaces\IItem $item);

    /**
     * @return bool False if no more than one package is expected to be produced by this grouping
     */
    function multiplePackagesExpected();
}