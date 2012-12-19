<?php
/**
 * Třída pro volbu šablony vzhledu
 * Třída slouží pro vybrání šablony pro vzhled
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4.5 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro výběr šablon
 * @see           http://interval.cz/clanky/rss-20/
 */

class Component_ViewTpl extends Component {
   const TPLS_LIST_FILE = 'tpls.php';
   const TPLS_LIST_NAME = 'main';

   /*
    * Parametry
    */
   const PARAM_MODULE = 'module';
   const PARAM_LIST_TYPE = 'list';


   /**
    * Pole s prvky kanálu
    * @var array
    */
   private $tpls = null;

   /**
    * Pole s konfiguračními hodnotami
    * @var array
    */
   protected $config = array('listfile' => self::TPLS_LIST_FILE,
      self::PARAM_LIST_TYPE => self::TPLS_LIST_NAME,
      self::PARAM_MODULE => 'text'
      );

   public function  __set($name, $value) {
      $this->tpls[$name] = $value;
   }
   
   public function  &__get($name) {
      if(isset ($this->tpls[$name])){
         return $this->tpls[$name];
      }
      $arr = array();
      return $arr;
   }

   public function  __isset($name) {
      return isset ($this->tpls[$name]);
   }

   /**
    * Metoda provede načtení šalon
    */
   private function loadTpls() {
      if(is_array($this->tpls)) return; // pokud je pole došlo k načtení
      $this->tpls = array();
      if(!isset ($this->{$this->getConfig(self::PARAM_LIST_TYPE)})
         AND file_exists(Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
         .$this->getConfig(self::PARAM_MODULE).DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){ // soubor z faces
         include Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$this->getConfig(self::PARAM_MODULE).DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
      }
      if(!isset ($this->{$this->getConfig(self::PARAM_LIST_TYPE)})
         AND file_exists(Template::faceDir(true).AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
         .$this->getConfig(self::PARAM_MODULE).DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){ // soubor z faces parent
         include Template::faceDir(true).AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$this->getConfig(self::PARAM_MODULE).DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
      }
      if(!isset ($this->{$this->getConfig(self::PARAM_LIST_TYPE)}) AND empty ($this->tpls)
         AND file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
         .$this->getConfig(self::PARAM_MODULE).DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){ // soubor s modulu
         include AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
         .$this->getConfig(self::PARAM_MODULE).DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
      }
   }

   /**
    * Metoda přeparsuje šablony a jejich názvy doplní do správného jazyka
    * @param string $listName -- název seznamu
    */
   private function parseTplsLabels($listName = self::TPLS_LIST_NAME) {
      if(isset ($this->{$listName.'Parsed'})){ // kontrola existence parsovaných
         return;
      }
      if(isset ($this->{$listName})){
         $this->{$listName.'Parsed'} = array();
         foreach ($this->{$listName} as $file => $labels) {
            if(is_string($labels)){ // popisek je string
               $this->{$listName.'Parsed'}[$file] = $labels.' ('.$file.')';
            } else if(isset ($labels[Locales::getLang()])){
               $this->{$listName.'Parsed'}[$file] = $labels[Locales::getLang()].' ('.$file.')';
            } else {
               foreach (Locales::getAppLangs() as $lang) {
                  if(isset ($labels[$lang])){
                     $this->{$listName.'Parsed'}[$file] = $labels[$lang].' ('.$file.')';
                  }
               }
               // pokud nebyl žádný jazyk vybrán protože se neshodují vezme se první popisek co tam je
               if(is_array($this->{$listName.'Parsed'}[$file])){
                  $this->{$listName.'Parsed'}[$file] = reset($labels).' ('.$file.')';
               }
            }
         }
      }
   }

   /**
    * Metoda vrátí seznam šablon s popisem
    * @return array ('text.phtml' => 'Výchotí šablona')
    */
   public function getTpls($listName = null) {
      if($listName === null) $listName = $this->getConfig(self::PARAM_LIST_TYPE);
      $this->loadTpls();
      $this->parseTplsLabels($listName);
      return $this->{$listName.'Parsed'};
   }

   /**
    * Metoda pro výpis komponenty
    */
   public function mainController() {}

   /**
    * Metoda pro výpis komponenty
    */
   public function mainView() {}
}
?>
