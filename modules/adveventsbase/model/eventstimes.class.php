<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_EventsTimes extends Model_ORM {
   const DB_TABLE = 'adv_events_times';
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID_EVENT      = 'id_event';
	const COLUMN_ID            = 'id_event_time';
	const COLUMN_DATE_BEGIN		= 'event_date_begin';
	const COLUMN_DATE_END		= 'event_date_end';
	const COLUMN_TIME_BEGIN		= 'event_time_begin';
	const COLUMN_TIME_END		= 'event_time_end';
	const COLUMN_NOTE		      = 'event_time_text';
   
//   const COLUMN_CHEERING_ONLY	= 'event_cheering_only';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_event_times');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_EVENT, array('datatype' => 'int', 'index' => true, 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_DATE_BEGIN, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_END, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_BEGIN, array('datatype' => 'time', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_END, array('datatype' => 'time', 'pdoparam' => PDO::PARAM_STR));

      $this->addForeignKey(self::COLUMN_ID_EVENT, 'AdvEventsBase_Model_Events', AdvEventsBase_Model_Events::COLUMN_ID);

      $this->setPk(self::COLUMN_ID);
   }

   public static function clearEventTimes($idEvent)
   {
      $m = new self();
      return $m->where(self::COLUMN_ID_EVENT." = :ide", array('ide' => $idEvent))->delete();
   }
}
