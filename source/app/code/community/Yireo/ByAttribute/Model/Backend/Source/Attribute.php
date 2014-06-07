<?php
/**
 * Yireo ByAttribute for Magento 
 *
 * @package     Yireo_ByAttribute
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_ByAttribute_Model_Backend_Source_Attribute
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        // Fetch all attributes
        $product = Mage::getModel('catalog/product');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('frontend_input', array('select', 'multiselect'))
        ;

        // Construct the options
        $options = array();
        foreach($attributes as $attribute) {
            $options[] = array(
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel(),
            );
        }

        return $options;
    }
}
