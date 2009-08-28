<?php
/**
 * Interface elementu formuláře
 * Interface definující základní vlastnosti elemntu formuláře
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: form.class.php 630 2009-06-14 15:52:19Z jakub $ VVE 5.1.0 $Revision: 630 $
 * @author        $Author: jakub $ $Date: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 * @abstract      Třída pro obsluhu formulářů
 */
interface Form_Element_Interface {
   /**
    * Metoda provede naplnění elementu
    * @param string $method -- typ metody přes kterou je prvek odeslán (POST|GET)
    */
   public function populate($method = 'post');

   /**
    * Metoda vrátí jestli je element validní
    */
   public function isValid();

   /**
    * Metoda vrátí jestli byl element naplněn validní
    */
   public function isPopulated();


}
?>
