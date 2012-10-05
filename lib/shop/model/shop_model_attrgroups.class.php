<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_AttrGroups extends Model_ORM {
   const DB_TABLE = 'shop_attributes_groups';

   const COLUMN_ID = 'id_attribute_group';
   const COLUMN_NAME = 'atgroup_name_cs';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_attr_grps');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR /*, 'lang' => true*/));
      
      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_Attributes', Shop_Model_Attributes::COLUMN_ID_GROUP);
   }
}

?>