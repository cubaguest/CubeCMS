<?php
/*
 * Třída modelu s listem Novinek
 */
class ContactModel extends DbModel {
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_CONTACT_NAME = 'name';
   const COLUMN_CONTACT_ID_ITEM = 'id_item';
   const COLUMN_CONTACT_ID = 'id_reference';
   const COLUMN_CONTACT_ID_CITY = 'id_city';
   const COLUMN_CONTACT_FILE = 'file';
   const COLUMN_CONTACT_CHANGED_TIME = 'changed_time';
   const COLUMN_CONTACT_TEXT = 'text';

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
   public function saveNewContact($names, $labels, $file, $idCity) {
      $Arr = $this->createValuesArray(self::COLUMN_CONTACT_NAME, $names,
                                          self::COLUMN_CONTACT_TEXT, $labels,
                                          self::COLUMN_CONTACT_CHANGED_TIME, time(),
                                          self::COLUMN_CONTACT_ID_ITEM, $this->getModule()->getId(),
                                          self::COLUMN_CONTACT_FILE, $file,
                                          self::COLUMN_CONTACT_ID_CITY, $idCity);

      $sqlInsert = $this->getDb()->insert()
      ->table($this->getModule()->getDbTable(), true)
      ->colums(array_keys($Arr))
      ->values(array_values($Arr));
//      //		Vložení do db
      return $this->getDb()->query($sqlInsert);
   }

   public function getAreas(){
      $select = $this->getDb()->select()->table($this->getModule()->getDbTable(3), 'city')
      ->colums(Db::COLUMN_ALL)
      ->join(array('area' => $this->getModule()->getDbTable(2)), array('city' => self::COLUMN_ID_AREA, self::COLUMN_ID_AREA),
         null, Db::COLUMN_ALL)
      ->order('area.'.self::COLUMN_ID_AREA);

      return $this->getDb()->fetchAll($select);
   }

   /**
    * Metoda vrací pole referencí
    *
    * @return array -- pole s referencí
    */
   public function getContactsList() {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable(), 'contact')
      ->colums(array(self::COLUMN_CONTACT_NAME =>"IFNULL(".self::COLUMN_CONTACT_NAME.'_'
            .Locale::getLang().",".self::COLUMN_CONTACT_NAME.'_'.Locale::getDefaultLang().")",
            self::COLUMN_CONTACT_TEXT =>"IFNULL(".self::COLUMN_CONTACT_TEXT.'_'.Locale::getLang()
            .",".self::COLUMN_CONTACT_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_CONTACT_ID, self::COLUMN_CONTACT_FILE, self::COLUMN_CONTACT_ID_CITY))
      ->join(array('city' => $this->getModule()->getDbTable(3)), array('contact' => self::COLUMN_CONTACT_ID_CITY,
            self::COLUMN_ID_CITY), null, self::COLUMN_CITY_NAME)
      ->join(array('area' => $this->getModule()->getDbTable(2)), array('city' => self::COLUMN_ID_AREA,
            self::COLUMN_ID_AREA),null, self::COLUMN_AREA_NAME)
      ->where('contact.'.self::COLUMN_CONTACT_ID_ITEM, $this->getModule()->getId())
      ->order('area.'.self::COLUMN_AREA_NAME)
      ->order('city.'.self::COLUMN_CITY_NAME);
      return $this->getDb()->fetchAll($sqlSelect);
   }

   public function getLabelsLangs() {
      return $this->refLabe;
   }

   public function getNamesLangs() {
      return $this->refName;
   }

   public function getId() {
      return $this->refId;
   }
   public function getFile($id = null) {
      if($this->getNamesLangs() == null AND $id != null){
         $this->getReferenceDetailAllLangs($id);
      }
      return $this->refFile;
   }

   /**
    * Metoda vrací referenci podle zadaného ID ve všech jazycích
    *
    * @param integer -- id reference
    * @return array -- pole s referencí
    */
   public function loadReferenceDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable())
      ->where(self::COLUMN_REFERENCE_ID, $id);
      $reference = $this->getDb()->fetchAssoc($sqlSelect);
      if(!empty ($reference)){
         $reference = $this->parseDbValuesToArray($reference, array(self::COLUMN_REFERENCE_NAME,
               self::COLUMN_REFERENCE_LABEL));
         $this->refName = $reference[self::COLUMN_REFERENCE_NAME];
         $this->refLabe = $reference[self::COLUMN_REFERENCE_LABEL];
         $this->refId = $reference[self::COLUMN_REFERENCE_ID];
         $this->refFile = $reference[self::COLUMN_REFERENCE_FILE];
      }
      return $reference;
   }

   /**
    * Metoda uloží upravenou ovinku do db
    *
    * @param array -- pole s detaily novinky
    */
   public function saveEditReference($names, $labels, $file, $id) {
      if($file != null){
         $refArr = $this->createValuesArray(self::COLUMN_REFERENCE_NAME, $names,
            self::COLUMN_REFERENCE_LABEL, $labels,
            self::COLUMN_REFERENCE_CHANGED_TIME, time(),
            self::COLUMN_REFERENCE_FILE, $file);
      } else {
         $refArr = $this->createValuesArray(self::COLUMN_REFERENCE_NAME, $names,
            self::COLUMN_REFERENCE_LABEL, $labels,
            self::COLUMN_REFERENCE_CHANGED_TIME, time());
      }

      $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable(), true)
      ->set($refArr)
      ->where(self::COLUMN_REFERENCE_ID, $id);
      // vložení do db
      return $this->getDb()->query($sqlInsert);
   }

   public function deleteReference($id) {
      //			smazání novinky
      $sqlUpdate = $this->getDb()->delete()->table($this->getModule()->getDbTable(), true)
      ->where(self::COLUMN_REFERENCE_ID, $id);
      return $this->getDb()->query($sqlUpdate);
   }

   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(1))
                  ->colums(self::COLUMN_REFERENCE_CHANGED_TIME)
						->where(self::COLUMN_REFERENCE_ID_ITEM, $this->getModule()->getId())
                  ->order(self::COLUMN_REFERENCE_CHANGED_TIME, Db::ORDER_ASC)
                  ->limit(0, 1);
		$time = $this->getDb()->fetchAssoc($sqlSelect);
      if(!empty ($time)){
         return $time[self::COLUMN_REFERENCE_CHANGED_TIME];
      }
		return false;
   }
}
?>