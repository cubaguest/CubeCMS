<?php
/**
 * Třída pro obsluhu html elementů
 * Třída slouží pro práci s jednotlivými html elemnty v šabloně, Jejich správné
 * a validní vykreslení a jednoduchou práci s elementy. Do elementu se dají vkládat
 * další instance a potomci této třídy. Při vykreslení jsou vykresleni také
 * podřízené instance této třídy.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 5.2.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu html elementů a jejich vykreslení
 */

class Html_Element {
/**
 * Název samotného elementu
 * @var string
 */
   private $elementName = null;

   /**
    * Jestli se jedná o element se dvěma tagy nebo inline (např. input)
    * @var bool
    */
   private $isInline = false;

   /**
    * Pole s třídami elementu
    * @var array
    */
   private $classes = array();

   /**
    * Pole s atributy elementu (kromě tříd, id, action, ...)
    * @var array
    */
   private $attribs = array();

   /**
    * Obsah elementu
    * @var string
    */
   protected $content = null;

   /**
    * Pole s elementy, které jsou inline (nepárové)
    * @var array
    */
   private $inlineElements = array('br','hr', 'base', 'basefont', 'area',
      'col', 'colgroup', 'frame', 'img', 'input', 'meta', 'param',
      'spacer');

   /**
    * Konstruktor pro vatvoření html tagu
    * @param string $name -- název tagu
    * @param string $content -- obsah elementu
    */
   public function  __construct($name, $content = null) {
      $this->isInline = in_array($name, $this->inlineElements);
      $this->elementName = $name;
      $this->content = $content;
   }

   /**
    * Magická metoda pro převod na řetězec
    * @return string -- tag určený pro výpis
    */
   public function  __toString() {
      $elem = $this->__toStringBegin();
      $elem .= $this->__toStringContent();
      $elem .= $this->__toStringEnd();
      return $elem;
   }

   /**
    * Metoda vrátí začátek elementu
    */
   public function __toStringBegin() {
      $string = "<".$this->elementName;
      // render atributů
      foreach ($this->attribs as $name => $value) {
         $string .= " ".$name."=\"$value\"";
      }
      // render tříd
      if(!empty ($this->classes)) {
         $string .= " class=\"";
         foreach ($this->classes as $class) {
            $string .= $class." ";
         }
         $string = substr($string, 0, strlen($string)-1);
         $string .= "\"";
      }
      if($this->isInline) {
         $string .= " />";
      } else {
         $string .= ">";
      }
      if(!$this->isEmpty()){
         $string .= "\n";
      }
      return $string;
   }

   /**
    * Metoda vrátí obsah elementu
    */
   public function __toStringContent() {
      return $this->content;
   }

   /**
    * Metoda vrátí konec elementu
    */
   public function __toStringEnd() {
      $string = null;
      // inline nebo block element
      if(!$this->isInline) {
         // ukončovací tag
         $string .= "</".$this->elementName.">\n";
      }
      return $string;
   }

   /**
    * Metoda provede render prvku
    */
   public function render() {
      print ($this);
   }

   /**
    * Metoda nastaví zadaný atribnut elementu (id, action, link, atd). Pokud je
    * hodnota atributu nastavena na null je atribut odstraněn
    * @param string $name -- název atributu
    * @param mixed $value -- (option) hodnota atributu
    * @return Html_Element -- vrací sám sebe
    */
   public function setAttrib($name, $value = null) {
      if($value == null){
         unset ($this->attribs[$name]);
      } else {
         $this->attribs[$name] = $value;
      }
      return $this;
   }

   /**
    * Metoda vrací hodnotu zadaneého atributu
    * @param string $name -- název atributu
    * @return mixed -- hodnota atributu
    */
   public function getAttrib($name) {
      return $this->attribs[$name];
   }

   /**
    * Metoda přidá zadanou třídu do elementu
    * @param string $class -- název třídy
    */
   public function addClass($class) {
      array_push($this->classes, $class);
   }

   /**
    * Metoda přidá potomka elementu (objekt elemeentu)
    * @param Html_Element $content -- objekt elementu
    */
   public function addContent($content) {
      $this->content .= (string)$content;
   }

   /**
    * Metoda vymaže obsah elementu
    */
   public function clearContent() {
      $this->content = null;
   }

   /**
    * Metoda vrací jestli je prvek prázdný - nemá žádný obsah
    * @return boolean
    */
   public function isEmpty() {
      if($this->content == null){
         return true;
      }
      return false;
   }
}
?>