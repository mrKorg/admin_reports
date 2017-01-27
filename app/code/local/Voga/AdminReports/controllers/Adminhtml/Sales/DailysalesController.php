<?php
class Voga_AdminReports_Adminhtml_Sales_DailysalesController extends Mage_Adminhtml_Controller_Report_Abstract
{
    public function reportAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Daily Sales Report'));

        $this->loadLayout();
        $this->_setActiveMenu('report');

        $gridBlock = $this->getLayout()->getBlock('voga_adminreports.sales.dailysales')->getChild('grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->renderLayout();
    }

    public function exportReportCsvAction()
    {
        $fileName = 'daily_sales_report.csv';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_sales_dailysales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportReportExcelAction()
    {
        $fileName = 'daily_sales_report.xml';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_sales_dailysales_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
