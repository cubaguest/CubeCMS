<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_OrderStatus extends Model_ORM {
   const DB_TABLE = 'shop_order_status';

   const COLUMN_ID = 'id_order_status';
   const COLUMN_ID_ORDER = 'id_order';
   
   const COLUMN_NAME = 'order_status_name';
   const COLUMN_NOTE = 'order_status_note';
   const COLUMN_TIME_ADD = 'order_status_time_add';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ord_stat');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_ORDER, array('datatype' => 'smallint', 'nn' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(500)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_ORDER, 'Shop_Model_Orders', Shop_Model_Orders::COLUMN_ID);
   }
}
