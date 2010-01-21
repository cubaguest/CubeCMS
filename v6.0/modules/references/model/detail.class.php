<?php
/*
 * Třída modelu detailem článku
 */
class References_Model_Detail extends Model_Db {
   /**
    * Tabulka s detaily
    */
    const DB_TABLE = 'references';

	/**
	 * Názvy sloupců v databázi
	 */
	const COLUMN_NAME = 'name';
	const COLUMN_TEXT = 'text';
	const COLUMN_TIME = 'changed_time';
	const COLUMN_ID_ITEM = 'id_item';
	const COLUMN_ID = 'id_reference';

	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUMN_USER_NAME = 'username';
	const COLUMN_USER_ID =	 'id_user';

//   private $articleLabel = null;
//
//   private $articleText = null;
//
//   private $articleId = null;
//
//   private $articleIdUser = null;
   private $lastEditIdRef = null;

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveNewReference($Labels, $texts) {
      $refArr = $this->createValuesArray(self::COLUMN_NAME, $Labels,
                                          self::COLUMN_TEXT, $texts,
                                          self::COLUMN_ID_ITEM, $this->module()->getId(),
                                          self::COLUMN_TIME, time(),
                                          self::COLUMN_TIME, time());

      $sqlInsert = $this->getDb()->insert()->table(Db::table(self::DB_TABLE))
      ->colums(array_keys($refArr))
      ->values(array_values($refArr));
//      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         $this->lastEditIdRef = $this->getDb()->getLastInsertedId();
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací id posledního vloženého článku
    * @return integer -- id článku
    */
   public function getLastInsertedId() {
      return $this->lastEditIdRef;
   }

   /**
    * Metoda vrací referenci podle zadaného ID a v aktuálním jazyku
    *
    * @param integer -- id reference
    * @return array -- pole s referencí
    */
   public function getReferenceDetailSelLang($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table(Db::table(self::DB_TABLE))
      ->colums(array(self::COLUMN_NAME =>"IFNULL(".self::COLUMN_NAME.'_'.Locale::getLang()
            .",".self::COLUMN_NAME.'_'.Locale::getDefaultLang().")",
            self::COLUMN_TEXT =>"IFNULL(".self::COLUMN_TEXT.'_'.Locale::getLang()
            .",".self::COLUMN_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_TIME, self::COLUMN_ID))
      ->where(self::COLUMN_ID_ITEM, $this->module()->getId())
      ->where(self::COLUMN_ID, $id);

      $refer = $this->getDb()->fetchAssoc($sqlSelect);

      $this->refId = $refer[self::COLUMN_ID];

      return $refer;
   }

   /**
    * Metoda vrací referenci podle zadaného ID ve všech jazycích
    *
    * @param integer -- id reference
    * @return array -- pole s referencí
    */
   public function getReferenceDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table(Db::table(self::DB_TABLE))
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_ID_ITEM, $this->module()->getId())
      ->where(self::COLUMN_ID, $id);

      $reference = $this->getDb()->fetchAssoc($sqlSelect);

      if(empty ($reference)){
         throw new UnexpectedValueException(_('Zadaný článek neexistuje'), 1);
      }
      $reference = $this->parseDbValuesToArray($reference, array(self::COLUMN_NAME,
               self::COLUMN_TEXT));

      $this->refText = $reference[self::COLUMN_TEXT];
      $this->refName = $reference[self::COLUMN_NAME];
      $this->refId = $reference[self::COLUMN_ID];

      return $reference;
   }

   /**
    * Metoda uloží upravenou referenci do db
    *
    * @param array -- pole s detaily reference
    */
   public function saveEditReference($labels, $texts, $id) {
      $refArr = $this->createValuesArray(self::COLUMN_NAME, $labels,
                                          self::COLUMN_TEXT, $texts,
                                          self::COLUMN_TIME, time());

      $sqlInsert = $this->getDb()->update()->table(Db::table(self::DB_TABLE))
            ->set($refArr)
            ->where(self::COLUMN_ID, $id);

      // vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      };
   }

   /**
    * Metoda smaže zadanou referenci
    * @param integer $idArticle
    * @return bool
    */
   public function deleteReference($idArticle) {
      $sqlDelete = $this->getDb()
      ->delete()->table(Db::table(self::DB_TABLE))
      ->where(self::COLUMN_ID,$idArticle);

      if($this->getDb()->query($sqlDelete)){
         return true;
      } else {
         return false;
      };
   }
}

?>