<?php

class Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Color extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    const ATTRIBUTE_CODE = 'color';

    protected $_attributes = array();

    public function render(Varien_Object $row)
    {
        $attributeId = $row->getData($this::ATTRIBUTE_CODE);
        if (!key_exists($attributeId, $this->_attributes)) {
            $helper = Mage::helper('voga_adminreports');
            $this->_attributes = $helper->getProductAttributeByCode($this::ATTRIBUTE_CODE);
        }
        if (key_exists($attributeId, $this->_attributes)) {
            return $this->_attributes[$attributeId];
        }
        return ;
    }
}
