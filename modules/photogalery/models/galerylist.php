<?php
/*
 * Třída modelu s listem galerií
 */
class GaleryListModel extends DbModel {
   /**
    * Názvy sloupců v databázi pro tabulku s galeriemi
    * @var string
    */
   const COLUMN_GALERY_LABEL     = 'label';
   const COLUMN_GALERY_TEXT      = 'text';
   const COLUMN_GALERY_TIME_ADD  = 'time_add';
   const COLUMN_GALERY_ID_USER 	= 'id_user';
   const COLUMN_GALERY_ID 			= 'id_galery';
   const COLUMN_GALERY_ID_ITEM 	= 'id_item';

   /**
    * Metoda vrací zadaný počet galerií
    * @param int $start -- od které galerie se začíná
    * @param int $cout -- počet galerií
    */
   public function getGaleryList($start = 0, $count = 0, $orderName = false) {
      $sqlSelect = $this->getDb()->select()->from(array("gal" => $this->getModule()->getDbTable()),
         array(self::COLUMN_GALERY_LABEL => "IFNULL(".self::COLUMN_GALERY_LABEL.'_'.Locale::getLang()
            .", ".self::COLUMN_GALERY_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_GALERY_TEXT => "IFNULL(".self::COLUMN_GALERY_TEXT.'_'.Locale::getLang()
            .", ".self::COLUMN_GALERY_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_GALERY_TIME_ADD, self::COLUMN_GALERY_ID))
      ->where("gal.".self::COLUMN_GALERY_ID_ITEM." = ".$this->getModule()->getId());

      if($count != 0){
         $sqlSelect->limit($start, $count);
      }

      if($orderName){
         $sqlSelect->order(self::COLUMN_GALERY_LABEL, 'asc');
      } else {
         $sqlSelect->order("gal.".self::COLUMN_GALERY_TIME_ADD, 'desc');
      }

      return $this->getDb()->fetchAssoc($sqlSelect);
   }

   /**
    * Metoda vrací celkový počet galeríí
    *
    * @return int -- počet galerií
    */
   public function getCountGaleries() {
      $sqlCount = $this->getDb()->select()->from($this->getModule()->getDbTable(), array("count"=>"COUNT(*)"))
      ->where(self::COLUMN_GALERY_ID_ITEM. ' = '.$this->getModule()->getId());
      $count = $this->getDb()->fetchObject($sqlCount);
      if(!empty ($count)){
         return $count->count;
      } else {
         return 0;
      }
   }

   private function getUserTable() {
      $tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);

      return $tableUsers;
   }
}

?>