<?php
/**
 * Objekt pro práci se sekcemi a kategoriemi, pro obsluhu menu a navigace
 *
 * @author jakub
 */
class Category_Structure implements Iterator, Countable, ArrayAccess {
   const ALL = 1;
   const VISIBLE_ONLY = 2;
   const CACHE_KEY_NAME = 'struct';
   
   private $level = 0;
   private $id = null;
   private $idParent = null;
   private $catObj = null;
   private $childrens = array();

   private $withHidden = false; // odstranit
   public $type = 'main';
   
   /**
    * Viditelná struktura
    * @var unknown_type
    */
   protected static $structureVisible = false; 
   /**
    * Kompletní struktura
    * @var unknown_type
    */
   protected static $structure = false;
   /**
    * Cesta k aktuální kategorii
    * @var array
    */ 
   protected static $path; 

   /**
    * Konstruktor pro vytvoření objektu se sekcemi
    * @param array $labels -- pole s popisy s jazyky
    */
   public function  __construct($id)
   {
      $this->id = $id;
   }

   protected function setLevel($level)
   {
      $this->level = $level;
   }

   /**
    * Metoda nastaví id rodiče
    * @param int $id
    */
   protected function setParentId($id)
   {
      $this->idParent = $id;
   }

   /**
    * Metoda vrací id sekce
    * @return int -- id
    */
   public function getId()
   {
      return $this->id;
   }

   /**
    * Metoda vrací level (zanoření) sekce
    * @return int -- level
    */
   public function getLevel()
   {
      return $this->level;
   }

   /**
    * Metoda vrací pole potomků
    * @return array -- potomci
    */
   public function getChildrens()
   {
      return $this->childrens;
   }

