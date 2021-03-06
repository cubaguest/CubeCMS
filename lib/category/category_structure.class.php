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

   protected static $defaultCategory = null;
   protected static $defaultCategoryPriority = -1;

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
    * Metoda vrací nadřazený element
    * @return Category_Structure nebo false pokud je root
    */
   public function getParent()
   {
      return $this->idParent != 0 ? self::getStructure(self::ALL)->getCategory($this->idParent) : false;
   }

   /**
    * Metoda vrátí cestu k zadané kategorii. POkud kaegorie není zadána, použije se aktuální
    * @param int $idCat
    * @param array $retArray -- internal
    * @param bool|string $onlyIdOrProperty -- bool pro id nebo objekt, řetězec pro název property datového objektu
    */
   public function getPath($idCat = null, $retArray = array(), $onlyIdOrProperty = false)
   {
      if($idCat == null){
         $idCat = Category::getSelectedCategory()->getId();
      }
      if($this->getId() == $idCat){
         if($onlyIdOrProperty === true){
            array_push($retArray, (int)$this->getId());
         } else  if(is_string($onlyIdOrProperty)){
             array_push($retArray, $this->getCatObj()->getDataObj()->{$onlyIdOrProperty});
         } else {
            array_push($retArray, $this);
         }
         return $retArray;
      }
      $newArr = $retArray;
      if($this->getCatObj() !== null){
         if($onlyIdOrProperty === true){
            array_push($newArr, (int)$this->getId());
         } else if(is_string($onlyIdOrProperty)){
            array_push($newArr, $this->getCatObj()->getDataObj()->{$onlyIdOrProperty});
         } else {
            array_push($newArr, $this);
         }
      }
      foreach ($this->childrens as $child) {
         $ret = $child->getPath($idCat, $newArr, $onlyIdOrProperty);
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
    * Odebere neviditelné potomky
    */
   public function removeNonVisibleChildrens()
   {
      foreach ($this->childrens as $key => $child) {
         $dataObj = $child->getCatObj()->getCatDataObj();
         if($child->getCatObj() != false
//            AND (Auth::isAdmin() OR $dataObj[$child->getId()][Model_Rights::COLUMN_RIGHT][0] == 'r' OR Auth::getUserId() == $catArray[$child->getId()][Model_Category::COLUMN_ID_USER_OWNER]) // práva ke kategorii
            AND (
//               $this->withHidden // všechny zkryté bez rozdílu OR
               ($dataObj[Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_ALL) // viditelné všem
               OR (!Auth::isLogin() AND $dataObj[Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_NOT_LOGIN) // viditelné nepřihlášeným
               OR (Auth::isLogin() AND $dataObj[Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_LOGIN) // viditelné přihlášeným
               OR (Auth::isAdmin() AND $dataObj[Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_ADMIN) // viditelné adminům
               OR (Auth::isAdminGroup() AND $dataObj[Model_Category::COLUMN_VISIBILITY] == Model_Category::VISIBILITY_WHEN_ADMIN_ALL) // viditelné adminům ze všech domén
            )) {
            $child->removeNonVisibleChildrens();
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

   /**
    * Vrací předchozí položku
    * @param Category_Structure $schild
    * @return Category_Structure|bool
    * @todo Odstranit parametr!!!
    * @note parametr se bude odstraňovat
    */
   public function prevChild(Category_Structure $schild = null)
   {
      if(!$schild){
         $parent = $this->getParent();
         if($parent){
            $childs = $parent->getChildrens();
            reset($childs);
            while ($child = current($childs)) {
               if ($child->getId() == $this->getId()) {
                  if(@prev($childs)){
                     return current($childs);
                  } else {
                     return false;
                  }
               }
               @next($childs);
            }
         }
      } else {
         reset($this->childrens);
         while ($child = current($this->childrens)) {
            if ($child->getId() == $schild->getId()) {
               if(@prev($this->childrens)){
                  return current($this->childrens);
               } else {
                  return false;
               }
            }
            @next($this->childrens);
         }
      }
      return false;
   }

   /**
    * Vrací následující položku
    * @param Category_Structure $schild
    * @return Category_Structure|null
    * @todo Odstranit parametr!!!
    * @note parametr se bude odstraňovat
    */
   public function nextChild(Category_Structure $schild = null)
   {
      if(!$schild){
         $parent = $this->getParent();
         if($parent){
            $childs = $parent->getChildrens();
            reset($childs);
            while ($child = current($childs)) {
               if ($child->getId() == $this->getId()) {
                  if(@next($childs)){
                     return current($childs);
                  } else {
                     return false;
                  }
               }
               @next($childs);
            }
         }
      } else {
         reset($this->childrens);
         while ($child = current($this->childrens)) {
            if ($child->getId() == $schild->getId()) {
               if(@next($this->childrens)){
                  return current($this->childrens);
               } else {
                  return false;
               }
            }
            @next($this->childrens);
         }
      }
      return false;
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
      $struct = clone self::$structure;
      $struct->cleanUpCategory();
      file_put_contents(AppCore::getAppCacheDir()."struct_before".date("Y-m-d_G:i").".tmp", $struct);

      $data = serialize($struct);
      Model_Config::setValue('CATEGORIES_STRUCTURE', $data, Model_Config::TYPE_SER_DATA);

      self::clearCache();
      //self::loadStruct(); // vytváři nekonzistenci cache
   }

   /**
    * Metoda vrací strukturu kategorií
    * @param bool $admin -- true pro vrácení struktury admin menu
    * @return Category_Structure
    */
   public static function getStructure($type = self::VISIBLE_ONLY)
   {
      if(!self::$structure){
         self::loadStruct();
      }
      switch ($type) {
         case self::ALL:
            return self::$structure;
            break;
         case self::VISIBLE_ONLY:
         default:
            return self::$structureVisible;
            break;
      }
   }

   /**
    * Vrací výchozí kategorii stránek
    * @return Category/null
    */
   public static function getDefaultCategory()
   {
      return self::$defaultCategory;
   }

   /**
    * Metoda načte strukturu a doplní objekty kategorií
    */
   protected static function loadStruct()
   {
      // kešování na stejném serveru stojí za hovno
      if( (self::$structure = Cache::get(self::getCacheKey().'_base')) == false
         || (self::$structureVisible = Cache::get(self::getCacheKey().'_visible')) == false ){
         self::$structure = unserialize(VVE_CATEGORIES_STRUCTURE);
         $modelCats = new Model_Category();
         $cats = $modelCats->getCategoryList(true);

         self::$structure->setCategoriesObjects($cats);

         self::$structureVisible = clone self::$structure;
         self::$structureVisible->clearNonVisible();
         Cache::set(self::getCacheKey().'_base', clone self::$structure);
         Cache::set(self::getCacheKey().'_visible', clone self::$structureVisible);
      }
   }

   public static function clearCache()
   {
      Cache::delete(self::getCacheKey().'_base');
      Cache::delete(self::getCacheKey().'_visible');
   }

   protected function setCategoriesObjects($catArray, $level = 0)
   {
      $childs = $this->getChildrens();
      foreach ($childs as $key => $child) {
         if(isset ($catArray[$child->getId()])) {
            $child->catObj = new Category(null, false, $catArray[$child->getId()]);
            if($child->catObj->getDataObj()->{Model_Category::COLUMN_PRIORITY} > self::$defaultCategoryPriority){
               self::$defaultCategory = $child->catObj;
               self::$defaultCategoryPriority = $child->catObj->getDataObj()->{Model_Category::COLUMN_PRIORITY};
            }
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
//      $this->cleanUpCategory();
      return array('level', 'id', 'idParent', 'catObj', 'childrens');
   }
   
   /**
    * Klonování kvůli referencím
    */
   public function __clone()
   {
      foreach ($this as $key => $value) {
         $this->childrens[$key] = clone $this->childrens[$key];
      }
      if(is_object($this->catObj)){
         $this->catObj = clone $this->catObj;
      }
   }

   public function cleanUpCategory()
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
   
    /**
    * Projde strom a vrátí v 1D poli hodnoty funkce/property/názvu jako hodnotu, ve funkci lze použít objekt typu Category 
    * @param string $delimiter
    * @param Closure $closureName - funkce na hodnotu v poli
    * @param Closure $closureKey - funkce na klíč v poli
    * @param strin $_prefix - internal
    * @return array
    */
   public function getCategoryPaths($delimiter, $closureName = null, $closureKey = null, $_prefix = null)
   {
      /* pole id => název */
      $catsArrReturn = array();
      $newPrefix = null;
      if($this->id != 0){
         if(is_null($closureName)){
            $name = $this->getCatObj()->getName();
         } else if(is_string($closureName)){
            $name = $this->getCatObj()->getDataObj()->{$closureName};
         } else if($closureName instanceof Closure){
            $name = $closureName($this->getCatObj());
         }
         $newPrefix = $_prefix.($_prefix != null ? $delimiter : null).$name;
         
         if(is_null($closureKey)){
            $key = $this->getCatObj()->getId();
         } else if(is_string($closureKey)){
            $key = $this->getCatObj()->getDataObj()->{$closureKey};
         } else if($closureKey instanceof Closure){
            $key = $closureKey($this->getCatObj());
         }
         if($key != false && $name != false){
            $catsArrReturn[(string)$key] = $newPrefix;
         }
      }
      
      foreach ($this as $child) {
         if($closureName instanceof Closure){
            $closureName->bindTo($child);
         }
         if($closureKey instanceof Closure){
            $closureKey->bindTo($child);
         }
         $catsArrReturn += $child->getCategoryPaths($delimiter, $closureName, $closureKey, $newPrefix);
      }
      return $catsArrReturn;
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
