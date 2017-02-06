<?php

class Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Render extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_attributeCode = '';

    protected $_attributeOptions = array();

    public function render(Varien_Object $row)
    {
        $attributeId = (int)$row->getData($this->_attributeCode);
        if (!key_exists($attributeId, $this->_attributeOptions)) {
            $helper = Mage::helper('voga_adminreports');
            $this->_attributeOptions = $helper->getProductAttributeOptionsByCode($this->_attributeCode);
        }
        if (key_exists($attributeId, $this->_attributeOptions)) {
            return $this->_attributeOptions[$attributeId];
        }
        return ;
    }
}
