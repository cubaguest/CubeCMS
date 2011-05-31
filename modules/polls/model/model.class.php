<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class Polls_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'polls';
   const DB_TABLE_VOTAR = 'polls_votar_users';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID         = 'id_poll';
   const COLUMN_ID_CAT     = 'id_category';
   const COLUMN_QUESTION   = 'question';
   const COLUMN_IS_MULTI   = 'is_multi';
   const COLUMN_ACTIVE     = 'active';
   const COLUMN_DATA       = 'data';
   const COLUMN_VOTES      = 'votes';
   const COLUMN_DATE       = 'date';

   const COL_V_ID_POLL     = 'id_poll';
   const COL_V_USER_IDEN   = 'user_identification';
   const COL_V_ID_USER     = 'id_user';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_polls');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_QUESTION, array('datatype' => 'varchar(500)', 'nn' => true,
//      $this->addColumn(self::COLUMN_QUESTION, array('datatype' => 'varchar(600)', 'nn' => true, 'lang' => true, 
         'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_IS_MULTI, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_DATA, array('datatype' => 'varbinary(1000)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_VOTES, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_DATE, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));


      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
//      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
//      $this->addForeignKey(self::COLUMN_ID_USER_LAST_EDIT, 'Model_Users', Model_Users::COLUMN_ID);
      
//      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_PrivateUsers', Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE);
   }
}

?>