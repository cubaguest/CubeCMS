<?php
/*
 * Třída modelu detailem článku
*/
class Journals_Model extends Model_ORM {
   const DB_TABLE = 'journals';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_journal';
   const COLUMN_NUMBER = 'journal_number';
   const COLUMN_YEAR = 'journal_year';
   const COLUMN_TEXT = 'journal_text';
   const COLUMN_FILE = 'journal_file';
   const COLUMN_VIEWED = 'journal_viewed';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_jounals');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NUMBER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_YEAR, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_VIEWED, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0 ));
      
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 
         'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER, 'default' => null));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      
      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Journals_Model_Labels', Journals_Model_Labels::COLUMN_ID_JOURNAL);
   }
}

?>