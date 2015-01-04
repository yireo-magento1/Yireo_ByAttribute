<?php
/**
 * Yireo ByAttribute
 *
 * @author Yireo
 * @package ByAttribute
 * @copyright Copyright 2015
 * @license Open Source License (OSL v3)
 * @link http://www.yireo.com
 */

/*
 * ByAttribute observer to various Magento events
 */
class Yireo_ByAttribute_Model_Observer extends Mage_Core_Model_Abstract
{
    /*
     * Method fired on the event <controller_action_predispatch>
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Yireo_ByAttribute_Model_Observer
     */
    public function controllerActionPredispatch($observer)
    {
        // Run the feed
        Mage::getModel('byattribute/feed')->updateIfAllowed();
    }
}
