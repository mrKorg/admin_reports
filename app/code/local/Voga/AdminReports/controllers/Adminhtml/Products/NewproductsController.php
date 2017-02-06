<?php
class Voga_AdminReports_Adminhtml_Products_NewproductsController extends Mage_Adminhtml_Controller_Report_Abstract
{
    public function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/products/voga_adminreports_sales_new_products');
    }

    public function reportAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Products'))->_title($this->__('New Products'));

        $this->loadLayout();
        $this->_setActiveMenu('report');

        $gridBlock = $this->getLayout()->getBlock('voga_adminreports.products.newproducts')->getChild('grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array($gridBlock, $filterFormBlock));

        $this->renderLayout();
    }

    public function exportReportCsvAction()
    {
        $fileName = 'new_orders.csv';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_products_newproducts_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportReportExcelAction()
    {
        $fileName = 'new_orders.xml';
        $grid = $this->getLayout()->createBlock('voga_adminreports/adminhtml_products_newproducts_grid');
        $this->_initReportAction($grid);
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
