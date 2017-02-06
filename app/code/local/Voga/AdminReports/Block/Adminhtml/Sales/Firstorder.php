<?php

class Voga_AdminReports_Block_Adminhtml_Sales_Firstorder extends Voga_AdminReports_Block_Adminhtml_Base_Report
{
    protected $_controller = 'adminhtml_sales_firstorder';
    protected $_reportHeaderText = 'First Orders Report';

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/report', array('_current' => true));
    }
}
