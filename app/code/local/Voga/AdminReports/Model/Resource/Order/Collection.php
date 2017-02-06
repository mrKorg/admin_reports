<?php

class Voga_AdminReports_Model_Resource_Order_Collection extends Mage_Sales_Model_Resource_Order_Collection
{
    public function getSize() {

        if ( is_null( $this->_totalRecords ) ) {
            $sql = $this->getSelectCountSql();
            // fetch all rows since it's a joined table and run a count against it.
            $this->_totalRecords = count( $this->getConnection()->fetchall( $sql, $this->_bindParams ) );
        }

        return intval( $this->_totalRecords );
    }
}
