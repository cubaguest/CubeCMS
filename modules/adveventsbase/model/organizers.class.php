<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_Organizers extends Model_ORM {
   
   const DB_TABLE = 'adv_events_organizers';
   
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID            = 'id_event_organizer';
	const COLUMN_ID_GROUP		= 'id_group';
	const COLUMN_NAME          = 'event_organizer_name';
	const COLUMN_ADDRESS		   = 'event_organizer_address';
	const COLUMN_NOTE          = 'event_organizer_note';
	const COLUMN_URL           = 'event_organizer_url';
	const COLUMN_URL_FCB       = 'event_organizer_fcb';
	const COLUMN_URL_YOUTUBE   = 'event_organizer_youtube';
	const COLUMN_URL_TWITTER   = 'event_organizer_twitter';
	const COLUMN_PRIORITY      = 'event_organizer_priority';
	const COLUMN_PHONE         = 'event_organizer_phone';
	const COLUMN_PHONE_2       = 'event_organizer_phone2';
	const COLUMN_EMAIL         = 'event_organizer_email';
	const COLUMN_CONTACT_PERSON= 'event_organizer_contact_person';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_event_orgs');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'int', 'index' => true, 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_PRIORITY, array('datatype' => 'int', 'index' => true, 'nn' => true, 'default' => 0));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ADDRESS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL_FCB, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL_TWITTER, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL_YOUTUBE, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_PHONE, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Model_Groups', Model_Groups::COLUMN_ID);
      $this->setPk(self::COLUMN_ID);
   }
   
    /**
    * @param int $limit
    * @param int $from
    * @param array|string $orderColumn
    * @param string $orderType
    * @return array
    */
   public static function getOrganizerByString($name, $limit = 10, $from = 0, $orderColumn = self::COLUMN_NAME, $orderType = Model_ORM::ORDER_ASC)
   {
      $m = new self();
      return $m
         ->where(self::COLUMN_NAME." LIKE :str1 OR ".self::COLUMN_ADDRESS." LIKE :str2 OR ".self::COLUMN_NOTE." LIKE :str3",
            array('str1' => '%'.$name.'%', 'str2' => '%'.$name.'%', 'str3' => '%'.$name.'%'))
         ->order(array($orderColumn => $orderType))
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
   public static function getOrganizers($limit = 10, $from = 0, $orderColumn = self::COLUMN_NAME, $orderType = Model_ORM::ORDER_ASC)
   {
      $m = new self();
      return $m
         ->order(array($orderColumn => $orderType))
         ->joinFK(self::COLUMN_ID_GROUP)
         ->limit($from, $limit)
         ->records();
   }
   
}
