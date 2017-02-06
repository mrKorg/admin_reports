<?php

class Voga_AdminReports_Block_Adminhtml_Sales_Firstorder_Grid extends Voga_AdminReports_Block_Adminhtml_Base_Grid_Grid
{
    protected function _getGridCollection($filterData)
    {
        $collection = Mage::getResourceModel('voga_adminreports/order_collection');
        $collection->addFieldToFilter('created_at', array('gteq' => $filterData->getData('from')))
            ->addFieldToFilter('created_at', array('lteq' => $filterData->getData('to')));

        $firstOrdersSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select()
            ->from($collection->getMainTable(), array('order_email' => 'LOWER(customer_email)', 'first_order_number' => 'MIN(entity_id)'))
            ->group('order_email');


        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('date(created_at) AS date, COUNT(*) AS number_of_orders, SUM(base_grand_total) as daily_total_revenue')
            ->join(
                array('customer_orders' => $firstOrdersSelect),
                'LOWER(main_table.customer_email) = customer_orders.order_email AND customer_orders.first_order_number = main_table.entity_id',
                array()
            )
            ->group('date(created_at)');

        return $collection;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('voga_adminreports');

        $this->addColumn('date', array(
            'header' => $helper->__('Date'),
            'index' => 'date',
            'width' => 100,
            'filter' => false,
            'sortable' => false,
            'period_type' => $this->getPeriodType(),
            'renderer' => 'adminhtml/report_sales_grid_column_renderer_date',
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('number_of_orders', array(
            'header' => $helper->__('Number of Orders From New Customers'),
            'index' => 'number_of_orders',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addColumn('daily_total_revenue', array(
            'header' => $helper->__('Daily Total Revenue'),
            'index' => 'daily_total_revenue',
            'filter' => false,
            'sortable' => false,
            'type' => 'currency',
            'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode()
        ));

        $this->addExportType('*/*/exportReportCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportReportExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
