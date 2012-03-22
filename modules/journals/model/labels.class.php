<?php
/*
 * Třída modelu detailem článku
*/
class Journals_Model_Labels extends Model_ORM {
   const DB_TABLE = 'journals_labels';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_label';
   const COLUMN_ID_JOURNAL = 'id_journal';
   const COLUMN_LABEL = 'journal_label';
   const COLUMN_PAGE = 'journal_page';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_journ_lab');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_JOURNAL, array('datatype' => 'smallint', 'nn' => true));
      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PAGE, array('datatype' => 'smallint', 'nn' => true));
      
      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_JOURNAL, 'Journal_Model');
   }
}

?>