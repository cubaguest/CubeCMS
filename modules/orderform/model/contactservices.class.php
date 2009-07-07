<?php
/*
 * Třída modelu s listem měst
 */
class Orderform_Model_Contactservices extends Model_Db {
   const FILE_SERVICES = 'services.xml';
   /**
    * Metoda vrací pole referencí na kontaktní města
    *
    * @return array -- pole s kontakty
    */
   public function getCityList() {
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable(), 'cities')
      ->colums(array(Orderform_Model_Detail::COLUMN_CITY_ID,
              Orderform_Model_Detail::COLUMN_CITY_NAME =>"IFNULL(cities.".Orderform_Model_Detail::COLUMN_CITY_NAME.'_'
            .Locale::getLang().",cities.".Orderform_Model_Detail::COLUMN_CITY_NAME.'_'.Locale::getDefaultLang().")",
            Db::COLUMN_ALL))->order(Orderform_Model_Detail::COLUMN_CITY_ID) ;
      return $this->getDb()->fetchAll($sqlSelect);
   }


   /**
    * Metoda vrací seznam prvků k výběru
    * return SimpleXMLElement
    */
   public function getServices() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getModelsDir().self::FILE_SERVICES));
   }
}
?>