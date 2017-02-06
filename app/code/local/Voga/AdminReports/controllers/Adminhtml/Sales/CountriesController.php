<?php
class Voga_AdminReports_Adminhtml_Sales_CountriesController extends Mage_Adminhtml_Controller_Report_Abstract
{
    public function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/voga_adminreports_sales_by_countries');
    }

    public function reportAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Sales By Country'));

        $this->loadLayout();
        $this->_setActiveMenu('report');

        $gridBlock = $this->getLayout()->getBlock('voga_adminreports.sales.countries')->getChild('grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->renderLayout();
    }

    public function exportReportCsvAction()
    {
        $fileName = 'sales_by_countries.csv';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_sales_countries_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportReportExcelAction()
    {
        $fileName = 'sales_by_countries.xml';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_sales_countries_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
