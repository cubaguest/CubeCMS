<?php
/*
 * Třída modelu s listem Novinek
 */
class ReferenceModel extends DbModel {
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_REFERENCE_LABEL = 'label';
   const COLUMN_REFERENCE_NAME = 'name';
   const COLUMN_REFERENCE_ID_ITEM = 'id_item';
   const COLUMN_REFERENCE_ID = 'id_reference';
   const COLUMN_REFERENCE_FILE = 'file';

   private $refName = null;

   private $refLabe = null;
   
   private $refId = null;
   private $refFile = null;



   /**
    * Metoda uloží referenci do db
    *
    * @param array -- pole s nadpisem reference
    * @param array -- pole s textem reference
    */
   public function saveNewReference($names, $labels, $file) {
      $referArr = $this->createValuesArray(self::COLUMN_REFERENCE_NAME, $names,
                                          self::COLUMN_REFERENCE_LABEL, $labels,
                                          self::COLUMN_REFERENCE_ID_ITEM, $this->getModule()->getId(),
                                          self::COLUMN_REFERENCE_FILE, $file);

      $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
      ->colums(array_keys($referArr))
      ->values(array_values($referArr));
//      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací pole referencí
    *
    * @return array -- pole s referencí
    */
   public function getReferencesList() {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()->from(array('refer' => $this->getModule()->getDbTable()),
         array(self::COLUMN_REFERENCE_NAME =>"IFNULL(".self::COLUMN_REFERENCE_NAME.'_'
            .Locale::getLang().",".self::COLUMN_REFERENCE_NAME.'_'.Locale::getDefaultLang().")",
            self::COLUMN_REFERENCE_LABEL =>"IFNULL(".self::COLUMN_REFERENCE_LABEL.'_'.Locale::getLang()
            .",".self::COLUMN_REFERENCE_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_REFERENCE_ID, self::COLUMN_REFERENCE_FILE))
         ->where('refer.'.self::COLUMN_REFERENCE_ID_ITEM." = ".$this->getModule()->getId());

      return $this->getDb()->fetchAssoc($sqlSelect);
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
      $sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
      ->where(self::COLUMN_REFERENCE_ID." = '".$id."'");

      $reference = $this->getDb()->fetchAssoc($sqlSelect, true);

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
            self::COLUMN_REFERENCE_FILE, $file);
      } else {
         $refArr = $this->createValuesArray(self::COLUMN_REFERENCE_NAME, $names,
            self::COLUMN_REFERENCE_LABEL, $labels);
      }


      $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
            ->set($refArr)
            ->where(self::COLUMN_REFERENCE_ID." = '".$id."'");

      // vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      };
   }

   public function deleteReference($id) {
      //			smazání novinky
      $sqlUpdate = $this->getDb()->delete()->from($this->getModule()->getDbTable())
      ->where(self::COLUMN_REFERENCE_ID." = ".$id);

      if($this->getDb()->query($sqlUpdate)){
         return true;
      } else {
         return false;
      };
   }

   private function getUserTable() {
      $tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
      return $tableUsers;
   }

}

?>