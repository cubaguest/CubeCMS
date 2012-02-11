<?php
/**
 * Abstraktní třída pro práci s panely.
 * Základní třída pro tvorbu tříd panelů jednotlivých modulu. Poskytuje prvky
 * základního přístu jak k vlastnostem modelu tak pohledu. Pomocí této třídy
 * se také generují šablony panelů.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: panel.class.php -1   $ VVE3.9.4 $Revision: -1 $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Abstraktní třída pro práci s panely
 * @todo				Není implementována práce s chybami
 */

class Panel_Obj {
   const DATA_DIR = 'panels';

   const ICONS_DIR = 'icons';

   const PANEL_DEFAULT_TPL = 'panel.phtml';

   /**
    * Pole s parametry panelu
    * @var array
    */
   private $panelParams = array();

   /**
    * Název panelu
    * @var string
    */
   private $panelName = null;

   /**
    * id panelu
    * @var int
    */
   private $panelId = 0;

   /**
    * Ikona panelu
    * @var string
    */
   private $panelIcon = null;

   /**
    * Obrázek pozadí panelu
    * @var string
    */
   private $panelImg = null;

   /**
    * Konstruktor
    * @param Category $category -- obejkt kategorie
    * @param Routes $routes -- objekt cest pro daný modul
    */
   function __construct($panelData) {
      if($panelData->{Model_Panel::COLUMN_PARAMS} != null){
         $this->panelParams = unserialize($panelData->{Model_Panel::COLUMN_PARAMS});
      }
      if((string)$panelData->{Model_Panel::COLUMN_NAME} != null){
         $this->panelName = $panelData->{Model_Panel::COLUMN_NAME};
      } else {
         $this->panelName = $panelData->{Model_Category::COLUMN_CAT_LABEL};
      }
      $this->panelIcon = $panelData->{Model_Panel::COLUMN_ICON};
      $this->panelId = (int)$panelData->{Model_Panel::COLUMN_ID};
      $this->panelImg = $panelData->{Model_Panel::COLUMN_IMAGE};
   }

   /**
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function getParam($param, $defaultParam = null) {
      if(isset($this->panelParams[$param])){
         return $this->panelParams[$param];
      } else {
         return $defaultParam;
      }
   }

   /**
    * Metoda vrací všechny parametry panelu
    * @return array
    */
   public function getParams()
   {
      return $this->panelParams;
   }
   
   /**
    * Metoda uloží všechny parametry panelu
    * @param array
    */
   public function setParams($params)
   {
      $this->panelParams = $params;
      $model = new Model_Panel();
      $rec = $model->record($this->getId());
      $rec->{Model_Panel::COLUMN_PARAMS} = serialize($this->panelParams);
      $model->save($rec);
   }

   
   /**
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function setParam($param, $value) {
      $this->panelParams[$param] = $value;
   }

   /**
    * Metoda vrací id kategorie
    * @return integer -- id kategorie
    */
   public function getId() {
      return (int)$this->panelId;
   }

   /**
    * Metoda vrací id kategorie
    * @return integer -- id kategorie
    */
   public function getName() {
      return $this->panelName;
   }

   /**
    * Metoda vrací id kategorie
    * @return integer -- id kategorie
    */
   public function getIcon() {
      return $this->panelIcon;
   }

   /**
    * Metoda vrací obrázek panelu
    * @return string -- název souboru s obrázkem panelu nebo null
    */
   public function getBackImage() {
      return $this->getImage();
   }

   /**
    * Metoda vrací obrázek panelu
    * @return string -- název souboru s obrázkem panelu nebo null
    */
   public function getImage() {
      return $this->panelImg;
   }

   /**
    * Metodda vrací adresář s ikonami panelů
    * @return string
    */
   final static function getIconDir($http = true) {
      if($http){
         return Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.self::DATA_DIR
            .URL_SEPARATOR.self::ICONS_DIR.URL_SEPARATOR;
      } else {
         return AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::DATA_DIR
            .DIRECTORY_SEPARATOR.self::ICONS_DIR.DIRECTORY_SEPARATOR;
      }
   }

   /**
    * Metoda vrcí adresář s pozadím panelu
    * @return string
    */
   final static function getBackImgDir($http = true) {
      return self::getImgDir($http);
   }

   /**
    * Metoda vrcí adresář s obrázkem panelu
    * @return string
    */
   final static function getImgDir($http = true) {
      if($http){
         return Url_Request::getBaseWebDir().VVE_DATA_DIR.URL_SEPARATOR.self::DATA_DIR
            .URL_SEPARATOR;
      } else {
         return AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::DATA_DIR
            .DIRECTORY_SEPARATOR;
      }
   }
}
?>