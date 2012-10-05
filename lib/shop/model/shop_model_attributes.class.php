<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_Attributes extends Model_ORM {
   const DB_TABLE = 'shop_attributes';

   const COLUMN_ID = 'id_attribute';
   const COLUMN_ID_GROUP = 'id_attribute_group';
   const COLUMN_NAME = 'attribute_name_cs';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_attr_grps');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'smallint', 'nn' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR /*, 'lang' => true*/));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Shop_Model_AttrGroups', Shop_Model_AttrGroups::COLUMN_ID);
   }
}

?>