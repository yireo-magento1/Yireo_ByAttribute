<?php
/**
 * Yireo ByAttribute for Magento 
 *
 * @package     Yireo_ByAttribute
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * ByAttribute Attribute model
 */
class Yireo_ByAttribute_Model_Attribute
{
    /**
     * Initialize the attribute
     */
    public function load($attributeValue, $categoryId = 0)
    {
        if($categoryId > 0) {
            $this->category = Mage::getModel('catalog/category')->load($categoryId);
        } else {
            $this->category = null;
        }

        if(is_numeric($attributeValue)) {
            $this->setAttribute('attribute_id', $attributeValue);
        } else {
            $this->setAttribute('attribute_code', $attributeValue);
        }

        return $this;
    }

    /**
     * Get a certain option
     */
    public function getOptionTitle($value)
    {
        $options = $this->attribute->getFrontend()->getSelectOptions();
        foreach($options as $option) {
            if($option['value'] == $value) {
                return $option['label'];
            }
        }
        return null;
    }

    /**
     * Get a list of all the options
     */
    public function getOptions($orderby = 'position', $skip_empty = false, $show_product_count = false)
    {
        $caching = (bool)Mage::app()->useCache('collections');

        // Get the options
        $options = $this->attribute->getFrontend()->getSelectOptions();

        // Initializing caching
        if($caching) {

            // Create a cache-ID
            $arguments = array('orderby' => $orderby, 'skip_empty' => $skip_empty, 'show_product_count' => $show_product_count);
            $arguments['attribute_code'] = $this->getAttributeCode();
            if(is_object($this->category)) $arguments['category'] = $this->category->getId(); 
            $cacheId = 'byattribute_attribute_'.md5(serialize($arguments));

            // Fetch the cached data if available
            if($cache = Mage::app()->loadCache($cacheId) && !empty($cache)) {
                $results = unserialize($cache);
                if(!empty($results)) return $results;
            }
        }

        // Set the counter
        $counter = array();
        if($skip_empty == true || $show_product_count == true) {

            // Load the product-collection
            if(is_object($this->category)) {
                $productCollection = $this->category->getProductCollection();
                $productCollection->addAttributeToSelect(array('name', 'manufacturer'));
                $productCollection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            } else {
                $productCollection = Mage::getResourceModel('catalog/product_collection');
                $productCollection->addAttributeToSelect('name');
                $productCollection->addAttributeToSelect($this->getAttributeCode());
                $productCollection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            }

            if($skip_empty == true) {
                //$productCollection->addAttributeToFilter($this->getAttributeCode(), array('isnotnull' => true));
                $productCollection->addAttributeToFilter($this->getAttributeCode(), array('neq' => 'NULL'));
            }

            if(!empty($productCollection)) {
                foreach($productCollection as $product) {

                    $value = $product->getData($this->getAttributeCode());
                    if(!empty($value)) {
                        if(array_key_exists($value, $counter)) {
                            $counter[$value] = $counter[$value] + 1;
                        } else {
                            $counter[$value] = 1;
                        }
                    }
                }
            }
        }

        if(!empty($options)) {
            foreach($options as $index => $option) {

                if(empty($option['label']) || empty($option['value'])) {
                    unset($options[$index]);
                    continue;
                }

                if(!empty($counter)) {
                    $value = $option['value'];
                    if(array_key_exists($value, $counter)) {
                        $option['product_count'] = $counter[$value];
                    } else {
                        $option['product_count'] = 0;
                    }
                }

                // Construct the URL 
                if(!empty($this->category)) {
                    $url = $this->category->getUrl().'?'.$this->getAttributeCode().'='.$option['value'];
                } else {

                    // Rewrite the value
                    if(Mage::helper('byattribute')->getConfigValue('use_label_in_url') && !empty($option['label'])) {
                        $option['value'] = Mage::helper('byattribute')->rewriteValue($option['label']);
                    }

                    $url = Mage::getURL('attribute/'.$this->getAttributeCode().'/'.$option['value']);
                }
                $option['url'] = $url;

                // Save changes into array
                $options[$index] = $option;
            }
        }

        // Sort all options
        if(($orderby == 'alpha' || $orderby == 'ralpha') && !empty($options)) {
            foreach($options as $index => $option) {
                unset($options[$index]);
                $index = $option['label']; 
                $options[$index] = $option;
            }

            if($orderby == 'ralpha') {
                krsort($options);
            } else {
                ksort($options);
            }
        }

        // Save to cache
        if($caching) {
            Mage::app()->saveCache(serialize($options), $cacheId, array('collections'), 86400);
        }

        return $options;
    }

    /**
     * Get the category name
     */
    public function getAttributeTitle()
    {
        return $this->attribute->getFrontendLabel();
    }

    /**
     * Get the attribute title
     */
    public function getCategoryName()
    {
        if(is_object($this->category)) {
            return $this->category->getName();
        } else {
            return '';
        }
    }

    /**
     * Get the attribute code
     */
    public function getAttributeCode()
    {
        return $this->attribute->getAttributeCode();
    }

    /**
     * Set the attribute by ID
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function setAttribute($attributeName = null, $attributeValue = null)
    {
        $product = Mage::getModel('catalog/product');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter($attributeName, $attributeValue);

        $this->attribute = $attributes->getFirstItem()->setEntity($product->getResource());
        return $this->attribute;
    }
}
