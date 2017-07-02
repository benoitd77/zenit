<?php

namespace Wpae\App\Field;


use Wpae\App\Categories\CategoriesCollection;

class GoogleProductCategory extends Field
{
    const SECTION = 'productCategories';

    public function getValue($snippetData)
    {
        $categoryData = $this->feed->getSectionFeedData(self::SECTION);

        if($categoryData['productCategories'] == 'mapProductCategories') {

            $categoryCollection = new CategoriesCollection($categoryData['cats']);

            $categoryName = $this->getCategory();

            $mappedCategory = $categoryCollection->findCategory($categoryName);

            if(isset($mappedCategory['selectedCategoryId'])) {
                return $mappedCategory['selectedCategoryId'];
            } else {
                return '';
            }

        } else if($categoryData['productCategories'] == 'useWooCommerceProductCategories') {

            return $this->getCategory();

        } else if($categoryData['productCategories'] == self::CUSTOM_VALUE_TEXT) {

            return $this->replaceSnippetsInValue($categoryData['productCategoriesCV'], $snippetData);

        } else {

            throw new \Exception('Unknown value '.$categoryData['productCategories'].' for field product categories');
        }
    }

    public function getFieldName()
    {
        return 'google_product_category';
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