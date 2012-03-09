<?php
/*
 * Třída modelu revizí souborů
 * 
 * CREATE  TABLE `{PREFIX}sharedoc_files_revisions` (
   `id_sharedoc_file_rev` INT NOT NULL ,
   `id_sharedoc_file` INT NULL ,
   `rev_filename` VARCHAR(100) NULL ,
   `rev_filename_hash` VARCHAR(100) NULL ,
   `rev_note` VARCHAR(500) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL ,
   `rev_date_add` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
   PRIMARY KEY (`id_sharedoc_file_rev`) ,
   INDEX `index_directory` (`id_sharedoc_file` ASC) )
   DEFAULT CHARACTER SET = utf8
   COLLATE = utf8_general_ci;
*/
class ShareDocs_Model_Revs extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'sharedoc_files_revisions';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID                  = 'id_sharedoc_file_rev';
   const COLUMN_ID_FILE             = 'id_sharedoc_file';
   const COLUMN_ID_USER             = 'id_user';
   const COLUMN_FILENAME            = 'rev_filename';
   const COLUMN_ORIG_FILENAME       = 'rev_original_filename';
   const COLUMN_NOTE                = 'rev_note';
   const COLUMN_DATE_ADD            = 'rev_date_add';
   const COLUMN_NUMBER            = 'rev_number';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_shdocs_frevs');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_FILE, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NUMBER, array('datatype' => 'int', 'nn' => true, 'default' => 1));
      
      $this->addColumn(self::COLUMN_FILENAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORIG_FILENAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(500)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_FILE, 'ShareDocs_Model_Files', ShareDocs_Model_Files::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
   }
}

?>