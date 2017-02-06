<?php
class Voga_AdminReports_Block_Adminhtml_Products_Newproducts_Grid extends Voga_AdminReports_Block_Adminhtml_Base_Grid_Grid
{
    protected function _getGridCollection($filterData)
    {
        $collection = Mage::getModel('catalog/product')->getCollection();

        $collection
            ->addAttributeToFilter('type_id', 'simple')
            ->addFieldToFilter('created_at', array('gteq' => $filterData->getData('from')))
            ->addFieldToFilter('created_at', array('lteq' => $filterData->getData('to')))
            ->addAttributeToSelect(array('entity_id', 'sku', 'color', 'size', 'designer'))
            ;

        $collection->joinAttribute(
            'price',
            'catalog_product/price',
            'entity_id',
            null,
            'left',
            Mage::app()->getStore()->getId()
        );

        $genderId = Mage::getModel('eav/config')->getAttribute('catalog_product', 'gender')->getId();
        $productTypeId = Mage::getModel('eav/config')->getAttribute('catalog_product', 'product_type')->getId();
        $nameId = Mage::getModel('eav/config')->getAttribute('catalog_product', 'name')->getId();

        $collection
            ->getSelect()
            ->join(
                array('link' => 'catalog_product_super_link'),
                'link.product_id = e.entity_id',
                array('parent_id', 'parent_id')
            )
            ->join(
                array('configurable'      => $collection->getTable('catalog/product')),
                'configurable.entity_id = link.parent_id',
                array(
                    'configurable_id'     => 'configurable.entity_id',
                    'configurable_sku'    => 'configurable.sku',
                )
            )
            ->join(
                array('entity' => $collection->getTable('catalog_product_entity_varchar')),
                "entity.entity_id = link.parent_id AND entity.attribute_id = {$nameId}",
                array(
                    'configurable_name'   => 'value'
                )
            )
            ->join(
                array('stock' => $collection->getTable('cataloginventory_stock_item')),
                'stock.product_id = e.entity_id',
                array('qty' => 'stock.qty')
            )
            ->joinLeft(
                array('at_type' => $collection->getTable('catalog_product_entity_int')),
                "at_type.entity_id = configurable.entity_id AND at_type.attribute_id = {$productTypeId}",
                array('product_type' => 'at_type.value')
            )
            ->joinLeft(
                array('at_gender' => $collection->getTable('catalog_product_entity_varchar')),
                "at_gender.entity_id = configurable.entity_id AND at_gender.attribute_id = {$genderId}",
                array('gender' => 'at_gender.value')
            )
        ;

        return $collection;
    }

    protected function _afterLoadCollection()
    {
        $configurableProductsIds = implode(',', $this->getConfigurableIds($this->getCollection()));

        $connection  = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql         = "SELECT category_id, product_id FROM `catalog_category_product` WHERE `product_id` in ({$configurableProductsIds})";
        $rows        = $connection->fetchAll($sql);

        $productCategoriesIds = array();
        foreach ($rows as $row) {
            $productCategoriesIds[$row['product_id']][] = $row['category_id'];
        }

        foreach ($this->getCollection() as $item) {
            if (array_key_exists($item->getConfigurableId(), $productCategoriesIds)) {
                $item->setProductCategoriesIds( $productCategoriesIds[$item->getConfigurableId()] );
            }
        }

        return $this;
    }

    public function getConfigurableIds($collection)
    {
        $idsSelect = clone $collection->getSelect();
        $idsSelect->reset(Zend_Db_Select::COLUMNS);

        $idsSelect
            ->columns('entity_id', 'configurable')
            ->distinct(true)
        ;

        return $collection->getConnection()->fetchCol($idsSelect);
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('voga_adminreports');

        $this->addColumn('sku', array(
            'header'          => $helper->__('Sku'),
            'index'           => 'sku',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
        ));
        $this->addColumn('configurable_sku', array(
            'header'          => $helper->__('Parent Sku'),
            'index'           => 'configurable_sku',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addColumn('qty', array(
            'header'          => $helper->__('Qty Available'),
            'index'           => 'qty',
            'width'           => 10,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_products_newproducts_renderer_qty',
        ));

        $this->addColumn('configurable_name', array(
            'header'          => $helper->__('Name'),
            'index'           => 'configurable_name',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addColumn('gender', array(
            'header'          => $helper->__('Gender'),
            'index'           => 'gender',
            'width'           => 30,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_products_newproducts_renderer_gender',
        ));

        $this->addColumn('designer', array(
            'header'          => $helper->__('Brand'),
            'index'           => 'designer',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_products_newproducts_renderer_brand',
        ));

        $this->addColumn('product_type', array(
            'header'          => $helper->__('Product Type'),
            'index'           => 'product_type',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_products_newproducts_renderer_type',
        ));

        $this->addColumn('color', array(
            'header'          => $helper->__('Color'),
            'index'           => 'color',
            'width'           => 50,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_products_newproducts_renderer_color',
        ));

        $this->addColumn('size', array(
            'header'          => $helper->__('Size'),
            'index'           => 'size',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_products_newproducts_renderer_size',
        ));

        $this->addColumn('price', array(
            'header'          => $helper->__('Price'),
            'index'           => 'price',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'currency',
            'currency_code'   => Mage::app()->getStore()->getBaseCurrencyCode()
        ));

        $this->addColumn('created_at', array(
            'header'          => $helper->__('Created Date'),
            'index'           => 'created_at',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('product_categories_ids', array(
            'header'          => $helper->__('Categories'),
            'index'           => 'product_categories_ids',
            'width'           => 200,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_products_newproducts_renderer_categories',
        ));

        $this->addExportType('*/*/exportReportCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportReportExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
