<?php
/*
 * Třída modelu s listem Novinek
 */
class ActionDetailModel extends DbModel {
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ACTION_LABEL = 'label';
   const COLUMN_ACTION_TEXT = 'text';
   const COLUMN_ACTION_TIME = 'time';
   const COLUMN_ACTION_ID_USER = 'id_user';
   const COLUMN_ACTION_ID_ITEM = 'id_item';
   const COLUMN_ACTION_ID = 'id_action';
   const COLUMN_ACTION_DISABLED = 'disable';
   const COLUMN_ACTION_DATE_START = 'start_date';
   const COLUMN_ACTION_DATE_STOP = 'stop_date';
   const COLUMN_ACTION_IMAGE = 'image';

   /**
    * Sloupce u tabulky uživatelů
    * @var string
    */
   const COLUMN_USER_NAME = 'username';
   const COLUMN_ISER_ID =	 'id_user';

   private $actionLabel = null;

   private $actionText = null;

   private $actionId = null;

   private $actionFile = null;

   private $actionIdUser = null;


   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem novinky
    * @param array -- pole s textem novinky
    * @param boolean -- id uživatele
    */
   public function saveNewAction($labels, $texts, $datestart, $datestop, $file, $idUser = 0) {
      if($file === true){
         $file = null;
      }

      $actionArr = $this->createValuesArray(self::COLUMN_ACTION_LABEL, $labels,
         self::COLUMN_ACTION_TEXT, $texts,
         //                                          self::COLUMN_ACTION_DATE_START, date('Y-m-d', $datestart),
         //                                          self::COLUMN_ACTION_DATE_STOP, date('Y-m-d', $datestop),
         //                                          self::COLUMN_ACTION_DATE_START, $datestart,
         //                                          self::COLUMN_ACTION_DATE_STOP, $datestop,
         self::COLUMN_ACTION_IMAGE, $file,
         self::COLUMN_ACTION_ID_ITEM, $this->getModule()->getId(),
         self::COLUMN_ACTION_ID_USER, $idUser,
         self::COLUMN_ACTION_TIME, time());

      $sqlInsert = $this->getDb()->insert()->table($this->getModule()->getDbTable())
      ->colums(array_keys($actionArr))
      ->values(array_values($actionArr));
      //      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací novinku podle zadaného ID a v aktuálním jazyku
    *
    * @param integer -- id novinky
    * @return array -- pole s novinkou
    */
   public function getActionDetailSelLang($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable(), 'action')
      ->colums(array(self::COLUMN_ACTION_LABEL =>"IFNULL(".self::COLUMN_ACTION_LABEL.'_'
            .Locale::getLang().",".self::COLUMN_ACTION_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ACTION_TEXT =>"IFNULL(".self::COLUMN_ACTION_TEXT.'_'.Locale::getLang()
            .",".self::COLUMN_ACTION_TEXT.'_'.Locale::getDefaultLang().")",
            Db::COLUMN_ALL))
      ->where('action.'.self::COLUMN_ACTION_ID_ITEM, $this->getModule()->getId())
      ->where('action.'.self::COLUMN_ACTION_ID, $id)
      ->where('action.'.self::COLUMN_ACTION_DISABLED, (int)false);

      $action = $this->getDb()->fetchAssoc($sqlSelect, true);

      $this->actionId = $action[self::COLUMN_ACTION_ID];
      //      $this->actionIdUser = $action[self::COLUMN_ACTION_ID_USER];

      return $action;
   }

   public function getLabelsLangs() {
      return $this->actionLabel;
   }

   public function getTextsLangs() {
      return $this->actionText;
   }

   public function getFile() {
      return $this->actionFile;
   }

   public function getId() {
      return $this->actionId;
   }

   public function getIdUser() {
      return $this->actionIdUser;
   }

   /**
    * Metoda vrací akci podle zadaného ID ve všech jazycích
    *
    * @param integer -- id akce
    * @return array -- pole s akcí
    */
   public function getActionDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable())
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_ACTION_ID_ITEM, $this->getModule()->getId())
      ->where(self::COLUMN_ACTION_ID, $id)
      ->where(self::COLUMN_ACTION_DISABLED, (int)false);

      $action = $this->getDb()->fetchAssoc($sqlSelect);

      $action = $this->parseDbValuesToArray($action, array(self::COLUMN_ACTION_LABEL,
            self::COLUMN_ACTION_TEXT));

      $this->actionText = $action[self::COLUMN_ACTION_TEXT];
      $this->actionLabel = $action[self::COLUMN_ACTION_LABEL];
      $this->actionFile = $action[self::COLUMN_ACTION_IMAGE];
      $this->actionId = $action[self::COLUMN_ACTION_ID];

      return $action;
   }

   /**
    * Metoda uloží upravenou ovinku do db
    *
    * @param array -- pole s detaily novinky
    */
   public function saveEditAction($newsLabels, $newsTexts, $file, $idAction) {
      if($file === true){
         $file = null;
      }

      if($file != null){
         $actionArr = $this->createValuesArray(self::COLUMN_ACTION_LABEL, $newsLabels,
            self::COLUMN_ACTION_IMAGE, $file,
            self::COLUMN_ACTION_TEXT, $newsTexts);
      } else {
         $actionArr = $this->createValuesArray(self::COLUMN_ACTION_LABEL, $newsLabels,
            self::COLUMN_ACTION_TEXT, $newsTexts);
      }
      $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
      ->set($actionArr)
      ->where(self::COLUMN_ACTION_ID, $idAction);

      // vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      };
   }

   public function deleteAction($idAction) {
      //			smazání novinky
      $sqlUpdate = $this->getDb()->update()->table($this->getModule()->getDbTable())
      ->set(array(self::COLUMN_ACTION_DISABLED => (int)true))
      ->where(self::COLUMN_ACTION_ID." = ".$idAction);

      if($this->getDb()->query($sqlUpdate)){
         return true;
      } else {
         return false;
      };
   }
}

?>