<?php
/*
 * Třída modelu lidí
*/
class Partners_Model_Groups extends Model_ORM_Ordered {
   const DB_TABLE = 'partners_groups';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_partners_group';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'partner_group_name';
   const COLUMN_ORDER = 'partner_group_order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_partners_grp');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 
         'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER, 'lang' => true));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category');
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Partners_Model', Partners_Model::COLUMN_ID_GROUP);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
   }
   
   public static function getGroups($idc)
   {
      $m = new self();
      return $m->where(self::COLUMN_ID_CATEGORY, $idc)->records();
   }
}

class Partners_Model_Groups_Record extends Model_ORM_Ordered_Record {
   
   /**
    * Vrátí partnery dané skupiny
    * @todo dodělat kešování - načíst všechny partnery z dané kateogrie a potm je rozřadit do statického pole
    */
   public function getPartners()
   {
      // asi kešování
      $m = new Partners_Model();
      return $m->where(Partners_Model::COLUMN_ID_GROUP, $this->getPK())->records();
   }
}