<?php
/**
 * Objekt pro práci se sekcemi a kategoriemi, pro obsluhu menu a navigace
 *
 * @author jakub
 */
class Category_Structure implements Iterator {
   private $level = 0;
   private $id = null;
   private $idParent = null;
   private $catObj = null;
   private $childrens = array();

   /**
    * Konstruktor pro vytvoření objektu se sekcemi
    * @param array $labels -- pole s popisy s jazyky
    */
   public function  __construct($id) {
      $this->id = $id;
   }

   protected function setLevel($level) {
      $this->level = $level;
   }

   /**
    * Metoda nastaví id rodiče
    * @param int $id
    */
   protected function setParentId($id) {
      $this->idParent = $id;
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
      if($this->level != 0) { // vynecháme ROOT kategorii
         $str = str_repeat('&nbsp;', $this->level*3);
//         print ($str." cat ".$this->catObj->label." - id: ".$this->catObj->id_category." level: ".$this->level."<br/>");
      }
      foreach ($this->childrens as $key => $child) {
         $child->render();
      };
   }

   /**
    * Metoda vrací id nadřazené sekce
    * @return int
    */
   public function getParentId() {
      return $this->idParent;
   }

   public function getPath($idCat, $retArray = array()) {
      if($this->getId() == $idCat){
         array_push($retArray, $this);
         return $retArray;
      }
      $newArr = $retArray;
      if($this->getCatObj() !== null){
         array_push($newArr, $this);
      }
      foreach ($this->childrens as $child) {
         $ret = $child->getPath($idCat, $newArr);
         if($ret !== false){
            return $ret;
         }
      }
      return false;
   }
   
   /**
    * Metoda nastaví kategorie a odstraní nepoužité kategorie a sekce
    * @param array $catArray -- pole s kategoriemi
    */
   public function setCategories($catArray) {
      if(isset ($catArray[$this->getId()]) AND $this->level != 0) {
         $this->catObj = new Category(null, false, $catArray[$this->getId()]);
      }
      foreach ($this->childrens as $key => $child) {
         if(isset ($catArray[$child->getId()])) {
            $child->setCategories($catArray);
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
   public function addChild(Category_Structure $child, $idParent = null) {
      if($idParent == null OR $idParent == $this->getId()) {
         $child->setParentId($this->getId());
         $child->setLevel($this->level+1);
         // přepočet ostatních levelů
         if(!$child->isEmpty()){
            $this->recalculateLevels($child, $child->getLevel());
         }
         array_push($this->childrens, $child);
      } else {
         foreach ($this->childrens as $key => $variable) {
            $variable->addChild($child, $idParent);
         }
      }
   }

   private function recalculateLevels($obj, $newLevel){
      foreach ($obj->getChildrens() as $child) {
         $child->setLevel($newLevel+1);
         if(!$child->isEmpty()){
            $child->recalculateLevels($child, $child->getLevel());
         }
      }
   }

   /**
    * Metoda odstraní kategorii podle zadaného id pořadí
    * @param int $idCat -- id kategoorie
    */
   public function removeCat($idCat) {
      foreach ($this->childrens as $key => $child) {
         if($child->getId() == $idCat){
            unset ($this->childrens[$key]);
            return true;
         }
         $child->removeCat($idCat);
      }
   }

   /**
    * Metoda vrací požadovaný objekt kategorie podle její id
    * @param int $idCat -- id kategorie
    * @return Category_Structure
    */
   public function getCategory($idCat) {
      if($this->getId() == $idCat) {
         return $this;
      }
      if(!$this->isEmpty()) {
         foreach ($this->childrens as $child) {
            $obj = $child->getCategory($idCat);
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
//   public function moveChild($id, $moveTo = 'down') {
//      $keyUp = null;
//      foreach ($this->childrens as $key => $child) {
//         if($child->getId() == $id){
//            if($moveTo == 'down') {
//
//            } else if($moveTo == 'up') {
//               $tmpChild = $this->childrens[$keyUp];
//               $this->childrens[$keyUp] = $child;
//               $this->childrens[$key] = $tmpChild;
//            } else {
//               new UnexpectedValueException(_('Nočekávaná hodnota pro posun'), 1);
//            }
//            return true;
//         }
//         $keyUp = $key;
//      }
//      return false;
//   }

   public function swapChild($child1, $child2) {
      if($child1 instanceof Category_Structure AND $child2 instanceof Category_Structure){
         $ch1Key = $this->getChildKey($child1);
         $ch2Key = $this->getChildKey($child2);
         $tmpChild = $this->childrens[$ch2Key];
         $this->childrens[$ch2Key] = $this->childrens[$ch1Key];
         $this->childrens[$ch1Key] = $tmpChild;
      } else {
         throw new InvalidArgumentException(_('Pro změnu nebyly předány platní potomci'), 2);
      }
   }

   /**
    * Metoda najde a vrátí klíč k potomku v poli
    * @param int $id -- id potomka
    * @return int -- klíč v poli
    */
   private function getChildKey($schild) {
      reset($this->childrens);
      while ($child = current($this->childrens)) {
         if ($child->getId() == $schild->getId()) {
            return key($this->childrens);
         }
         @next($this->childrens);
      }
      return false;
   }

   public function prevChild(Category_Structure $schild) {
      reset($this->childrens);
      while ($child = current($this->childrens)) {
         if ($child->getId() == $schild->getId()) {
            if(@prev($this->childrens)){
               return current($this->childrens);
            } else {
               return null;
            }
         }
         @next($this->childrens);
      }
      return null;
   }

   public function nextChild(Category_Structure $schild) {
      reset($this->childrens);
      while ($child = current($this->childrens)) {
         if ($child->getId() == $schild->getId()) {
            if(@next($this->childrens)){
               return current($this->childrens);
            } else {
               return null;
            }
         }
         @next($this->childrens);
      }
      return null;
   }

   public function getChild($id) {
      foreach ($this->childrens as $child) {
         if($child->getId() == $id){
            return $child;
         }
      }
      return false;
   }


   // methods implements Iterator
   public function current() {
      return current($this->childrens);
   }

   public function key() {
      return key($this->childrens);
   }

   public function next() {
      return next($this->childrens);
   }

   public function rewind() {
      return reset($this->childrens);
   }

   public function valid() {
      return key($this->childrens) !== null;
   }

   public function count() {
      return count($this->childrens);
   }

   /**
    * Metoda vrací objekt kategorie načtený z db
    * @return Object
    */
   public function getCatObj() {
      return $this->catObj;
   }

   public function saveStructure($structure = null) {
      if($structure == null) {
         $model = new Model_Config();
         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($this));
      } else {
         $model = new Model_Config();
         $model->saveCfg('CATEGORIES_STRUCTURE', serialize($structure));
      }
   }

   /**
    * Magická metoda pro výpis
    * @return string
    */
   public function  __toString() {
      if($this->getLevel() != 0){
         return (string)$this->getCatObj()->getLabel();
      }
      $string = null;
      return $string;
   }
}
?>
