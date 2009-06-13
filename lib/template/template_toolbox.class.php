<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem).
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template.class.php 576 2009-04-15 10:52:59Z jakub $ VVE3.9.4 $Revision: 576 $
 * @author        $Author: jakub $ $Date: 2009-04-15 10:52:59 +0000 (St, 15 dub 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-04-15 10:52:59 +0000 (St, 15 dub 2009) $
 * @abstract 		Třída pro obsluhu šablony
 */

class Template_Toolbox extends Template {
   /**
    * Ikona pro přidání
    */
   const ICON_ADD = 'icon_add.png';

   /**
    * Ikona pro editaci
    */
   const ICON_EDIT = 'icon_edit.png';

   /**
    * Ikona pro smazání
    */
   const ICON_REMOVE = 'icon_remove.png';

   /**
    * Pole s nástroji
    * @var array
    */
   private $tools = array();

   /**
    * Náhodné číslo, používá se pro odlišení toolboxů u modulů na stránce
    * @var integer
    */
   private $randomSeek = null;

   /**
    * Konstruktor, vytvoří základní šablonu pro nástroje.
    */
   public function  __construct() {
      parent::__construct();
      $this->randomSeek = rand();
      $this->createTemplate();
   }

   /**
    * Metoda vytvoří šablonu
    */
   private function createTemplate() {
      $this->addTplFile("toolbox.phtml", true);
      $this->addJsPlugin(new JsPlugin_JQuery());
   }

   /**
    * Metoda přidá do šablony nástroj
    * @param string $name -- název nástroje
    * @param string $value -- hodnota prvku
    * @param Links/string $targetLink -- odkaz, kam nástroj má přejít
    * @param string $title -- popis nástroje
    * @param string $icon -- název ikony nebo konstanta třídy s ikonou
    * @param string $nameHidden -- pokud má obsahovat i zkrytý prvek, např. id
    * @param string $nameValue -- hodnota zkrytého prvku
    * @param integer $index -- index, pokud má být nástroj řazen
    * @return TplToolbox -- sám sebe vrací
    */
   public function addTool($name, $value, $targetLink, $title = null, $icon = self::ICON_ADD,
      $nameHidden = null, $valueHidden = null, $confirmMessage = null, $index = null) {

      $tool = array();
      $tool['name'] = $name;
      $tool['value'] = $value;
      if($title != null){
         $tool['title'] = $title;
      } else {
         $tool['title'] = $value;
      }

      $tool['link'] = $targetLink;
      $tool['icon'] = $icon;
      $tool['nameHidden'] = $nameHidden;
      $tool['valueHidden'] = $valueHidden;
      $tool['message'] = $confirmMessage;
      
      if($index == null){
         array_push($this->tools, $tool);
      } else {
         $this->tools[$index] = $tool;
      }
      return $this;
   }

   public function getTools() {
      return $this->tools;
   }

   /**
    * Metoda vrací náhodné číslo vygenerovaného objektu
    * @return int -- náhodné číslo toolboxu
    */
   public function getRandSeek() {
      return $this->randomSeek;
   }
}
?>