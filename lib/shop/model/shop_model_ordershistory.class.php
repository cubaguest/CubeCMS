<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_OrdersHistory extends Model_ORM {
   const DB_TABLE = 'shop_order_history';

   const COLUMN_ID = 'id_order_history';
   const COLUMN_ID_STATE = 'id_order_state';
   const COLUMN_ID_ORDER = 'id_order';
   
   const COLUMN_NOTE = 'order_state_history_note';
   const COLUMN_TIME_ADD = 'order_state_time_add';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ord_history');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_STATE, array('datatype' => 'int', 'nn' => true, 'index' => array(self::COLUMN_ID_ORDER, self::COLUMN_ID_STATE)));
      $this->addColumn(self::COLUMN_ID_ORDER, array('datatype' => 'int', 'nn' => true));
      
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(500)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_ORDER, 'Shop_Model_Orders', Shop_Model_Orders::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_STATE, 'Shop_Model_OrdersStates', Shop_Model_OrdersStates::COLUMN_ID);
   }
}
