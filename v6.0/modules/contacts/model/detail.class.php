<?php
/*
 * Třída modelu s listem Novinek
 */
class Contacts_Model_Detail extends Model_Db {
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_CONTACT_NAME = 'name';
   const COLUMN_CONTACT_ID_ITEM = 'id_item';
   const COLUMN_CONTACT_ID = 'id_contact';
   const COLUMN_CONTACT_ID_TYPE = 'id_type';
   const COLUMN_CONTACT_FILE = 'image';
   const COLUMN_CONTACT_CHANGED_TIME = 'changed_time';
   const COLUMN_CONTACT_TEXT = 'text';
   const COLUMN_CONTACT_POS_X = 'position_x';
   const COLUMN_CONTACT_POS_Y = 'position_y';
   const COLUMN_CONTACT_PRYORITY = 'priority';

   const COLUMN_ID_AREA = 'id_area';
   const COLUMN_ID_CITY = 'id_city';
   const COLUMN_CITY_NAME = 'city_name';
   const COLUMN_AREA_NAME = 'area_name';

   private $conName = null;

   private $conText = null;
   
   private $conId = null;
   private $conFile = null;

   /**
    * Metoda uloží referenci do db
    *
    * @param array -- pole s nadpisem kontaktu
    * @param array -- pole s textem kontaktu
    * @param string -- název souboru s obrázkem
    * @param integer -- id města
    */
   public function saveNewContact($names, $text, $posX, $posY, $file, $id_type, $priority) {
      $Arr = $this->createValuesArray(self::COLUMN_CONTACT_NAME, $names,
                                          self::COLUMN_CONTACT_TEXT, $text,
                                          self::COLUMN_CONTACT_POS_X, $posX,
                                          self::COLUMN_CONTACT_POS_Y, $posY,
                                          self::COLUMN_CONTACT_CHANGED_TIME, time(),
                                          self::COLUMN_CONTACT_ID_ITEM, $this->module()->getId(),
                                          self::COLUMN_CONTACT_FILE, $file,
                                          self::COLUMN_CONTACT_ID_TYPE, $id_type,
                                          self::COLUMN_CONTACT_PRYORITY, $priority);
      $sqlInsert = $this->getDb()->insert()
      ->table($this->module()->getDbTable(), true)
      ->colums(array_keys($Arr))
      ->values(array_values($Arr));
      //		Vložení do db
      return $this->getDb()->query($sqlInsert);
   }

   public function getTextsLangs() {
      return $this->conText;
   }

   public function getNamesLangs() {
      return $this->conName;
   }

   public function getId() {
      return $this->conId;
   }

//   public function getIdCity() {
//      return $this->conIdCity;
//   }

   public function getFile($id = null) {
      if($this->getNamesLangs() == null AND $id != null){
         $this->getContactDetailAllLangs($id);
      }
      return $this->conFile;
   }

   /**
    * Metoda vrací kontakt podle zadaného ID ve všech jazycích
    *
    * @param integer -- id kontaktu
    * @return array -- pole s kontaktem
    */
   public function getContactDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(), 'contact')
      ->colums(Db::COLUMN_ALL)
      ->join(array('type' => $this->module()->getDbTable(2)), array('contact' => self::COLUMN_CONTACT_ID_TYPE,
         Contacts_Model_Types::COLUMN_ID), null, Db::COLUMN_ALL)
      ->where(self::COLUMN_CONTACT_ID, $id);
      $contact = $this->getDb()->fetchAssoc($sqlSelect);
      if(!empty ($contact)){
         $contact = $this->parseDbValuesToArray($contact, array(self::COLUMN_CONTACT_NAME,
               self::COLUMN_CONTACT_TEXT));
         $this->conName = $contact[self::COLUMN_CONTACT_NAME];
         $this->conText = $contact[self::COLUMN_CONTACT_TEXT];
         $this->conId = $contact[self::COLUMN_CONTACT_ID];
         $this->conFile = $contact[self::COLUMN_CONTACT_FILE];
//         $this->conIdCity = $contact[self::COLUMN_CONTACT_ID_CITY];
      }
      return $contact;
   }

   /**
    * Metoda uloží upravený kontakt do db
    *
    * @param array -- pole s detaily novinky
    */
   public function saveEditContact($names, $labels, $posX, $posY, $prior, $file, $idType, $id) {
      if($file != null){
         $conArr = $this->createValuesArray(self::COLUMN_CONTACT_NAME, $names,
            self::COLUMN_CONTACT_TEXT, $labels,
            self::COLUMN_CONTACT_POS_X, $posX,
            self::COLUMN_CONTACT_POS_Y, $posY,
            self::COLUMN_CONTACT_PRYORITY, $prior,
            self::COLUMN_CONTACT_CHANGED_TIME, time(),
            self::COLUMN_CONTACT_ID_TYPE, $idType,
            self::COLUMN_CONTACT_FILE, $file);
      } else {
         $conArr = $this->createValuesArray(self::COLUMN_CONTACT_NAME, $names,
            self::COLUMN_CONTACT_TEXT, $labels,
            self::COLUMN_CONTACT_POS_X, $posX,
            self::COLUMN_CONTACT_POS_Y, $posY,
            self::COLUMN_CONTACT_PRYORITY, $prior,
            self::COLUMN_CONTACT_ID_TYPE, $idType,
            self::COLUMN_CONTACT_CHANGED_TIME, time());
      }

      $sqlInsert = $this->getDb()->update()->table($this->module()->getDbTable(), true)
      ->set($conArr)
      ->where(self::COLUMN_CONTACT_ID, $id);
      // vložení do db
      return $this->getDb()->query($sqlInsert);
   }

   public function deleteContact($id) {
      $sqlUpdate = $this->getDb()->delete()->table($this->module()->getDbTable(), true)
      ->where(self::COLUMN_CONTACT_ID, $id);
      return $this->getDb()->query($sqlUpdate);
   }

   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(1))
                  ->colums(self::COLUMN_CONTACT_CHANGED_TIME)
						->where(self::COLUMN_CONTACT_ID_ITEM, $this->getModule()->getId())
                  ->order(self::COLUMN_CONTACT_CHANGED_TIME, Db::ORDER_ASC)
                  ->limit(0, 1);
		$time = $this->getDb()->fetchAssoc($sqlSelect);
      if(!empty ($time)){
         return $time[self::COLUMN_CONTACT_CHANGED_TIME];
      }
		return false;
   }
}
?>