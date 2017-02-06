<?php
class Voga_AdminReports_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_categories = null;

    public function getProductAttributeOptionsByCode($code)
    {
        $attributesOptions = array();
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $code);
        foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
            $attributesOptions[$option['value']] = $option['label'];
        }
        return $attributesOptions;
    }

    public function getCategoriesTree()
    {
        if (is_null($this->_categories)) {
            $this->_categories = array();
            $collection = $this->_getCategoriesCollection();
            foreach ($collection as $item) {
                if ($item['level'] == 2) {
                    $this->_categories[$item['entity_id']] = $item['name'];
                } else {
                    if (array_key_exists($item['parent_id'], $this->_categories)) {
                        $this->_categories[$item['entity_id']] = $this->_categories[$item['parent_id']] . ' > ' . $item['name'];
                    }
                }
            }
        }
        return $this->_categories;
    }

    protected function _getCategoriesCollection()
    {
        $collection = Mage::getModel('catalog/category')->getCollection();

        // Exclude categories 'sale' and 'new-in'
        $excludedCategories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect(array('name', 'entity_id'))
            ->addAttributeToFilter('url_key', array('sale', 'new-in'))
        ;
        $expelledCategoriesIds = implode(',', $excludedCategories->getAllIds());

        $collection
            ->addAttributeToSelect(array('parent_id', 'level', 'name', 'is_active'))
            ->addAttributeToFilter('level', array('gteq' => 2))
            ->addAttributeToFilter('entity_id', array('nin' => $expelledCategoriesIds))
            ->addAttributeToFilter('is_active', 1)
            ->getSelect()
            ->order('level','DESC')
        ;

        return $collection;
    }
}
