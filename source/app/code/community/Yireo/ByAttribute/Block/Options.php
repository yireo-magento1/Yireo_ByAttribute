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
 * Class for block "byattribute_options"
 */
class Yireo_ByAttribute_Block_Options extends Mage_Core_Block_Template
{
    /**
     * The title of the block
     */
    private $title = null;

    /**
     * Variable deciding whether the show the product count in the PHTML-template
     */
    private $show_product_count = false;

    /**
     * Either an ID or code to identify the right attribute
     */
    private $attributeValue = null;

    /**
     * Category ID when using only attributes that are used in a specific category
     */
    private $categoryId = 0;

    /**
     * Values to skip
     */
    private $skip_options = array();

    /**
     * Whether to skip values with no products assigned to it
     */
    private $skip_empty = false;

    /**
     * Constructor method
     *
     */
    public function __construct()
    {
        // Constructor
        $rt = parent::__construct();

        // Load configuration details
        $this->skip_empty = (bool)Mage::helper('byattribute')->getConfigValue('sidebar_skip_empty');
        $this->show_product_count = (bool)Mage::helper('byattribute')->getConfigValue('sidebar_show_product_count');

        // Set the template
        $this->setTemplate('byattribute/options.phtml');
    }

    /**
     * Method to set the title
     */
    public function setTitle($title = null)
    {
        $this->title = $title;
    }

    /**
     * Method to set the attribute
     */
    public function setAttributeValue($attributeValue = null)
    {
        $this->attributeValue = $attributeValue;
    }

    /**
     * Method to set whether to skip empty values
     */
    public function skipEmpty($skip = null)
    {
        $this->skip_empty = (bool)$skip;
    }

    /**
     * Method to add a new option-value to skip in the listing
     */
    public function skipOptionValue($value= null)
    {
        $this->skip_options[] = $value;
    }

    /**
     * Method to set the category-ID
     */
    public function setCategoryId($categoryId = 0)
    {
        $this->categoryId = (int)$categoryId;
    }

    /**
     * Method to set a limit to the number of attributes shown
     */
    public function setLimit($limit = null)
    {
        if(is_numeric($limit)) {
            $this->limit = $limit;
        }
    }

    /**
     * Method to set the ordering of attribute-values
     */
    public function setOrdering($ordering = null)
    {
        $this->ordering = $ordering;
    }

    /**
     * Method to get the attribute-title
     */
    public function getAttributeTitle()
    {
        return $this->getAttribute()->getAttributeTitle();
    }

    /**
     * Method to get the category-name
     */
    public function getCategoryName()
    {
        return $this->getAttribute()->getCategoryName();
    }

    /**
     * Method to get the title
     */
    public function getTitle()
    {
        $title = $this->title;
        if(empty($title)) {
            $title = $this->getAttributeTitle();
            $categoryName = $this->getCategoryName();
            if(!empty($categoryName)) $title = $categoryName.' '.$title;
        }
        return $title;
    }

    /**
     * Method to get the ordering of attribute-values
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Method to get the all the attribute-values
     */
    public function getOptions($ordering = null)
    {
        if(empty($this->attribute)) {
            $this->getAttribute();
        }
        $options = $this->attribute->getOptions($ordering, $this->skip_empty, $this->show_product_count);

        if($this->limit > 0 && !empty($options)) {
            $options = array_slice($options, 0, $this->limit);
        }

        foreach($options as $index => $option) {
            if(in_array($option['label'], $this->skip_options)) {
                unset($options[$index]);
                continue;
            }

            if($this->skip_empty == true && isset($option['product_count']) && $option['product_count'] == 0) {
                unset($options[$index]);
                continue;
            }
        }

        return $options;
    }

    /**
     * Method to set or get whether to show the product-count
     */
    public function showProductCount($set = null)
    {
        if(is_numeric($set)) {
            $this->show_product_count = (bool)$set;
        }
        return (bool)$this->show_product_count;
    }

    /**
     * Method to get the attribute by its category and its attribute-ID or code
     *
     * @return object
     */
    public function getAttribute()
    {
        $this->attribute = Mage::getSingleton('byattribute/attribute')->load($this->attributeValue, $this->categoryId);
        return $this->attribute;
    }
}
