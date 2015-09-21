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
 * Class for block "byattribute_overview"
 */
class Yireo_ByAttribute_Block_Overview extends Mage_Core_Block_Template
{
    protected $title = null;
    protected $title_format = 'Products with $ATTRIBUTE $VALUE';

    /**
     * Constructor method
     *
     * @return mixed
     */
    public function __construct()
    {
        // Constructor
        $rt = parent::__construct();

        // Implement block-caching
        $path = $this->getRequest()->getOriginalPathInfo();
        $params = $this->getRequest()->getParams();
        $cacheKey = 'byattribute_overview_'.md5($path.var_export($params, true));
        $this->addData(array(
            'cache_lifetime' => 1800,
            'cache_tags' => array('block_html'),
            'cache_key' => $cacheKey,
        ));

        // Load configuration details
        $this->title_format = Mage::helper('byattribute')->getConfigValue('overview_title_format');
    
        return $rt;
    }

    protected function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', array(
            'label' => Mage::helper('cms')->__('Home'),
            'title' => Mage::helper('cms')->__('Go to Home Page'), 
            'link' => Mage::getBaseUrl()
        ));
        $breadcrumbs->addCrumb('byattribute_page', array(
            'label' => $this->getTitle(), 
            'title' => $this->getTitle()
        ));

        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $title = strip_tags($this->getTitle());
            $head->setTitle($title);
            $head->setKeywords($this->getMetaKeywords());
            $head->setDescription($title);
        }

        return parent::_prepareLayout();
    }

    /**
     * Get the page title and construct it if needed
     */
    public function setTitleFormat($title_format)
    {
        $this->title_format = $title_format;
    }

    /**
     * Get the page title and construct it if needed
     */
    public function getTitle()
    {
        if(empty($this->title)) {

            $attributes = Mage::getSingleton('byattribute/overview')->getAttributes();
            if(isset($attributes[0])) {
            
                $attribute = $attributes[0]['attribute'];
                $current_label = $attribute->getFrontendLabel();
                $current_value = null;

                $options = $attribute->getFrontend()->getSelectOptions();
                foreach($options as $option) {
                    if($option['value'] == $attributes[0]['value']) {
                        $current_value = $option['label'];
                        break;
                    }
                }

                $this->title = sprintf($this->__($this->title_format), strtolower($current_label), $current_value); // Legacy method
                $this->title = str_replace('$ATTRIBUTE', $current_label, $this->title);
                $this->title = str_replace('$VALUE', $current_value, $this->title);

                $headBlock = $this->getLayout()->getBlock('head');
                if ($headBlock) {
                    $headBlock->setTitle($this->title);
                }
            }
        }

        return $this->title;
    }

    public function getMetaKeywords()
    {
        $keywords = array();
        $attributes = Mage::getSingleton('byattribute/overview')->getAttributes();
        foreach($attributes as $attribute) {
            $attribute = $attributes[0]['attribute'];
            $keywords[] = $attribute->getFrontendLabel();
            $options = $attribute->getFrontend()->getSelectOptions();
            foreach($options as $option) {
                if($option['value'] == $attributes[0]['value']) {
                    $keywords[] = $option['label'];
                    break;
                }
            }
        }
        return implode(', ', $keywords);
    }
}
