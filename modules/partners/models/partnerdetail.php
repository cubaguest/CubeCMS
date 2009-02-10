<?php
/*
 * Třída modelu s detailem Partnera
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class PartnerDetailModel extends DbModel {
   /**
    * Názvy sloupců v DB
    */
   const COLUMN_ID_PARTNER = 'id_partner';
   const COLUMN_ID_ITEM = 'id_item';
   const COLUMN_NAME = 'name';
   const COLUMN_LABEL = 'label';
   const COLUMN_URL = 'url';
   const COLUMN_LOGO_FILE = 'logo_file';
   const COLUMN_LOGO_TYPE = 'logo_type';
   const COLUMN_LOGO_WIDTH = 'logo_width';
   const COLUMN_LOGO_HEIGHT = 'logo_height';

   /**
    * Typy log
    */
   const LOGO_IMAGE_TYPE = 'image';
   const LOGO_FLASH_TYPE = 'flash';

   private $dataLoaded = false;

   private $partnerId = null;
   private $partnerName = null;
   private $partnerLabel = null;
   private $partnerUrl = null;
   private $partnerImage = null;



   /**
    * Metoda uloží novinku do db
    *
    * @param string $name -- název partnera
    * @param array $labelsArr -- pole s popisy partnera
    * @param string $url -- adresa partnera
    * @param string $logoFile -- název souboru s logem
    * @param string $logoType -- název typu souboru z konstaty 'LOGO_XXX_TYPE'
    * @param int $logoWidth -- šířka loga
    * @param int $logoHeight -- výška loga
    */
   public function saveNewPartner($name, $labelsArr, $Url = null, $logoFile = null,
      $logoType = null, $logoWidth = null, $logoHeight = null) {
      $partnerArr = $this->createValuesArray(self::COLUMN_NAME, $name,
                                          self::COLUMN_LABEL, $labelsArr,
                                          self::COLUMN_URL, $Url,
                                          self::COLUMN_ID_ITEM, $this->getModule()->getId(),
                                          self::COLUMN_LOGO_FILE, $logoFile,
                                          self::COLUMN_LOGO_TYPE, $logoType,
                                          self::COLUMN_LOGO_WIDTH, $logoWidth,
                                          self::COLUMN_LOGO_HEIGHT, $logoHeight);

      $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
      ->colums(array_keys($partnerArr))
      ->values(array_values($partnerArr));
//      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací partnera podle zadaného ID ve všech jazycích
    *
    * @param integer -- id partnera
    * @return array -- pole s partnerem
    */
   public function getPartnerDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
      ->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId())
      ->where(self::COLUMN_ID_PARTNER." = '".$id."'");

      $partner = $this->getDb()->fetchAssoc($sqlSelect, true);

      $partner = $this->parseDbValuesToArray($partner, array(self::COLUMN_LABEL));

      $this->partnerId = $partner[self::COLUMN_ID_PARTNER];
      $this->partnerName = $partner[self::COLUMN_NAME];
      $this->partnerLabel = $partner[self::COLUMN_LABEL];
      $this->partnerUrl = $partner[self::COLUMN_URL];
      $this->partnerImage = $partner[self::COLUMN_LOGO_FILE];

      return $partner;
   }

   public function getLabelsLangs($id = null) {
      $this->loadData($id);
      return $this->partnerLabel;
   }

   public function getName($id = null) {
      $this->loadData($id);
      return $this->partnerName;
   }

   public function getId($id = null) {
      $this->loadData($id);
      return $this->partnerId;
   }

   public function getUrl($id = null) {
      $this->loadData($id);
      return $this->partnerUrl;
   }

   public function getFileImage($id = null) {
      $this->loadData($id);
      return $this->partnerImage;
   }

   private function loadData($id) {
      if(!$this->dataLoaded AND $id != null){
         $this->getPartnerDetailAllLangs($id);
      }
   }

   public function saveEditPartner($name, $labelsArr, $idPartner, $Url = null) {

      $partnerArr = $this->createValuesArray(self::COLUMN_NAME, $name,
         self::COLUMN_LABEL, $labelsArr,
         self::COLUMN_URL, $Url);
      $sqlUpdate = $this->getDb()->update()->table($this->getModule()->getDbTable())
         ->set($partnerArr)
         ->where(self::COLUMN_ID_PARTNER.'='.$idPartner);
      // Vložení do db
      if($this->getDb()->query($sqlUpdate)){
         return true;
      } else {
         return false;
      }
   }

   public function saveEditPartnerFile($idPartner, $logoFile = null, $logoType = null,
      $logoWidth = null, $logoHeight = null) {

      $partnerArr = $this->createValuesArray(self::COLUMN_LOGO_FILE, $logoFile,
         self::COLUMN_LOGO_TYPE, $logoType,
         self::COLUMN_LOGO_WIDTH, $logoWidth,
         self::COLUMN_LOGO_HEIGHT, $logoHeight);

      $sqlUpdate = $this->getDb()->update()->table($this->getModule()->getDbTable())
         ->set($partnerArr)
         ->where(self::COLUMN_ID_PARTNER.'='.$idPartner);
      // Vložení do db
      if($this->getDb()->query($sqlUpdate)){
         return true;
      } else {
         return false;
      }
   }

   public function deletePartner($idPartner) {
      $deleteSql = $this->getDb()->delete()->from($this->getModule()->getDbTable())
      ->where(self::COLUMN_ID_PARTNER.' = '.$idPartner);

      return $this->getDb()->query($deleteSql);
   }
}
?>