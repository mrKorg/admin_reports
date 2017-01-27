<?php
class Voga_AdminReports_Block_Adminhtml_Sales_Dailysales_Grid extends Voga_AdminReports_Block_Adminhtml_Base_Grid_Grid
{
    protected function _getGridCollection($filterData)
    {

        $collection = Mage::getResourceModel('sales/order_collection');
        $collection
            ->addFieldToFilter('main_table.created_at', array('gteq' => $filterData->getData('from')))
            ->addFieldToFilter('main_table.created_at', array('lteq' => $filterData->getData('to')))
            ->addFieldToSelect(
                array(
                    'increment_id', 'status', 'total_qty_ordered', 'base_grand_total', 'base_discount_amount', 'base_shipping_amount',
                )
            );

        $firstOrdersSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select()
            ->from(
                $collection->getMainTable(), 
                array(
                    'order_email'        => 'LOWER(customer_email)', 
                    'first_order_number' => 'MIN(entity_id)',
                    )
                )
            ->group('order_email');

        $collection
            ->getSelect()
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
                    'first_order'       => "if(main_table.entity_id=customer_orders.first_order_number, 'Yes', 'No')"
                    )
                )
            ->joinLeft(
                array('order_items'     => 'sales_flat_order_item'),
                'order_items.order_id = main_table.entity_id AND order_items.parent_item_id = NULL',
                array(
                    'store_id'          => "main_table.store_id"
                    )
                )
            ;

        Mage::log((string)$collection->getSelect());

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

        $this->addColumn('increment_id', array(
            'header'          => $helper->__('Order ID'),
            'index'           => 'increment_id',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
        ));

        $this->addColumn('status', array(
            'header'          => $helper->__('Status'),
            'index'           => 'status',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_base_grid_renderer_status',
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

        $this->addColumn('total_qty_ordered', array(
            'header'          => $helper->__('Qty'),
            'index'           => 'total_qty_ordered',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'number'
        ));

        $this->addColumn('base_grand_total', array(
            'header'          => $helper->__('Grand Total'),
            'index'           => 'base_grand_total',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'currency',
            'currency_code'   => Mage::app()->getStore()->getBaseCurrencyCode()
        ));

        $this->addColumn('base_discount_amount', array(
            'header'          => $helper->__('Discount'),
            'index'           => 'base_discount_amount',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'number'
        ));

        $this->addColumn('base_shipping_amount', array(
            'header'          => $helper->__('Shipping'),
            'index'           => 'base_shipping_amount',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'type'            => 'number'
        ));

        $this->addColumn('store_id', array(
            'header'          => $helper->__('language'),
            'index'           => 'language',
            'width'           => 100,
            'filter'          => false,
            'sortable'        => false,
            'renderer'        => 'voga_adminreports/adminhtml_base_grid_renderer_language',
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
