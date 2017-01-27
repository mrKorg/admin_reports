<?php

class Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Language extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $storeId = $row->getStoreId();
        $store = Mage::app()->getStore($storeId);
        return $store->getName();
    }
}
