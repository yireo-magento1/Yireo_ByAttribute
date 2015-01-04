<?php
/**
 * Yireo ByAttribute
 *
 * @package     Yireo_ByAttribute
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * ByAttribute default controller
 */
class Yireo_ByAttribute_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Display products with a specific attribute
     *
     */
    public function indexAction()
    {
        // Fetch all the attributes
        $attribute_codes = $this->_getAttributeCodes();
 
        $model = Mage::getSingleton('byattribute/overview');
        $originalPathInfo = $this->getRequest()->getOriginalPathInfo();
        $params = $this->getRequest()->getParams();

        if(!empty($originalPathInfo)) {
            $originalPathInfo = preg_replace('/^\//', '', $originalPathInfo);
            $originalPathInfo = explode('/', $originalPathInfo);
            if(in_array($originalPathInfo[0], array('attribute', 'byattribute'))) {
                $name = Mage::helper('byattribute')->urlClean($originalPathInfo[1]);
                $value = Mage::helper('byattribute')->urlClean($originalPathInfo[2]);

                if(isset($attribute_codes[$name])) {
                    $model->addAttribute(array(
                        'name' => $name,
                        'value' => $value,
                        'attribute' => $attribute_codes[$name],
                    ));
                }
            }

        } elseif(!empty($params)) {
            foreach($params as $name => $value) {
                if(array_key_exists($name, $attribute_codes)) {
                    $model->addAttribute(array(
                        'name' => $name,
                        'value' => $value,
                        'attribute' => $attribute_codes[$name],
                    ));
                }
            }
        }

        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();
        $update->addHandle('byattribute_index_index');
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;
        $this->renderLayout();
    }

    protected function _getAttributeCodes()
    {
        $cacheId = 'byattribute_attributecodes';
        if(Mage::app()->useCache('collections')) {
            if($cache = Mage::app()->loadCache($cacheId)) {
                $result = unserialize($cache);
                if(!empty($result)) return $result;
            }
        }

        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->addVisibleFilter();
        $attributeCodes = array();
        foreach($attributes as $attribute) {
            $attributeCodes[$attribute->getAttributeCode()] = $attribute;
        }

        // Save to cache
        if(Mage::app()->useCache('collections')) {
            Mage::app()->saveCache(serialize($attributeCodes), $cacheId, array('collections'), 86400);
        }

        return $attributeCodes;
    }
}
