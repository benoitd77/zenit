<?php

namespace Wpae\App\Field;


use Wpae\App\Categories\CategoriesCollection;

class Gender extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if ($detailedInformationData['gender'] == 'selectProductTaxonomies') {

            $categoryData = $this->feed->getSectionFeedData(self::SECTION);

            $categoryCollection = new CategoriesCollection($categoryData['genderCats']);

            $categoryName = $this->getCategory();

            $mappedCategory = $categoryCollection->findCategory($categoryName);

            if(isset($mappedCategory['selectedGender'])) {
                return $mappedCategory['selectedGender'];
            } else {
                return '';
            }

        } else if ($detailedInformationData['gender'] == self::SELECT_FROM_WOOCOMMERCE_PRODUCT_ATTRIBUTES) {

            $genderAttribute = $detailedInformationData['genderAttribute'];
            return $this->replaceSnippetsInValue($genderAttribute, $snippetData);

        }
        else if ($detailedInformationData['gender'] == self::CUSTOM_VALUE_TEXT) {

            return $this->replaceSnippetsInValue($detailedInformationData['genderCV'], $snippetData);

        } else if ($detailedInformationData['gender'] == 'autodetectBasedOnProductTaxonomies') {

            //TODO: Implement algorithm to detect gender
            $productAttributes = get_object_taxonomies( 'product', 'objects' );
        }
        else {
            throw new \Exception('Unknown vale '.$detailedInformationData['gender'].' for field gender');
        }
    }

    public function getFieldName()
    {
        return 'gender';
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