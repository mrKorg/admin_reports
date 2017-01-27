<?php

class Voga_AdminReports_Block_Adminhtml_Base_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $status = $row->getStatus();
        if ($status == Mage_Sales_Model_Order::STATE_COMPLETE) {
            $status = 'Completed';
        } elseif ($status == Mage_Sales_Model_Order::STATE_CANCELED || $status == Mage_Sales_Model_Order::STATE_CLOSED) {
            $status = 'Canceled';
        } else {
            $status = 'On going';
        }
        return $status;
    }
}
