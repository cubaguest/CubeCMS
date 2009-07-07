<?php
class Orderform_Model_Items extends Model_XmlFile {
   const FILE_PRODUCTS = 'products.xml';
   const FILE_COLORS = 'colors.xml';

   /**
    * Metoda vrací seznam prvků k výběru
    * return SimpleXMLElement
    */
   public function getItems() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getModelsDir().self::FILE_PRODUCTS));

   }

   /**
    * Metoda vrací barvy pro prvky
    * @return SimpleXMLElement
    */
   public function getColors() {
      return new SimpleXMLElement(file_get_contents($this->sys()->module()->getDir()->getModelsDir().self::FILE_COLORS));
   }
}
?>
