<?php
/**
 * Třída pro vytvoření objektu nástroje (tool) pro toolbox verze 2
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Rozhraní pro tvorbu nástroje toolboxu
 */

class Template_Toolbox2_Tool implements Template_Toolbox2_Tool_Interface {
   /**
    * Název nástroje
    * @var string
    */
   protected $name = null;

   /**
    * Název ikony
    * @var string
    */
   protected $icon = null;

   /**
    * Odkaz akce
    * @var Url_Link
    */
   protected $action = null;

   /**
    * Název nástroje
    * @var string
    */
   protected $label = null;

   /**
    * Popisek nástroje
    * @var astring
    */
   protected $title = null;

   /**
    * Další odesílané prvky
    * @var array
    */
   protected $aditionalSendValus = array();

   /**
    * Proměnná s potvrzovací zprávou
    * @var string
    */
   protected $confirmMessage = null;
   
   /**
    * označení důležitých položek
    * @var bool
    */
   protected $important = false;

   /**
    * Konstruktor nástroje
    * @param string $name -- název nástroje
    * @param string $label -- popisek nástroje
    * @param Url_Link $action -- odkaz nástroje (pokud je null použije se aktuální)
    */
   public function  __construct($name, $label, $action = null) {
      if($action == null) $action = new Url_Link();
      $this->action = $action;
      $this->name = $name;
      $this->label = $label;
      $this->title = $this->label;
   }

   /**
    * Metoda vrací název nástroje
    * @return string 
    */
   public function getName() {
      return $this->name;
   }

   /**
    * Metoda nastaví akci
    * @param Url_Link $title -- titulek nástroje
    * @return Template_Toolbox2_Tool
    */
   public function setAction(Url_Link $action) {
      $this->action = $action;
      return $this;
   }

   /**
    * Metoda vrací akci
    * @return Url_Link/string
    */
   public function getAction() {
      return $this->action;
   }

   /**
    * Metoda nastaví název nástroje
    * @param string $label -- název nástroje
    * @return Template_Toolbox2_Tool
    */
   public function setLabel($label) {
      $this->label = $label;
      return $this;
   }

   /**
    * Metoda vrací label
    * @return label
    */
   public function getLabel() {
      return $this->label;
   }

   /**
    * Metoda nastaví titulek
    * @param string $title -- titulek nástroje
    * @return Template_Toolbox2_Tool
    */
   public function setTitle($title) {
      $this->title = $title;
      return $this;
   }

   /**
    * Metoda vrací titulek
    * @return string
    */
   public function getTitle() {
      return $this->title;
   }

   /**
    * Metoda nastaví ikonu
    * @param string $icon -- název ikony
    * @return Template_Toolbox2_Tool
    */
   public function setIcon($icon) {
      $this->icon = $icon;
      return $this;
   }

   /**
    * Metoda vrací název ikony
    * @return string
    */
   public function getIcon() {
      // remove translate icon in 8.2
      return Template_Toolbox2::translateIcon($this->icon);
   }
   
   /**
    * Metoda nastaví důležitou položku
    * @param bool $important -- true pro důležitou položku
    * @return Template_Toolbox2_Tool
    */
   public function setImportant($important) {
      $this->important = $important;
      return $this;
   }

   /**
    * Metoda vrací jestli je nástroj důležitý
    * @return bool
    */
   public function getImportant() {
      return $this->important;
   }

   /**
    * Metoda přidá další proměnné, které budou odeslány spolu s formulářem
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    * @return Template_Toolbox2_Tool
    */
   public function setSubmitValue($name, $value) {
      $this->aditionalSendValus[$name] = $value;
      return $this;
   }

   /**
    * Metoda vrátí další hodnoty odeslané s formem
    * @return array
    */
   public function getSubmitValues() {
      return $this->aditionalSendValus;
   }

   /**
    * Metoda nastaví potvrzovací zprávu
    * @param string $msg
    * @return Template_Toolbox2_Tool_PostConfirm
    */
   public function setConfirmMeassage($msg) {
      $this->confirmMessage = $msg;
      return $this;
   }

   /**
    * Metoda vrací potvrzovací zprávů
    * @return string
    */
   public function getConfirmMessage(){
      return $this->confirmMessage;
   }

   /*
    * Magické metody
    */
   /**
    * Magická metoda pro nasatvení nedefinovaného atributu. Nastavuje další hodnoty pro odeslání
    * @param string $name -- název atributu
    * @param int/string $value -- hodnota atributu
    */
   public function  __set($name, $value) {
      $this->aditionalSendValus[$name] = $value;
   }

   /**
    * Magická metoda pro přístup k nedefinovaným atributům třídy. Vrací přidané elementy
    * @param string $name -- název elementu
    * @return int/string -- vrací hodnotu elementu
    */
   public function  &__get($name) {
      if(isset ($this->aditionalSendValus[$name])) return $this->aditionalSendValus[$name];
      $null = null;
      return $null;
   }
}
