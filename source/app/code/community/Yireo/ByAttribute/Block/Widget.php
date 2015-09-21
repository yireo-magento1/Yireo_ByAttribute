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
 * Class for ByAttribute widget
 */
class Yireo_ByAttribute_Block_Widget extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**protected function _construct()
    {
        $rt = parent::_construct();

        $cacheKeyData = array(
            Mage_Cms_Model_Block::CACHE_TAG,
            'byattribute.widget',
            Mage::app()->getStore()->getId(),
            intval(Mage::app()->getStore()->isCurrentlySecure())
        );

        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags'     => array(Mage_Cms_Model_Block::CACHE_TAG, Mage_Core_Model_Store::CACHE_TAG),
            'cache_key'      => implode('_', $cacheKeyData),
        ));

        return $rt;
    }*/

    /**
     * Implementation of toHtml() method
     *
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
