<?php 
/**
 * Třída pro obsluhu vyjímek pro načítání třídy
 * Třída rozšiřuje třídu Exception
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.1 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu chyb načítání tříd
 */
class BadClassException extends Exception {
   public function  __construct($message = null, $code = null) {
      parent::__construct($message, $code);
   }
}
?>
