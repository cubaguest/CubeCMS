<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_Locations extends Model_ORM {
   const DB_TABLE = 'adv_events_lcoations';

	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 	   = 'id_place_location';
	const COLUMN_NAME		= 'location_name';
	const COLUMN_ZIP		= 'location_zip_code';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_advevent_locations');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ZIP, array('datatype' => 'varchar(6)', 'pdoparam' => PDO::PARAM_STR));

      $this->addRelatioOneToMany(self::COLUMN_ID, 'AdvEventsBase_Model_Places', AdvEventsBase_Model_Places::COLUMN_ID_LOCATION);
      
      $this->setPk(self::COLUMN_ID);
      $this->order(self::COLUMN_NAME);
   }
   
//   public static function getLocations()
//   {
//      $m = new self();
//      
//      return $m->order(self::COLUMN_NAME)->records();
//   }
}