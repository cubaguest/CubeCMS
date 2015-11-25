<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_EventsSources extends Model_ORM {
   const DB_TABLE = 'adv_events_sources';
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID      = 'id_event_source';
	const COLUMN_NAME   = 'event_source_name';
	const COLUMN_CLASS   = 'event_source_class';
	const COLUMN_PARAMS	= 'event_source_params';
	const COLUMN_LAST_IMPORT	= 'event_last_import';
	const COLUMN_ENABLED	= 'event_source_enabled';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_event_sources');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_CLASS, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PARAMS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LAST_IMPORT, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ENABLED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));

      $this->setPk(self::COLUMN_ID);
   }
}
