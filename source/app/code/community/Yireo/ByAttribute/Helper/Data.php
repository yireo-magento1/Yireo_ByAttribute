<?php
/**
 * Yireo ByAttribute for Magento 
 *
 * @package     Yireo_ByAttribute
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * ByAttribute helper
 */
class Yireo_ByAttribute_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigValue($key = null, $default_value = null)
    {
        $value = Mage::getStoreConfig('catalog/byattribute/'.$key);
        if(empty($value)) $value = $default_value;
        return $value;
    }

    public function rewriteValue($value)
    {
        $value = trim(strtolower($value));
        $value = preg_replace('/([\ \'\"\_\=]+)/', '-', $value);
        $value = preg_replace('/([\-]{2,})/', '-', $value);
        return $value;
    }

    public function urlClean($value)
    {
        $value = preg_replace('/([^a-zA-Z0-9\-\_\.]+)/', '', $value);
        return strtolower($value);
    }
}
