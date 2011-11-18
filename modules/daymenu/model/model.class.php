<?php
/*
 * Třída modelu detailem článku
 */
class DayMenu_Model extends Model_ORM {
   const DB_TABLE = 'daymenu';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_daymenu';
   const COLUMN_TEXT = 'daymenu_text';
   const COLUMN_TEXT_CLEAR = 'daymenu_text_clear';
   const COLUMN_TEXT_PANEL = 'daymenu_text_panel';
   const COLUMN_DATE = 'daymenu_date';
   const COLUMN_CONCEPT = 'daymenu_concept';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_daymenu');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_TEXT_PANEL, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CONCEPT, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);
   }
}

?>