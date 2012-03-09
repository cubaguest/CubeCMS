<?php
/*
 * Třída modelu souborů
 * 
 * CREATE  TABLE `{PREFIX}sharedoc_directory_access_groups` (
   `id_sharedoc_access_group_conn` INT NOT NULL AUTO_INCREMENT ,
   `id_sharedoc_directory` INT NOT NULL ,
   `id_group` INT NOT NULL ,
   PRIMARY KEY (`id_sharedoc_access_group_conn`) ,
   INDEX `index_groups` (`id_sharedoc_directory` ASC, `id_group` ASC) );
*/
class ShareDocs_Model_GroupsAcc extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'sharedoc_directory_access_groups';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID                  = 'id_sharedoc_access_group_conn';
   const COLUMN_ID_DIR              = 'id_sharedoc_directory';
   const COLUMN_ID_GROUP            = 'id_group';
   const COLUMN_READ_ONLY           = 'group_read_only';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_shdocs_grpacc');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_DIR, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_READ_ONLY, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_DIR, 'ShareDocs_Model_Dirs', ShareDocs_Model_Dirs::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Model_Groups', Model_Groups::COLUMN_ID);
   }
}

?>