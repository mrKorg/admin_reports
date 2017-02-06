<?php

class Voga_AdminReports_Block_Adminhtml_Products_Newproducts_Renderer_Gender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    const UNISEX = 'Unisex';
    protected $_attributeCode = 'gender';
    protected $_attributeOptions = array();

    public function render(Varien_Object $row)
    {
        $attributeIds = explode(',', $row->getData($this->_attributeCode));
        $gender = array();
        foreach ($attributeIds as $attributeId) {
            if (!key_exists($attributeId, $this->_attributeOptions)) {
                $helper = Mage::helper('voga_adminreports');
                $this->_attributeOptions = $helper->getProductAttributeOptionsByCode($this->_attributeCode);
            }
            if (key_exists($attributeId, $this->_attributeOptions)) {
                $gender[] =  $this->_attributeOptions[$attributeId];
            }
        }

        switch (count($gender)) {
            case (1):
                return $gender[0];
            case (2):
                return $this::UNISEX;
        }

        return ;
    }

}
