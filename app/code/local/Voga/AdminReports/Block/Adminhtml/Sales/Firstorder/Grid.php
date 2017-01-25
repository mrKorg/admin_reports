<?php
class Voga_AdminReports_Block_Adminhtml_Sales_Firstorder_Grid extends Mage_Adminhtml_Block_Widget_Grid
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

        $collection = Mage::getResourceModel('sales/order_collection');
        $collection
            ->addFieldToFilter('main_table.created_at', array('gteq' => $filterData->getData('from')))
            ->addFieldToFilter('main_table.created_at', array('lteq' => $filterData->getData('to')));

        $firstOrdersSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select()
            ->from(
                $collection->getMainTable(), 
                array(
                    'order_email'        => 'LOWER(customer_email)', 
                    'first_order_number' => 'MIN(entity_id)',
                    )
                )
            ->group('order_email');

        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('date(main_table.created_at) AS date')

            ->join(
                array('address'         => 'sales_flat_order_address'),
                'address.parent_id = main_table.entity_id AND address.address_type=\'billing\'',
                array('country_id', 'city')
            )
            ->joinLeft(
                array('customer_orders' => $firstOrdersSelect),
                'LOWER(main_table.customer_email) = customer_orders.order_email AND customer_orders.first_order_number = main_table.entity_id',
                array(
                    'order_id'          => 'main_table.increment_id', 
                    'order_status'      => 'main_table.status', 
                    'order_qty'         => 'main_table.total_qty_ordered',
                    'order_grand_total' => 'main_table.base_grand_total',
                    'order_discount'    => 'main_table.base_discount_amount',
                    'order_shipping'    => 'main_table.base_shipping_amount',
                    'first_order'       => "if(main_table.entity_id=customer_orders.first_order_number, 'Yes', 'No')"
                    )
                )
            ->joinLeft(
                array('order_items'     => 'sales_flat_order_item'),
                'order_items.order_id = main_table.entity_id AND order_items.parent_item_id = NULL',
                array(
                    'language'          => "if(main_table.store_id= 1, 'EN', 'AR')"
                    )
                )
            ->group('date(created_at)');

        return $collection;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('voga_adminreports');

        $this->addColumn('date', array(
            'header'          => $helper->__('Date'),
            'index'           => 'date',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'adminhtml/report_sales_grid_column_renderer_date',
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('order_id', array(
            'header'          => $helper->__('Order ID'),
            'index'           => 'order_id',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addColumn('order_status', array(
            'header'          => $helper->__('Status'),
            'index'           => 'order_status',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addColumn('country_id', array(
            'header'          => $helper->__('Country'),
            'index'           => 'country_id',
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_base_grid_renderer_country',
        ));

        $this->addColumn('city', array(
            'header'          => $helper->__('City'),
            'index'           => 'city',
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addColumn('order_qty', array(
            'header'          => $helper->__('Qty'),
            'index'           => 'order_qty',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'number'
        ));

        $this->addColumn('order_grand_total', array(
            'header'          => $helper->__('Grand Total'),
            'index'           => 'order_grand_total',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'number'
        ));

        $this->addColumn('order_discount', array(
            'header'          => $helper->__('Discount'),
            'index'           => 'order_discount',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'number'
        ));

        $this->addColumn('order_shipping', array(
            'header'          => $helper->__('Shipping'),
            'index'           => 'order_shipping',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'number'
        ));

        $this->addColumn('language', array(
            'header'          => $helper->__('language'),
            'index'           => 'language',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addColumn('first_order', array(
            'header'          => $helper->__('First order'),
            'index'           => 'first_order',
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addExportType('*/*/exportReportCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportReportExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }
}