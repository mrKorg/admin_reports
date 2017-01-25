<?php
class Voga_AdminReports_Adminhtml_Sales_FirstorderController extends Mage_Adminhtml_Controller_Report_Abstract
{
    public function reportAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('First Order'));

        $this->loadLayout();
        $this->_setActiveMenu('report');

        $gridBlock = $this->getLayout()->getBlock('voga_adminreports.sales.firstorder')->getChild('grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->renderLayout();
    }

    public function exportReportCsvAction()
    {
        $fileName = 'sales_first_orders.csv';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_sales_firstorder_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportReportExcelAction()
    {
        $fileName = 'sales_first_orders.xml';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_sales_firstorder_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
