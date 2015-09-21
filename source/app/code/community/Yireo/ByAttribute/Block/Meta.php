<?php
/**
 * Yireo ByAttribute for Magento 
 *
 * @package     Yireo_ByAttribute
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class for block "byattribute_meta"
 */
class Yireo_ByAttribute_Block_Meta extends Mage_Core_Block_Abstract
{
    /**
     * Method to set the meta-title
     */
    public function setTitle($custom_title = null, $skip = null, $addlabel = null)
    {
        // Parse the parameters
        $addlabel = $this->getArrayFromCsv($addlabel);

        // Determine the title
        $title = (!empty($custom_title)) ? $custom_title : $this->getStoreName();

        // Add the category-title
        $title = $this->getCurrentCategory()->getName().$this->getSeperator().$title;

        // Add the attribute-filters
        $filters = $this->getFilters($skip);
        if(!empty($filters)) {
            foreach($filters as $filter) {
                if(in_array(strtolower($filter->getName()), $addlabel)) {
                    $label = $this->__($filter->getName()).': '.$filter->getLabel();
                } else {
                    $label = $filter->getLabel();
                }
                $title = $label.$this->getSeperator().$title;
            }
        }

        // Set the title in the head-block
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($title);
        }

        return true;
    }

    /**
     * Method to set the meta-keywords
     */
    public function setKeywords($skip = null, $addlabel = null)
    {
        // Add the attribute-filters
        $filters = $this->getFilters($skip);
        $filter_keywords = array();
        if(!empty($filters)) {
            foreach($filters as $filter) {
                $filter_keywords[] = $filter->getLabel();
            }
        }

        // Set the keywords in the head-block
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            if ($keywords = $headBlock->getKeywords()) {
                $headBlock->setKeywords(implode( ', ', $filter_keywords) . ', ' . $keywords);
            }
        }

        return true;
    }

    /**
     * Method to get the right filters from the Layered Navigation
     */
    public function getFilters($skip)
    {
        // Read the parameters
        $skip = $this->getArrayFromCsv($skip);

        // Get the filters
        static $filters = false;
        if(!$filters) {
            $filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
            if (!is_array($filters)) {
                $filters = array();
            }
        }

        // Alter the filters a bit
        if(!empty($filters)) {
            foreach($filters as $index => $filter) {

                if(empty($filter) || in_array(strtolower($filter->getName()), $skip)) {
                    unset($filters[$index]);
                    continue;
                }

                $label = strip_tags($filter->getLabel());
                $filter->setLabel($label);

                $filters[$index] = $filter;
            }
        }

        return $filters;
    }

    /**
     * Convert a comma-seperatod list
     */
    public function getArrayFromCsv($string) 
    {
        if(!empty($string)) {
            $array = explode( ',', $string );
            foreach($array as $index => $value) {
                $array[$index] = strtolower(trim($value));
            }
        } else {
            $array = array();
        }

        return $array;
    }

    /**
     * Helper-method to get the current category
     */
    public function getCurrentCategory()
    {
        return Mage::getSingleton('catalog/category')->load($this->getRequest()->get('id'));
    }

    /**
     * Helper-method to get the current category
     */
    public function getStoreName()
    {
        return Mage::getStoreConfig('system/store/name');
    }

    /**
     * Helper-method to get the seperator
     */
    public function getSeperator()
    {
        return ' '.Mage::getStoreConfig('catalog/seo/title_separator').' ';
    }
}
