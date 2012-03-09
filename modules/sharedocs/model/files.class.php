<?php
/*
 * Třída modelu souborů
 * 
 * CREATE  TABLE `{PREFIX}sharedoc_files` (
   `id_sharedoc_file` INT NOT NULL ,
   `id_sharedoc_directory` VARCHAR(45) NULL ,
   `file_name` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL ,
   `file_title` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL ,
   `locked` TINYINT NULL DEFAULT 0 ,
   `locked_by_id_user` INT NOT NULL DEFAULT 0 ,
   `file_date_add` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
   PRIMARY KEY (`id_sharedoc_file`) ,
   INDEX `index_directory` (`id_sharedoc_directory` ASC) ),
   INDEX `index_userlock` (`locked_by_id_user` ASC) )
   DEFAULT CHARACTER SET = utf8
   COLLATE = utf8_general_ci;
*/
class ShareDocs_Model_Files extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'sharedoc_files';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID                  = 'id_sharedoc_file';
   const COLUMN_ID_DIRECTORY        = 'id_sharedoc_directory';
   const COLUMN_NAME                = 'file_name';
   const COLUMN_TITLE               = 'file_title';
   const COLUMN_LOCKED              = 'locked';
   const COLUMN_LOCKED_ID_USER      = 'locked_by_id_user';
   const COLUMN_DATE_ADD            = 'file_date_add';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_shdocs_files');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_DIRECTORY, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TITLE, array('datatype' => 'varchar(500)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_LOCKED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_LOCKED_ID_USER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_DIRECTORY, 'ShareDocs_Model_Dirs', ShareDocs_Model_Dirs::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_LOCKED_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
      
//      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_PrivateUsers', Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE);
   }
}

?>