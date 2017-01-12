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

   /**
    * Metoda vygeneruje řádek pro formulář
    * @return string
    */
   public function render(Form $form);
   
   /**
    * Renderuje celou skupinu elementů
    * @param type $param
    */
   public function createGroup($name, $params, $formElements);
   
   /**
    * Renderuje řádek elementu
    * @param type $param
    */
   public function createRow($name, $formElements);
   
   /**
    * Renderuje popisek k prvku
    * @param type $param
    */
   public function createLabel($element);
   
   /**
    * Renderuje ovládací prvek
    * @param type $param
    */
   public function createControl($element);
   
   /**
    * Renderuje ovládací prvek
    * @param Html_Element $param
    */
   public function createForm();
   
}
