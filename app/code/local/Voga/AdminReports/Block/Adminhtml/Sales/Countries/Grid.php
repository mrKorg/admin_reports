<?php

class Voga_AdminReports_Block_Adminhtml_Sales_Countries_Grid extends Voga_AdminReports_Block_Adminhtml_Base_Grid_Grid
{
    protected function _getGridCollection($filterData)
    {
        $collection = Mage::getResourceModel('voga_adminreports/order_collection');
        $collection->addFieldToFilter('created_at', array('gteq' => $filterData->getData('from')))
            ->addFieldToFilter('created_at', array('lteq' => $filterData->getData('to')));

        $customersSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select()
            ->from(Mage::getSingleton('core/resource')->getTableName('customer/entity'), array('customer_created_date' => 'DATE_FORMAT(created_at, \'%Y-%m-%d\')', 'total_customers' => 'COUNT(*)'))
            ->where('created_at BETWEEN \'' . $filterData->getData('from') .'\' AND \'' . $filterData->getData('to') . '\'')
            ->group('customer_created_date');


        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('DATE_FORMAT(main_table.created_at, \'%Y-%m-%d\') AS date, SUM(main_table.base_grand_total) AS country_base_grand_total')
            ->join(
                array('address' => 'sales_flat_order_address'),
                'address.parent_id = main_table.entity_id AND address.address_type=\'billing\'',
                array('country_id')
            )
            ->joinLeft(
                array('customers' => $customersSelect),
                'DATE_FORMAT(main_table.created_at, \'%Y-%m-%d\') = customers.customer_created_date',
                array('total_new_customers' => 'IF(total_customers IS NOT NULL, total_customers, 0)')
            )
            ->group(array('DATE_FORMAT(main_table.created_at, \'%Y-%m-%d\')', 'address.country_id'))
            ->order(array('date', 'address.country_id'));

        Mage::log((string)$collection->getSelect());
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
        $this->addColumn('country_id', array(
            'header' => $helper->__('Country'),
            'index' => 'country_id',
            'filter' => false,
            'sortable' => false,
            'renderer' => 'voga_adminreports/adminhtml_base_grid_renderer_country',
        ));

        $this->addColumn('country_base_grand_total', array(
            'header' => $helper->__('Base Grand Total'),
            'index' => 'country_base_grand_total',
            'type' => 'currency',
            'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
            'filter' => false,
            'sortable' => false,
        ));
        $this->addColumn('total_new_customers', array(
            'header' => $helper->__('Number of New Customers (Total by Date)'),
            'index' => 'total_new_customers',
            'filter' => false,
            'sortable' => false,
        ));

        $this->addExportType('*/*/exportReportCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportReportExcel', $helper->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
