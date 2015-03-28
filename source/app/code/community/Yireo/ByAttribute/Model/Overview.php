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
 * ByAttribute Overview model
 */
class Yireo_ByAttribute_Model_Overview extends Mage_Core_Model_Abstract
{
    protected $_attributes = array();

    /**
     * Add an attribute
     */
    public function addAttribute($attribute)
    {
        $attributeModel = $attribute['attribute'];
        if(preg_match('/^id\,([0-9]+)/', $attribute['value'], $match)) {
            $attribute['value'] = (int)$match[1];
            $this->_attributes[] = $attribute;
            return;
        }

        $options = $attributeModel->getFrontend()->getSelectOptions();
        foreach($options as $option) {

            if(empty($option['value'])) {
                continue;
            }

            $urlLabel = Mage::helper('byattribute')->rewriteValue($option['label']);
            if($urlLabel == $attribute['value']) {
                $attribute['value'] = $option['value'];
                break;
            }
        }

        $this->_attributes[] = $attribute;
    }

    /**
     * Get the current attributes
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }
}
