<?php

namespace Wpae\App\Field;


use Wpae\App\Categories\CategoriesCollection;

class AgeGroup extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($detailedInformationData['ageGroup'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            if(isset($detailedInformationData['ageGroupAttribute'])) {
                $ageGroupAttribute = $detailedInformationData['ageGroupAttribute'];
                return $this->replaceSnippetsInValue($ageGroupAttribute, $snippetData);
            } else {
                return '';
            }

        } else if ($detailedInformationData['ageGroup'] == 'selectFromProductTaxonomies') {

            $categoryData = $this->feed->getSectionFeedData(self::SECTION);

            $categoryCollection = new CategoriesCollection($categoryData['ageGroupCats']);

            $categoryName = $this->getCategory();

            $mappedCategory = $categoryCollection->findCategory($categoryName);

            if(isset($mappedCategory['selectedAgeGroup'])) {
                return $mappedCategory['selectedAgeGroup'];
            } else {
                return '';
            }

        } else if($detailedInformationData['ageGroup'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($detailedInformationData['ageGroupCV'], $snippetData);
        } else {
            throw new \Exception('Unknown vale '.$detailedInformationData['ageGroup'].' for field ageGroup');
        }
    }

    public function getFieldName()
    {
        return 'age_group';
    }

    /**
     * @return string
     */
    private function getProductCategoryName($product)
    {
        $productCategories = get_the_terms($product->ID, 'product_cat');
        if (!is_array($productCategories) || !count($productCategories)) {
            return '';
        }

        // loop through each cat
        foreach ($productCategories as $category) {
            // get the children (if any) of the current cat
            $children = get_categories(array('taxonomy' => 'product_cat', 'parent' => $category->term_id));

            if (count($children) == 0) {
                // if no children, then echo the category name.
                return $category->name;
            }
        }

        $lastCategory = $productCategories[0];

        return $lastCategory->name;
    }

    /**
     * @return string
     */
    private function getCategory()
    {
        $categoryName = '';

        if ($this->entry->post_type == 'product') {
            $categoryName = $this->getProductCategoryName($this->entry);
        } else if ($this->entry->post_type == 'product_variation') {
            $categoryName = $this->getProductCategoryName(get_post($this->entry->post_parent));
        }

        return $categoryName;
    }
}