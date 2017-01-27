<?php

class Voga_AdminReports_Model_Resource_Order_Collection extends Mage_Sales_Model_Resource_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('voga_adminreports/order');
    }

    public function getSize() {

        if ( is_null( $this->_totalRecords ) ) {
            $sql = $this->getSelectCountSql();
            // fetch all rows since it's a joined table and run a count against it.
            $this->_totalRecords = count( $this->getConnection()->fetchall( $sql, $this->_bindParams ) );
        }

        return intval( $this->_totalRecords );
    }
}
