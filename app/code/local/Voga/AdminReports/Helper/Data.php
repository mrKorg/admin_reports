<?php
class Voga_AdminReports_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_categories = array();

    public function getProductAttributeByCode($code)
    {
        $attributes = array();
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $code);
        foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
            $attributes[$instance['value']] = $instance['label'];
        }
        return $attributes;
    }

    public function getCategoriesTree()
    {
        if (!count($this->_categories)) {
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
            ->addAttributeToSelect (array('name', 'entity_id'))
            ->addAttributeToFilter ('url_key', array('sale', 'new-in'))
        ;
        $expelledCategoriesIds = array();
        foreach ($excludedCategories as $category) {
            $expelledCategoriesIds[] = $category->getEntityId();
        }
        $expelledCategoriesIds = implode(',',$expelledCategoriesIds);

        $collection
            ->addAttributeToSelect(array('entity_id', 'parent_id', 'level', 'name', 'price', 'color', 'size', 'designer'))
        ;
        $collection
            ->getSelect()
            ->join(
                array('name' => 'catalog_category_entity_varchar'),
                'name.entity_id = e.entity_id and name.attribute_id = 41 and name.store_id = 0',
                array('value'=>'name.value')
            )
            ->join(
                array('enabled' => 'catalog_category_entity_int'),
                'enabled.entity_id = e.entity_id and enabled.attribute_id = 42 and name.store_id = 0',
                array('value'=>'name.value')
            )
            ->where(
                "e.level >= 2 and e.entity_id not in ({$expelledCategoriesIds}) and enabled.value = 1"
            )
            ->order('level','DESC')
        ;
        return $collection;
    }
}
