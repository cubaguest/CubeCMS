<?php
class Orderform_Model_Items extends Model_XmlFile {
   const FILE_PRODUCTS = 'products.xml';
   const FILE_COLORS = 'colors.xml';
   const FILE_PROFILE_WINDOW = 'profile-window.xml';
   const FILE_PROFILE_DOORS = 'profile-door.xml';
   const FILE_GRIDS = 'grid.xml';

   /**
    * Metoda vrací seznam prvků k výběru
    * return SimpleXMLElement
    */
   public function getItems() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getDataDir().self::FILE_PRODUCTS));

   }

   /**
    * Metoda vrací barvy pro prvky
    * @return SimpleXMLElement
    */
   public function getColors() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getDataDir().self::FILE_COLORS));
   }

   /**
    * Metoda vrací profily pro okna
    * @return SimpleXMLElement
    */
   public function getWindowProfiles() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getDataDir().self::FILE_PROFILE_WINDOW));
   }

   /**
    * Metoda vrací profily pro dveře
    * @return SimpleXMLElement
    */
   public function getDoorProfiles() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getDataDir().self::FILE_PROFILE_DOORS));
   }

   /**
    * Metoda vrací typy mřížek
    * @return SimpleXMLElement
    */
   public function getGrids() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getDataDir().self::FILE_GRIDS));
   }
}
?>
