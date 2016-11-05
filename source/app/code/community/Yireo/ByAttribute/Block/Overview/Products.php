<?php
/**
 * Yireo ByAttribute for Magento 
 *
 * @package     Yireo_ByAttribute
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * Class for block "byattribute_overview_products"
 */
class Yireo_ByAttribute_Block_Overview_Products extends Mage_Catalog_Block_Product_List
{
    /**
     * Get the products
     *
     * @return array
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {

            $collection = Mage::getResourceModel('catalog/product_collection');
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);

            $collection->addStoreFilter();

            // Fetch all the attributes
            $attributes = Mage::getSingleton('byattribute/overview')->getAttributes();
            if(!empty($attributes)) {
                foreach($attributes as $attribute) {
                    //$collection->addAttributeToFilter($attribute['name'], $attribute['value']);
                    $resource = Mage::getSingleton('core/resource');
                    $attributeModel = $attribute['attribute'];
                    $connection = $resource->getConnection('core_read');
                    $tableAlias = $attribute['name'].'_idx';
                    $conditions = array(
                        "{$tableAlias}.entity_id = e.entity_id",
                        $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attributeModel->getAttributeId()),
                        $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
                        $connection->quoteInto("{$tableAlias}.value = ?", $attribute['value'])
                    );

                    $mainTable = $resource->getTableName('catalog_product_index_eav');
                    $collection->getSelect()->join(
                        array($tableAlias => $mainTable),
                        implode(' AND ', $conditions),
                        array()
                    );
                }
            }

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
