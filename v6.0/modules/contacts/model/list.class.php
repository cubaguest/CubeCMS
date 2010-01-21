<?php
/*
 * Třída modelu s listem Novinek
 */
class Contacts_Model_List extends Model_Db {
   /**
    * Metoda vrací pole referencí
    *
    * @return array -- pole s kontakty
    */
   public function getContactsList() {
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable(), 'cont')
      ->colums(array(Contacts_Model_Detail::COLUMN_CONTACT_NAME =>"IFNULL(cont.".Contacts_Model_Detail::COLUMN_CONTACT_NAME.'_'
            .Locale::getLang().",cont.".Contacts_Model_Detail::COLUMN_CONTACT_NAME.'_'.Locale::getDefaultLang().")",
            Contacts_Model_Detail::COLUMN_CONTACT_TEXT =>"IFNULL(cont.".Contacts_Model_Detail::COLUMN_CONTACT_TEXT.'_'.Locale::getLang()
            .",cont.".Contacts_Model_Detail::COLUMN_CONTACT_TEXT.'_'.Locale::getDefaultLang().")",
            Db::COLUMN_ALL))
      ->join(array('type' => $this->module()->getDbTable(2)),
         array('cont' => Contacts_Model_Detail::COLUMN_CONTACT_ID_TYPE,
            Contacts_Model_Types::COLUMN_ID), Db::JOIN_LEFT,
         array(Contacts_Model_Types::COLUMN_NAME =>"IFNULL(type.".Contacts_Model_Types::COLUMN_NAME.'_'
            .Locale::getLang().",type.".Contacts_Model_Types::COLUMN_NAME.'_'.Locale::getDefaultLang().")"))
      ->where('cont.'.Contacts_Model_Detail::COLUMN_CONTACT_ID_ITEM, $this->module()->getId())
      ->order(Contacts_Model_Detail::COLUMN_CONTACT_PRYORITY, Db::ORDER_DESC)
      ->order(Contacts_Model_Detail::COLUMN_CONTACT_NAME, Db::ORDER_ASC);
      return $this->getDb()->fetchAll($sqlSelect);
   }

//   public function getTextsLangs() {
//      return $this->conText;
//   }
//
//   public function getNamesLangs() {
//      return $this->conName;
//   }
//
//   public function getId() {
//      return $this->conId;
//   }
//
//   public function getIdCity() {
//      return $this->conIdCity;
//   }
//
//   public function getFile($id = null) {
//      if($this->getNamesLangs() == null AND $id != null){
//         $this->getContactDetailAllLangs($id);
//      }
//      return $this->conFile;
//   }

//   /**
//    * Metoda vrací kontakt podle zadaného ID ve všech jazycích
//    *
//    * @param integer -- id kontaktu
//    * @return array -- pole s kontaktem
//    */
//   public function loadContactDetailAllLangs($id) {
//      //		načtení novinky z db
//      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(), 'contact')
//      ->colums(Db::COLUMN_ALL)
//      ->join(array('city' => $this->getModule()->getDbTable(3)), array('contact' => self::COLUMN_CONTACT_ID_CITY,
//         self::COLUMN_ID_CITY), null, self::COLUMN_CONTACT_ID_CITY)
//      ->where(self::COLUMN_CONTACT_ID, $id);
//      $contact = $this->getDb()->fetchAssoc($sqlSelect);
//      if(!empty ($contact)){
//         $contact = $this->parseDbValuesToArray($contact, array(self::COLUMN_CONTACT_NAME,
//               self::COLUMN_CONTACT_TEXT));
//         $this->conName = $contact[self::COLUMN_CONTACT_NAME];
//         $this->conText = $contact[self::COLUMN_CONTACT_TEXT];
//         $this->conId = $contact[self::COLUMN_CONTACT_ID];
//         $this->conFile = $contact[self::COLUMN_CONTACT_FILE];
//         $this->conIdCity = $contact[self::COLUMN_CONTACT_ID_CITY];
//      }
//      return $contact;
//   }

   /**
    * Metoda uloží upravený kontakt do db
    *
    * @param array -- pole s detaily novinky
    */
//   public function saveEditContact($names, $labels, $file, $idCity, $id) {
//      if($file != null){
//         $conArr = $this->createValuesArray(self::COLUMN_CONTACT_NAME, $names,
//            self::COLUMN_CONTACT_TEXT, $labels,
//            self::COLUMN_CONTACT_CHANGED_TIME, time(),
//            self::COLUMN_CONTACT_ID_CITY, $idCity,
//            self::COLUMN_CONTACT_FILE, $file);
//      } else {
//         $conArr = $this->createValuesArray(self::COLUMN_CONTACT_NAME, $names,
//            self::COLUMN_CONTACT_TEXT, $labels,
//            self::COLUMN_CONTACT_ID_CITY, $idCity,
//            self::COLUMN_CONTACT_CHANGED_TIME, time());
//      }
//
//      $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable(), true)
//      ->set($conArr)
//      ->where(self::COLUMN_CONTACT_ID, $id);
//      // vložení do db
//      return $this->getDb()->query($sqlInsert);
//   }
//
//   public function deleteContact($id) {
//      $sqlUpdate = $this->getDb()->delete()->table($this->getModule()->getDbTable(), true)
//      ->where(self::COLUMN_CONTACT_ID, $id);
//      return $this->getDb()->query($sqlUpdate);
//   }
//
//   public function getLastChange() {
//      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(1))
//                  ->colums(self::COLUMN_CONTACT_CHANGED_TIME)
//						->where(self::COLUMN_CONTACT_ID_ITEM, $this->getModule()->getId())
//                  ->order(self::COLUMN_CONTACT_CHANGED_TIME, Db::ORDER_ASC)
//                  ->limit(0, 1);
//		$time = $this->getDb()->fetchAssoc($sqlSelect);
//      if(!empty ($time)){
//         return $time[self::COLUMN_CONTACT_CHANGED_TIME];
//      }
//		return false;
//   }
}
?>