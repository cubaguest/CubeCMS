<?php
/**
 * Interface filtru elementu formuláře
 * Interface definující základní vlastnosti filtru elemntu formuláře
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision:  $
 * @author        $Author:  $ $Date: $
 *                $LastChangedBy:  $ $LastChangedDate: $
 * @abstract      Třída pro tvorbu filtrů elemntů formuláře
 */
interface Form_Filter_Interface {
   /**
    * Metoda provede filtrování dat alamentu
    */
   public function filer();
}
?>
