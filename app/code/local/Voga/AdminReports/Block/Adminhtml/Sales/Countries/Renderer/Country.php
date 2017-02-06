<?php

class Voga_AdminReports_Block_Adminhtml_Sales_Countries_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_countries = array();

    public function render(Varien_Object $row)
    {
        return $this->_getCountyName($row->getData('country_id'));
    }

    protected function _getCountyName($countryCode)
    {
        if (!array_key_exists($countryCode, $this->_countries)) {
            $this->_countries[$countryCode] = Mage::getModel('directory/country')->loadByCode($countryCode)->getName();
        }

        return $this->_countries[$countryCode];
    }
}