   public function render()
   {
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
   public function getParentId()
   {
      return $this->idParent;
   }

   /**
    * Metoda vrátí cestu k zadané kategorii. POkud kaegorie není zadána, použije se aktuální
    * @param int $idCat
    * @param array $retArray -- internal
    * @param bool $onlyId
    */
   public function getPath($idCat = null, $retArray = array(), $onlyId = false)
   {
      if($idCat == null){
         $idCat = Category::getSelectedCategory()->getId();
      }
      if($this->getId() == $idCat){
         if($onlyId){
            array_push($retArray, (int)$this->getId());
         } else {
            array_push($retArray, $this);
         }
         return $retArray;
      }
      $newArr = $retArray;
      if($this->getCatObj() !== null){
         if($onlyId){
            array_push($newArr, (int)$this->getId());
         } else {
            array_push($newArr, $this);
         }
      }
      foreach ($this->childrens as $child) {
         $ret = $child->getPath($idCat, $newArr, $onlyId);
         if($ret !== false){
            return $ret;
         }
      }
      return false;
   }
   
   /**
    * metoda vrací pozici potomka
    * @param int $idc -- id kategorie
    */
   public function getPosition($idc)
   {
      if(!$this->isEmpty()){
         // nalezení v aktuálních potomcích
         $pos = 1;
         foreach ($this as $child) {
            if($child->getId() == $idc){
               return $pos;
            }
            $pos++;
         }
         // předání hledání potomkům
         foreach ($this as $child) {
            $pos = $child->getPosition($idc);
            if($pos != 0){
               return $pos;
            }
         }
      }
      return 0;
   }

   /**
    * Meoda vrací maximáolní hloubku stromu
    * @return int
    */
   public function getMaxDepth()
   {
      $d = 0;
   
      if(!$this->isEmpty()){
         $d = 1;
         $childd = 0;
         foreach ($this->getChildrens() as $child) {
            $maxd = $child->getMaxDepth();
            $childd = $maxd > $childd ? $maxd : $childd;
         }
         $d += $childd;
      }
      return $d;
   }
   
   /**
    * Metoda nastaví kategorie a odstraní nepoužité kategorie a sekce
    * @param array $catArray -- pole s kategoriemi
    * @deprecated -- nepoužívat. Stačí použít getStructure()
    */
   public function setCategories($catArray)
   {
      if(isset ($catArray[$this->getId()]) AND $this->level != 0) {
         $this->catObj = new Category(null, false, $catArray[$this->getId()]);
      }
      foreach ($this->childrens as $key => $child) {
         $child->withHidden($this->withHidden);
         if(isset ($catArray[$child->getId()])) {
            // načtení práva
            $right = $catArray[$child->getId()][Model_Rights::COLUMN_RIGHT] != null 
               ? $catArray[$child->getId()][Model_Rights::COLUMN_RIGHT] : $catArray[$child->getId()][Model_Category::COLUMN_DEF_RIGHT];

            if( (Auth::isAdmin() OR $right[0] == 'r' OR Auth::getUserId() == $catArray[$child->getId()][Model_Category::COLUMN_ID_USER_OWNER]) 
               AND ($this->withHidden // všechny zkryté bez rozdílu
                  OR ($catArray[$child->getId()][Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_ALL) // viditelné všem
                  OR (!Auth::isLogin() AND $catArray[$child->getId()][Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_NOT_LOGIN) // viditelné nepřihlášeným
                  OR (Auth::isLogin() AND $catArray[$child->getId()][Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_LOGIN) // viditelné přihlášeným
                  OR (Auth::isAdmin() AND $catArray[$child->getId()][Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_ADMIN) // viditelné adminům
                  OR (Auth::isAdminGroup() AND $catArray[$child->getId()][Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_ADMIN_ALL) // viditelné adminům ze všech domén
               )) {
                  $child->setCategories($catArray);
            } else {
               unset ($this->childrens[$key]);
            }
         } else {
            unset ($this->childrens[$key]);
         }
      }
   }

   /**
    * Metoda nastaví jestli se mají i zkryté kategorie
    * @param bool $hidden
    */
   public function withHidden($hidden = false)
   {
      $this->withHidden = $hidden;
   }

   /**
    * Metoda zjišťuje jestli je sekce prázdná
    * @return boolean -- true pokud je prázdný
    */
   public function isEmpty()
   {
      return empty ($this->childrens);
   }

   /**
    * Metoda přidá
    * @param id rodiče $idParent
    * @param mixed $child -- sekce nebo kategorie
    */
   public function addChild(Category_Structure $child, $idParent = 0, $pos = -1)
   {
      if($idParent == 0 OR $idParent == $this->getId()) {
         $child->setParentId($this->getId());
         $child->setLevel($this->level+1);
         // přepočet ostatních levelů
         if(!$child->isEmpty()){
            $this->recalculateLevels($child, $child->getLevel());
         }
         // přidání na konec
         if($pos == -1 OR $pos >= count($this->childrens)){
            array_push($this->childrens, $child);
         }
         // přidání na požadovanou pozici
         else {
            /**
             * Nejde pouze pomocí array_splice
             * @see http://www.php.net/manual/en/function.array-splice.php#41118
             */
            $firstPart = array_slice($this->childrens, 0, $pos);
            $secondPart = array_slice($this->childrens, $pos);
            $insertPart = array($child);
            $this->childrens = array_merge($firstPart, $insertPart, $secondPart);
         }
      } else {
         foreach ($this->childrens as $key => $variable) {
            $variable->addChild($child, $idParent, $pos);
         }
      }
   }

   private function recalculateLevels($obj, $newLevel)
      {
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
   public function removeCat($idCat)
   {
      foreach ($this as $key => $child) {
         if($child->getId() == $idCat){
            unset ($this[$key]);
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
   public function getCategory($idCat)
   {
      if($this->getId() == $idCat) {
         return $this;
      }
      if(!$this->isEmpty()) {
         foreach ($this as $child) {
            $obj = $child->getCategory($idCat);
            if($obj !== false) {
               return $obj;
            }
         }
      }
      return false;
   }
   
   /**
    * Metoda odstraní zadaného potomka ze struktury
    * @param int $id -- id potomka
    */
   public function removeChild($id)
   {
      foreach ($this as $key => $child) {
         if($child->getId() == $id){
            unset ($this[$key]);
         }
      }
   }

   /**
    * Metoda přesune potomka třídy na danou pozici
    * @param integer $idParent -- id potomka
    * @param integer $key -- klíč v poli
    * @param string $moveTo -- jestli nahoru nebo dolu ('up','down')
    */
   public function moveChild($pos, $newPos = -1)
   {
      $ch1Key = $this->getChildKey($child1);
      $ch2Key = $this->getChildKey($child2);
      $tmpChild = $this->childrens[$ch2Key];
      $this->childrens[$ch2Key] = $this->childrens[$ch1Key];
      $this->childrens[$ch1Key] = $tmpChild;
   }

   /**
    * Metoda najde a vrátí klíč k potomku v poli
    * @param int $id -- id potomka
    * @return int -- klíč v poli
    */
   private function getChildKey($schild)
   {
      reset($this->childrens);
      while ($child = current($this->childrens)) {
         if ($child->getId() == $schild->getId()) {
            return key($this->childrens);
         }
         @next($this->childrens);
      }
      return false;
   }

   public function prevChild(Category_Structure $schild)
   {
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

   public function nextChild(Category_Structure $schild)
   {
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

   public function getChild($id)
   {
      foreach ($this->childrens as $child) {
         if($child->getId() == $id){
            return $child;
         }
      }
      return false;
   }


   // methods implements Iterator
   public function current()
   {
      return current($this->childrens);
   }

   public function key()
   {
      return key($this->childrens);
   }

   public function next()
   {
      return next($this->childrens);
   }

   public function rewind()
   {
      return reset($this->childrens);
   }

   public function valid()
   {
      return key($this->childrens) !== null;
   }

   public function count()
   {
      return count($this->childrens);
   }

   /**
    * Metoda vrací objekt kategorie načtený z db
    * @return Category_Core
    */
   public function getCatObj()
   {
      return $this->catObj;
   }

   public function saveStructure()
   {
      $model = new Model_Config();
      $record = $model->where(Model_Config::COLUMN_KEY, 'CATEGORIES_STRUCTURE')->record();
      file_put_contents(AppCore::getAppCacheDir()."struct_before".date("Y-m-d_G:i").".tmp", $record->{Model_Config::COLUMN_VALUE});
      // tady cleanup protože v db není třeba objektů kategorií
      $data = serialize(self::$structure);
      $record->{Model_Config::COLUMN_VALUE} = $data;
      $model->save($record);
      $cache = new Cache();
      $cache->delete(self::getCacheKey());
      self::loadStruct();
   }

   /**
    * Metoda vrací strukturu kategorií
    * @param bool $admin -- true pro vrácení struktury admin menu
    * @return Category_Structure
    */
   public static function getStructure($type = self::VISIBLE_ONLY)
   {
      if(self::$structure == false){
         self::loadStruct();
      }
      switch ($type) {
         case self::ALL:
            $rStruct = self::$structure;
            break;
         case self::VISIBLE_ONLY:
         default:
            $rStruct = self::$structureVisible;
            break;
      }
      return $rStruct;
   }
   
   /**
    * Metoda načte strukturu a doplní objekty kategorií
    */
   protected static function loadStruct()
   {
      $cache = new Cache();
      if( $st = $cache->get(self::getCacheKey()) ){
         self::$structure = $st['base'];
         self::$structureVisible = $st['vis'];
      } else {
         self::$structure = unserialize(VVE_CATEGORIES_STRUCTURE);
         // tohle by se mělo řešit přes __wakeUp
         $modelCats = new Model_Category();
         $cats = $modelCats->getCategoryList(true);
         
         self::$structure->setCategoriesObjects($cats);
         
         self::$structureVisible = clone self::$structure;
         self::$structureVisible->clearNonVisible();
         
         $cache->set(self::getCacheKey(), array( 'base' => self::$structure, 'vis' => self::$structureVisible ));
      }
   }
   
   protected function setCategoriesObjects($catArray, $level = 0)
   {
      $childs = $this->getChildrens();
      foreach ($childs as $key => $child) {
         if(isset ($catArray[$child->getId()])) {
            $child->catObj = new Category(null, false, $catArray[$child->getId()]);
            $child->setCategoriesObjects($catArray, $level+1);
         } else {
            unset ($this[$key]);
         }
      }
   }
   
   protected function clearNonVisible(){
      $childs = $this->getChildrens(); // kvůli referencím tohle musí být přenesené
      foreach ($childs as $key => $child) {
         $right = (string)$child->getCatObj()->getDataObj()->{Model_Rights::COLUMN_RIGHT};
         $rightDefault = (string)$child->getCatObj()->getDataObj()->{Model_Category::COLUMN_DEF_RIGHT};
         $visibility = (string)$child->getCatObj()->getDataObj()->{Model_Category::COLUMN_VISIBILITY};
         $ownerId = (string)$child->getCatObj()->getDataObj()->{Model_Category::COLUMN_ID_USER_OWNER};
          
         $right = $right != null ? $right : $rightDefault;
          
         if( ( (string)$child->getCatObj()->getDataObj()->{Model_Category::COLUMN_URLKEY} != null) // musí být URL klíč, jinak je neplatná
               AND (Auth::isAdmin() OR $right[0] == 'r' OR Auth::getUserId() == $ownerId)
               AND (
                     ($visibility == Model_Category::VISIBILITY_ALL) // viditelné všem
                     OR (!Auth::isLogin() AND $visibility == Model_Category::VISIBILITY_WHEN_NOT_LOGIN) // viditelné nepřihlášeným
                     OR (Auth::isLogin() AND $visibility == Model_Category::VISIBILITY_WHEN_LOGIN) // viditelné přihlášeným
                     OR (Auth::isAdmin() AND $visibility == Model_Category::VISIBILITY_WHEN_ADMIN) // viditelné adminům
                     OR (Auth::isAdminGroup() AND $visibility == Model_Category::VISIBILITY_WHEN_ADMIN_ALL) // viditelné adminům ze všech domén
               )
         ) {
            if(!$child->isEmpty()){
               $this[$key]->clearNonVisible();
            }
         } else {
            unset($this[$key]);
         }
      }
   }  

   /**
    * Magická metoda pro výpis
    * @return string
    */
   public function  __toString()
   {
      if($this->getLevel() != 0 AND $this->getCatObj() instanceof Category_Core){
         return (string)$this->getCatObj()->getLabel();
      }
      $string = null;
      return (string)$string;
   }
   
   public function __sleep() 
   {
      $this->cleanUpCategory();
      return array('level', 'id', 'idParent', 'catObj', 'childrens');
   }

   protected function cleanUpCategory() 
   {
      $this->catObj = null;
      foreach ($this->getChildrens() as $child) {
         $child->cleanUpCategory();
      }
   }
   
   private static function getCacheKey()
   {
      return '_struct_user_'.Auth::getUserId()."_".Locales::getLang();
   } 
   
   /* ARRAY ACCESS */
   public function offsetSet($offset, $value) 
   {
      if (is_null($offset)) {
         $this->childrens[] = $value;
      } else {
         $this->childrens[$offset] = $value;
      }
   }
   public function offsetExists($offset) 
   {
      return isset($this->childrens[$offset]);
   }
   public function offsetUnset($offset)
   {
      unset($this->childrens[$offset]);
   }
   public function offsetGet($offset) 
   {
      return isset($this->childrens[$offset]) ? $this->childrens[$offset] : null;
   }
}
?>
