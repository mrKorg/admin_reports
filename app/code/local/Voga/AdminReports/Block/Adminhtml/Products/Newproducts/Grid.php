<?php
class Voga_AdminReports_Block_Adminhtml_Products_Newproducts_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        if (!$filterData->getData('from') || !$filterData->getData('to')) {
            return parent::_prepareCollection();
        }

        $date = Mage::app()->getLocale()->date($filterData->getData('from'), 'y-MM-dd', null, false);
        $filterData->setData('from', $date->get('y-MM-dd HH:mm:ss'));

        $date = Mage::app()->getLocale()->date($filterData->getData('to'), 'y-MM-dd', null, false);
        $date->add('1', Zend_Date::DAY);
        $date->sub('1', Zend_Date::SECOND);
        $filterData->setData('to', $date->get('y-MM-dd HH:mm:ss'));

        $this->setCollection($this->_getGridCollection($filterData));

        return parent::_prepareCollection();
    }

    protected function _getGridCollection($filterData)
    {
        $collection = Mage::getModel('catalog/product')->getCollection();

        $collection
            ->addAttributeToFilter('type_id', 'simple')
            ->addFieldToFilter('created_at', array('gteq' => $filterData->getData('from')))
            ->addFieldToFilter('created_at', array('lteq' => $filterData->getData('to')))
            ->addAttributeToSelect(array('entity_id', 'sku', 'configurable_sku', 'name', 'price', 'color', 'size', 'designer'))
            ;

        $genderId = Mage::getModel('eav/config')->getAttribute('catalog_product', 'gender')->getId();
        $productTypeId = Mage::getModel('eav/config')->getAttribute('catalog_product', 'product_type')->getId();

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
                array('stock' => $collection->getTable('cataloginventory_stock_item')),
                'stock.product_id = e.entity_id',
                array('qty' => 'stock.qty')
            )
            ->joinLeft(
                array('at_type' => $collection->getTable('catalog_product_entity_int')),
                "at_type.entity_id = configurable.entity_id AND at_type.attribute_id = {$productTypeId}",
                array('type_id' => 'at_type.entity_id', 'product_type' => 'at_type.value')
            )
            ->joinLeft(
                array('at_gender' => $collection->getTable('catalog_product_entity_varchar')),
                "at_gender.entity_id = configurable.entity_id AND at_gender.attribute_id = {$genderId}",
                array('gender_id' => 'at_gender.entity_id', 'gender' => 'at_gender.value')
            )
        ;

        return $collection;
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
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Qty',
        ));

        $this->addColumn('name', array(
            'header'          => $helper->__('Name'),
            'index'           => 'name',
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
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Gender',
        ));

        $this->addColumn('designer', array(
            'header'          => $helper->__('Brand'),
            'index'           => 'designer',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Brand',
        ));

        $this->addColumn('product_type', array(
            'header'          => $helper->__('Product Type'),
            'index'           => 'product_type',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Type',
        ));

        $this->addColumn('color', array(
            'header'          => $helper->__('Color'),
            'index'           => 'color',
            'width'           => 50,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Color',
        ));

        $this->addColumn('size', array(
            'header'          => $helper->__('Size'),
            'index'           => 'size',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Size',
        ));

        $this->addColumn('price', array(
            'header'          => $helper->__('Price'),
            'index'           => 'price',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'currency',
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Price',
        ));

        $this->addColumn('created_at', array(
            'header'          => $helper->__('Created Date'),
            'index'           => 'created_at',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'adminhtml/report_sales_grid_column_renderer_date',
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('configurable_id', array(
            'header'          => $helper->__('Categories'),
            'index'           => 'configurable_id',
            'width'           => 200,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Categories',
        ));

        $this->addExportType('*/*/exportReportCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportReportExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
