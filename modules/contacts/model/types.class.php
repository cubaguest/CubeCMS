<?php
/*
 * Třída modelu s listem Novinek
 */
class Contacts_Model_Types extends Model_Db {
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ID = 'id_type';
   const COLUMN_NAME = 'name_type';

   public function getContactTypes(){
      $select = $this->getDb()->select()->table($this->module()->getDbTable(2), 'types')
      ->colums(array(self::COLUMN_NAME => "IFNULL(".self::COLUMN_NAME.'_'.Locale::getLang().", "
            .self::COLUMN_NAME.'_'.Locale::getDefaultLang().")", self::COLUMN_ID))
      ->order(self::COLUMN_NAME);

      return $this->getDb()->fetchAll($select);
   }
}
?>