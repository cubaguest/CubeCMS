<?php

/**
 * Třída dekorátoru formuláře (tato dřída upsahuje implementaci dekorátoru pomocí
 * tabulek. Jejím děděním lze dekorátor upravit)
 *
 * @copyright  	Copyright (c) 2011 Jakub Matas
 * @version    	$Id: $ VVE 7.1 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída dekorátoru pro formulář
 */
interface Form_Decorator_Interface {
   /**
    * Konstruktor vytvoří obal
    * @param array $decoration -- pole s nastavením pro dekorátor
    *
    * <p>
    * prvky<br/>
    * <ul>
    * <li>'wrap' -- obal elementů (table)</li>
    * <li>'rowwrap' -- obal řádku (tr)</li>
    * <li>'labelwrap' -- obal popisku (th)</li>
    * <li>'ctrlwrap' -- obal kontrolního prvku (td)</li>
    * </ul>
    * </p>
    */
   public function __construct($decoration = null);

   public function addElement(Form_Element $element);

   /**
    * Metoda vygeneruje řádek pro formulář
    * @return Html_Element -- objekt Html_elementu
    */
   public function render($createGroupClass = false);

   /**
    * Metoda nastaví název skupiny
    * @param string $name -- tag legend
    * @return Form_Decorator 
    */
   public function setGroupName($name);

   /**
    * Metoda nastaví popisek skupiny
    * @param string $text -- popisek uvnitře fieldsetu
    * @return Form_Decorator
    */
   public function setGroupText($text);

   /**
    * Magická metoda vrátí obsah dekorátoru jako řetězec
    * @return string (nejčastěji fieldset)
    */
   public function __toString();

}
?>
