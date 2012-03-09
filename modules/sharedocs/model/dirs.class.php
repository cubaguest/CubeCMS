<?php
/*
 * Třída modelu adresářů
 * 
 * CREATE  TABLE `{PREFIX}sharedoc_directories` (
   `id_sharedoc_directory` INT NOT NULL AUTO_INCREMENT ,
   `id_category` INT NOT NULL DEFAULT 0 ,
   `dir_name` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL ,
   `dir_title` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL ,
   `dir_date_add` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
   `dir_date_last_change` DATETIME NULL DEFAULT NULL ,
   PRIMARY KEY (`id_sharedoc_directory`) )
   DEFAULT CHARACTER SET = utf8
   COLLATE = utf8_general_ci;
*/
class ShareDocs_Model_Dirs extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'sharedoc_directories';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID                  = 'id_sharedoc_directory';
   const COLUMN_ID_CATEGORY         = 'id_category';
   const COLUMN_NAME                = 'dir_name';
   const COLUMN_TITLE               = 'dir_title';
   const COLUMN_DATE_ADD            = 'dir_date_add';
   const COLUMN_DATE_LAST_CHANGE    = 'dir_date_last_change';
   const COLUMN_IS_PUBLIC           = 'dir_is_public';
   const COLUMN_IS_PUBLIC_WRITE     = 'dir_is_public_write';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_shdocs_dirs');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TITLE, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_DATE_LAST_CHANGE, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_IS_PUBLIC, array('datatype' => 'tinyint(1)', 
         'nn' => true, 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_IS_PUBLIC_WRITE, array('datatype' => 'tinyint(1)', 
         'nn' => true, 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      
//      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_PrivateUsers', Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE);
   }
}

?>
