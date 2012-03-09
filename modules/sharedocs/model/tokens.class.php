<?php
/*
 * Třída modelu souborů
 * 
 * CREATE  TABLE `{PREFIX}sharedoc_files_download_tokens` (
   `id_sharedoc_file_token` INT NOT NULL ,
   `id_sharedoc_file` INT NULL ,
   `token` VARCHAR(50) NOT NULL ,
   `token_date_add` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
   PRIMARY KEY (`id_sharedoc_file_token`) ,
   INDEX `index_file` (`id_sharedoc_file` ASC) )
   DEFAULT CHARACTER SET = utf8
   COLLATE = utf8_general_ci;
*/
class ShareDocs_Model_Tokens extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'sharedoc_files_download_tokens';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID                  = 'id_sharedoc_file_token';
   const COLUMN_ID_FILE             = 'id_sharedoc_file';
   const COLUMN_TOKEN               = 'token';
   const COLUMN_DATE_ADD            = 'token_date_add';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_shdocs_files_tokens');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_FILE, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_TOKEN, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_FILE, 'ShareDocs_Model_Files', ShareDocs_Model_Files::COLUMN_ID);
   }
}

?>