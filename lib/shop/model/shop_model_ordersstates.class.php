<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_OrdersStates extends Model_ORM {
   const DB_TABLE = 'shop_order_states';

   const COLUMN_ID = 'id_order_state';
   const COLUMN_ID_TEMPLATE = 'id_order_state_mail_template';
   
   const COLUMN_NAME = 'order_state_name';
   const COLUMN_NOTE = 'order_state_note';
   const COLUMN_COLOR = 'order_state_color';
   const COLUMN_COMPLETE = 'order_state_complete';
   const COLUMN_DELETED = 'order_state_deleted';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ord_stat');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_TEMPLATE, array('datatype' => 'int', 'nn' => true));
      $this->addColumn(self::COLUMN_COMPLETE, array('datatype' => 'tinyint(1)', 'nn' => true, 'default' => 0, 'pdoparam' => PDO::PARAM_BOOL));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'nn' => true, 'default' => 0, 'pdoparam' => PDO::PARAM_BOOL));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(100)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_COLOR, array('datatype' => 'varchar(9)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_TEMPLATE, 'Templates_Model', Templates_Model::COLUMN_ID);
   }
   
   public static function getActiveStates()
   {
      $m = new self();
      
      return $m->where(self::COLUMN_DELETED, 0)->order(self::COLUMN_NAME)->records();
   }
}
