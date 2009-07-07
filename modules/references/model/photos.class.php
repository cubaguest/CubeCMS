<?php
/*
 * Třída modelu detailem článku
 */
class References_Model_Photos extends Model_Db {
	/**
	 * Názvy sloupců v databázi
	 */
	const COLUMN_LABEL = 'label';
	const COLUMN_FILE = 'file';
	const COLUMN_ID = 'id_ref_photo';
	const COLUMN_ID_REFERENCE = 'id_reference';

   /**
    * id poslední přidané fotky
    * @var int
    */
    private $lastInsertId = null;

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s popisem fotky
    * @param string -- název souboru
    * @param integer -- id reference
    */
   public function saveNewPhoto($labels, $file, $idRefer) {
      if($labels != null){
      $photoArr = $this->createValuesArray(self::COLUMN_LABEL, $labels,
                                          self::COLUMN_FILE, $file,
                                          self::COLUMN_ID_REFERENCE, $idRefer);
      } else {
         $photoArr = $this->createValuesArray(self::COLUMN_FILE, $file,
                                          self::COLUMN_ID_REFERENCE, $idRefer);
      }

      $sqlInsert = $this->getDb()->insert()->table($this->module()->getDbTable(2))
      ->colums(array_keys($photoArr))
      ->values(array_values($photoArr));
      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         $this->lastInsertId = $this->getDb()->getLastInsertedId();
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací id posledního vložené fotky
    * @return integer -- id fotky
    */
   public function getLastInsertedId() {
      return $this->lastInsertId;
   }

   /**
    * Metoda vrací fotky podle zadané id reference ve všech jazycích
    *
    * @param integer -- id reference
    * @return array -- pole s fotkama
    */
   public function getListAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable(2))
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_ID_REFERENCE, $id)
      ->order(self::COLUMN_ID, Db::ORDER_DESC);
      $photos = $this->getDb()->fetchAll($sqlSelect);
      $this->setLangColumn($photos, array(self::COLUMN_LABEL), true);
      return $photos;
   }

   /**
    * Metoda vrací fotky podle zadané id reference v navoleném jazyku
    *
    * @param integer -- id reference
    * @return array -- pole s fotkama
    */
   public function getList($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable(2))
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_ID_REFERENCE, $id)
      ->order(self::COLUMN_ID, Db::ORDER_DESC);
      $photos = $this->getDb()->fetchAll($sqlSelect);
      $this->setLangColumn($photos, array(self::COLUMN_LABEL));
      return $photos;
   }

   /**
    * Metoda vrací fotku podle zadaného ID a ve všech jazycích
    *
    * @param integer -- id fotky
    * @return array -- pole s článkem
    */
   public function getPhotoAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable(2))
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_ID, $id);
      $photos = $this->getDb()->fetchAssoc($sqlSelect);
      $this->setLangColumn($photos, array(self::COLUMN_LABEL), true);
      return $photos;
   }

//   public function getLabelsLangs() {
//      return $this->articleLabel;
//   }
//
//   public function getTextsLangs() {
//      return $this->articleText;
//   }
//
//   public function getId() {
//      return $this->articleId;
//   }
//
//   public function getIdUser() {
//      return $this->articleIdUser;
//   }

   /**
    * Metoda vrací novinku podle zadaného ID ve všech jazycích
    *
    * @param integer -- id novinky
    * @return array -- pole s novinkou
    */
//   public function getArticleDetailAllLangs($id) {
//      //		načtení novinky z db
//      $sqlSelect = $this->getDb()->select()
//      ->table($this->module()->getDbTable())
//      ->colums(Db::COLUMN_ALL)
//      ->where(self::COLUMN_ID_ITEM, $this->module()->getId())
//      ->where(self::COLUMN_ID, $id);
//
//      $article = $this->getDb()->fetchAssoc($sqlSelect);
//
//      if(empty ($article)){
//         throw new UnexpectedValueException(_('Zadaný článek neexistuje'), 1);
//      }
//      $article = $this->parseDbValuesToArray($article, array(self::COLUMN_NAME,
//               self::COLUMN_TEXT));
//
//      $this->articleText = $article[self::COLUMN_TEXT];
//      $this->articleLabel = $article[self::COLUMN_NAME];
//      $this->articleId = $article[self::COLUMN_ID];
//
//      return $article;
//   }

   /**
    * Metoda uloží upravenou fotku do db
    *
    * @param array -- pole s popisy fotky
    */
   public function saveEditPhoto($labels, $id) {
      $articleArr = $this->createValuesArray(self::COLUMN_LABEL, $labels);

      $sqlInsert = $this->getDb()->update()->table($this->module()->getDbTable(2))
            ->set($articleArr)
            ->where(self::COLUMN_ID, $id);

      // vložení do db
      return $this->getDb()->query($sqlInsert);
   }

   /**
    * Metoda smaže zadanou fotku
    * @param integer $id -- id fotky
    * @return bool
    */
   public function deletePhoto($id) {
      $sqlDelete = $this->getDb()
      ->delete()->table($this->module()->getDbTable(2))
      ->where(self::COLUMN_ID,$id);

      return $this->getDb()->query($sqlDelete);
   }
}

?>