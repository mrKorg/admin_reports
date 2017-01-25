<?php
class Voga_AdminReports_Block_Adminhtml_Products_Newproducts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $helper = Mage::helper('voga_adminreports');
        $this->_blockGroup = 'voga_adminreports';
        $this->_controller = 'adminhtml_products_newproducts';
        $this->_headerText = $helper->__('New products');
        parent::__construct();
        $this->setTemplate('report/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label' => $helper->__('Show Report'),
            'onclick' => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/report', array('_current' => true));
    }
}