<?php

class Voga_AdminReports_Block_Adminhtml_Products_Newproducts_Renderer_Categories extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $_allProductCategories = array();
        $helper = Mage::helper('voga_adminreports');
        $categoriesTree = $helper->getCategoriesTree();

        foreach ($row->getProductCategoriesIds() as $category) {
            if (key_exists($category, $categoriesTree)) {
                $_allProductCategories[] = $categoriesTree[$category];
            }
        }
        return implode(', ', $_allProductCategories);
    }
}
