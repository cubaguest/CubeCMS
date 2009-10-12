<?php
/**
 * Objekt pro práci se sekcemi a kategoriemi, pro obsluhu menu a navigace
 *
 * @author jakub
 */
class Menu_Sections implements Iterator {
/**
 * Pole s názvy jazyků sekce
 * @var array
 */
   private $labels = array();
   private $level = 0;
   private $id = null;
   private $idParent = null;

   private $childrens = array();

   /**
    * Konstruktor pro vytvoření objektu se sekcemi
    * @param array $labels -- pole s popisy s jazyky
    */
   public function  __construct($labels) {
      $this->labels = $labels;
      $this->id = rand();
   }

   protected function setLevel($level) {
      $this->level = $level;
   }

   public function setLabel($label) {
      $this->labels[Locale::getLang()] = $label;
   }

   public function setLabels($label) {
      $this->labels = $label;
   }

   /**
    * Metoda nastaví id rodiče
    * @param int $id
    */
   protected function setParentId($id) {
      $this->idParent = $id;
   }

   /**
    * Metoda vrací popisek sekce
    * @return string -- popisek
    */
   public function getLabel() {
      if(isset ($this->labels[Locale::getLang()])){
         return $this->labels[Locale::getLang()];
      } else {
         return null;
      }
   }

   /**
    * Metoda vrací všechny popisky sekce v jazykovém poli
    * @return array -- popisky
    */
   public function getLabels() {
      return $this->labels;
   }

   /**
    * Metoda vrací id sekce
    * @return int -- id
    */
   public function getId() {
      return $this->id;
   }

   /**
    * Metoda vrací level (zanoření) sekce
    * @return int -- level
    */
   public function getLevel() {
      return $this->level;
   }

   /**
    * Metoda vrací pole potomků
    * @return array -- potomci
    */
   public function getChildrens() {
      return $this->childrens;
   }

   public function render() {
      $str = str_repeat('&nbsp;', $this->level*3);
      print ($str."sec ".$this->labels[Locale::getLang()]." - id: ".$this->id
         ." level: ".$this->level."<br/>");
      foreach ($this->childrens as $key => $variable) {
         if($variable instanceof Menu_Sections) {
            $variable->render();
         } else {
            print ($str.$str."cat ".$variable[Model_Category::COLUMN_CAT_LABEL]
               ." id: ".$variable[Model_Category::COLUMN_CAT_ID]."<br />");
         //            print ($str.$str."cat ID ".$key."<br />");
         }
      };
   }

   /**
    * Metoda vrací id nadřazené sekce
    * @return int
    */
   public function getParentId() {
      return $this->idParent;
   }

   public function printPath($idCat) {
      $ret = false;
      foreach ($this->childrens as $child) {
         if($child instanceof Menu_Sections) {
            $res = $child->printPath($idCat);
            if($res != false) {
               $ret = $this->labels[Locale::getLang()].' '.$res;
            }
         } else if($child == $idCat) {
               return ($this->labels['cs'].' '.$child);
            }
      }
      return $ret;
   }

   /**
    * Metoda nastaví kategorie a odstraní nepoužité kategorie a sekce
    * @param array $catArray -- pole s kategoriemi
    */
   public function setCategories($catArray) {
      foreach ($this->childrens as $key => $child) {
         if($child instanceof Menu_Sections) {
            $child->setCategories($catArray);
            if($child->isEmpty()) {
               unset ($this->childrens[$key]);
            }
         } else if(isset ($catArray[$child])) {
               $this->childrens[$key] = $catArray[$child];
            } else {
               unset ($this->childrens[$key]);
            }
      }
   }

   /**
    * Metoda zjišťuje jestli je sekce prázdná
    * @return boolean -- true pokud je prázdný
    */
   public function isEmpty() {
      return empty ($this->childrens);
   }

   /**
    * Metoda přidá
    * @param id rodiče $idParent
    * @param mixed $child -- sekce nebo kategorie
    */
   public function addChild($child, $idParent = null) {
      if($idParent == null OR $idParent == $this->id) {
         if($child instanceof Menu_Sections) {
            $child->setLevel($this->level+1);
            $child->setParentId($this->getId());
         }
         array_push($this->childrens, $child);
      } else {
         foreach ($this->childrens as $key => $variable) {
            if($variable instanceof Menu_Sections) {
               $variable->addChild($child, $idParent);
            }
         }
      }
   }

   /**
    * Metoda odstraní kategorii podle zadaného id sekce a pořadí
    * @param int $idSec -- id sekce
    * @param int $keyCat -- klíč kategoorie
    */
   public function removeCat($idSec, $keyCat) {
      if($idSec == $this->getId()){
         if(key_exists($keyCat, $this->childrens)){
            unset ($this->childrens[$keyCat]);
            return true;
         }
      } else {
         foreach ($this->childrens as $child) {
            if($child instanceof Menu_Sections AND $child->removeCat($idSec, $keyCat)){
               return true;
            }
         }
      }
      return false;
   }

   /**
    * Metoda odstraní sekci podle zadaného id sekce
    * @param int $idSec -- id sekce
    */
   public function removeSec($idSec) {
      foreach ($this->childrens as $key => $child) {
         if($child instanceof Menu_Sections){
            if($child->getId() == $idSec){
               unset($this->childrens[$key]);
               return true;
            } else {
               if($child->removeSec($idSec)){
                  return true;
               }
            }
         }
      }
      return false;
   }

   /**
    * Metoda vrací požadovaný objekt sekce podle její id
    * @param int $idSec -- id sekce
    * @return Menu_Sections/false
    */
   public function getSection($idSec) {
      if($this->getId() == $idSec) {
         return $this;
      }
      foreach ($this->childrens as $child) {
         if($child instanceof Menu_Sections) {
            $obj = $child->getSection($idSec);
            if($obj !== false) {
               return $obj;
            }
         }
      }
      return false;
   }

   /**
    * Metoda přesune potomka třídy nahoru nebo dolu
    * @param integer $idParent -- id potomka
    * @param integer $key -- klíč v poli
    * @param string $moveTo -- jestli nahoru nebo dolu ('up','down')
    */
   public function moveChild($idParent, $key, $moveTo = 'down') {
      $section = $this->getSection($idParent);
      if($moveTo == 'down'){
         
      } else if($moveTo == 'up'){

      } else {
         new UnexpectedValueException(_('Nočekávaná hodnota pro posun'), 1);
      }
      
   }

   // methods implements Iterator
   public function current(){
      return current($this->childrens);
   }

   public function key(){
      return key($this->childrens);
   }

   public function next(){
      return next($this->childrens);
   }

   public function rewind(){
      return reset($this->childrens);
   }

   public function valid(){
      return key($this->childrens) !== null;
   }

   public function count(){
      return count($this->childrens);
   }

   /**
    * Metoda proo serializaci
    * @return array
    */
   public function __sleep() {
      foreach (Locale::getAppLangs() as $lang) {
         if(!key_exists($lang, $this->labels)){
            $this->labels[$lang] = null;
         }
      }
      return array_keys(get_object_vars($this));
   }
}
?>
