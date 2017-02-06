<?php

class Voga_AdminReports_Block_Adminhtml_Products_Newproducts_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return number_format($row->getQty(), 0, '.', '');
    }
}
