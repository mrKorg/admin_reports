<?php

abstract class Voga_AdminReports_Block_Adminhtml_Base_Grid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    abstract protected function _getGridCollection($filterData);

    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        if (!$filterData->getData('from') || !$filterData->getData('to')) {
            return parent::_prepareCollection();
        }

        $date = Mage::app()->getLocale()->date($filterData->getData('from'), 'y-MM-dd', null, false);
        $filterData->setData('from', $date->get('y-MM-dd HH:mm:ss'));

        $date = Mage::app()->getLocale()->date($filterData->getData('to'), 'y-MM-dd', null, false);
        $date->add('1', Zend_Date::DAY);
        $date->sub('1', Zend_Date::SECOND);
        $filterData->setData('to', $date->get('y-MM-dd HH:mm:ss'));

        $this->setCollection($this->_getGridCollection($filterData));

        return parent::_prepareCollection();
    }
}
