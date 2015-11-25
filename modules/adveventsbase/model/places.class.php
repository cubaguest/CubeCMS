<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_Places extends Model_ORM {
   const DB_TABLE = 'adv_events_places';

	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 	   = 'id_ev_place';
	const COLUMN_ID_LOCATION = 'id_place_location';
	const COLUMN_NAME		= 'place_name';
	const COLUMN_DESC		= 'place_description';
	const COLUMN_ADDRESS	= 'place_address';
	const COLUMN_MAP_URL	= 'place_map_url';
	const COLUMN_URL	= 'place_url';
	const COLUMN_OPENING_HOURS	= 'place_opening_hours';
	const COLUMN_LAT	= 'place_lat';
	const COLUMN_LNG	= 'place_lng';
	const COLUMN_ID_EXTERNAL	= 'place_external_id';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_advevent_places');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_LOCATION, array('datatype' => 'int', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DESC, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ADDRESS, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MAP_URL, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ID_EXTERNAL, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'index' => true));
      $this->addColumn(self::COLUMN_OPENING_HOURS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LAT, array('datatype' => 'float', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LNG, array('datatype' => 'float', 'pdoparam' => PDO::PARAM_STR));

      $this->addForeignKey(self::COLUMN_ID_LOCATION, 'AdvEventsBase_Model_Locations', AdvEventsBase_Model_Locations::COLUMN_ID);
      
      $this->setPk(self::COLUMN_ID);
      $this->order(self::COLUMN_NAME);
   }

   /**
    * @param $id
    * @return Model_ORM_Record
    */
   public static function getPlace($id)
   {
      $m = new self();
      return $m->record($id);
   }
   
   /**
    * @param $id
    * @return Model_ORM_Record
    */
   public static function isPlaceByExternalID($id)
   {
      $m = new self();
      return (bool)$m->where(self::COLUMN_ID_EXTERNAL." = :ide", array('ide' => $id))->record();
   }
   
   /**
    * @param $name - název místa
    * @return Model_ORM_Record
    */
   public static function findPlaceByName($name)
   {
      $m = new self();
      return $m->where(self::COLUMN_NAME." LIKE :name", array('name' => '%'.$name.'%'))->record();
   }

   /**
    * @param int $limit
    * @param int $from
    * @param array|string $orderColumn
    * @param string $orderType
    * @return array
    */
   public static function getPlaces($limit = 1000, $from = 0, $orderColumn = self::COLUMN_NAME, $orderType = Model_ORM::ORDER_ASC)
   {
      $m = new self();
      return $m
         ->order(array($orderColumn => $orderType))
         ->joinFK(self::COLUMN_ID_LOCATION)
         ->limit($from, $limit)
         ->records();
   }

   /**
    * @param int $limit
    * @param int $from
    * @param array|string $orderColumn
    * @param string $orderType
    * @return array
    */
   public static function getPlacesByString($name, $limit = 10, $from = 0, $orderColumn = self::COLUMN_NAME, $orderType = Model_ORM::ORDER_ASC)
   {
      $m = new self();
      return $m
         ->where(self::COLUMN_NAME." LIKE :str1 OR "/*.self::COLUMN_DESC." LIKE :str2 OR "*/.self::COLUMN_ADDRESS." LIKE :str3",
            array('str1' => '%'.$name.'%', /*'str2' => '%'.$name.'%',*/ 'str3' => '%'.$name.'%'))
         ->order(array($orderColumn => $orderType))
         ->limit($from, $limit)
         ->records();
   }
   
   public static function getImagesUrl($idPlace)
   {
      $imagesArray = array();
      $baseDir = AdvEventsBase_Controller::getPlaceImagesDir($idPlace);
      $baseUrl = AdvEventsBase_Controller::getPlaceImagesUrl($idPlace);
      
      $tmp = glob($baseDir."*.{jpg,png,gif}", GLOB_BRACE);
      
      foreach ($tmp as $path) {
         $imagesArray[] = $baseUrl.pathinfo($path, PATHINFO_BASENAME);
      }
      return $imagesArray;
   }
}


class AdvEventsBase_Model_Places_Record extends Model_ORM_Record {
   
   public function getImagesUrl()
   {
      $imagesArray = array();
      $baseDir = AdvEventsBase_Controller::getPlaceImagesDir($this->getPK());
      $baseUrl = AdvEventsBase_Controller::getPlaceImagesUrl($this->getPK());
      
      $tmp = glob($baseDir."*.{jpg,png,gif}", GLOB_BRACE);
      
      foreach ($tmp as $path) {
         $imagesArray[] = $baseUrl.pathinfo($path, PATHINFO_BASENAME);
      }
      return $imagesArray;
   }
}