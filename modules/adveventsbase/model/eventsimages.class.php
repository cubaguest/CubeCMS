<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_EventsImages extends Model_ORM_Ordered {
   const DB_TABLE = 'adv_events_images';
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID_EVENT      = 'id_event';
	const COLUMN_ID            = 'id_event_image';
	const COLUMN_FILE          = 'event_image_file';
	const COLUMN_NAME		      = 'event_image_name';
	const COLUMN_ORDER		   = 'event_image_order';
	const COLUMN_IS_TITLE		= 'event_image_is_title';
   
//   const COLUMN_CHEERING_ONLY	= 'event_cheering_only';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_event_images');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_EVENT, array('datatype' => 'int', 'index' => true, 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_IS_TITLE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));

      $this->addForeignKey(self::COLUMN_ID_EVENT, 'AdvEventsBase_Model_Events', AdvEventsBase_Model_Events::COLUMN_ID);

      $this->setPk(self::COLUMN_ID);
      $this->setLimitedColumns(array(self::COLUMN_ID_EVENT));
      $this->setOrderColumn(self::COLUMN_ORDER);
   }

   public static function clearEventImages($idEvent)
   {
//      $m = new self();
//      return $m
//         ->joinFK(self::COLUMN_ID_PLACE)
//         ->join(self::COLUMN_ID, array('t_eve_has_sport' => 'SvbBase_Model_EventHasSports'), SvbBase_Model_EventHasSports::COLUMN_ID_EVENT)
//         ->join(array('t_eve_has_sport' => SvbBase_Model_EventHasSports::COLUMN_ID_SPORT),
//         array('t_eve_sport' => 'SvbBase_Model_Sports'), SvbBase_Model_Sports::COLUMN_ID,
//         array('sports_string_merged' => 'GROUP_CONCAT('.SvbBase_Model_Sports::COLUMN_NAME.' SEPARATOR \';\')'))
//         ->groupBy(array(self::COLUMN_ID))
//         ->record($id);
   }
}
