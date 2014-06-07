<?php
/**
 * Yireo ByAttribute for Magento 
 *
 * @package     Yireo_ByAttribute
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/*
 * Class for ByAttribute widget
 */
class Yireo_ByAttribute_Block_Widget extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /*
     * Implementation of toHtml() method
     *
     * @return null
     */
    protected function _toHtml()
    {
        $byattributeBlock = Mage::app()->getLayout()->getBlock('byattribute.widget');
        if($byattributeBlock) {
            $byattributeBlock->setTitle($this->getData('title'));
            $byattributeBlock->setAttributeValue($this->getData('attribute_value'));
            $byattributeBlock->showProductCount($this->getData('show_product_count'));
            $byattributeBlock->skipEmpty($this->getData('skip_empty'));

            $categoryId = $this->getData('category_id');
            if(!empty($categoryId)) $byattributeBlock->setCategoryId($categoryId);

            $html = $byattributeBlock->toHtml();
            return $html;
        }

        return null;
    }
}
