<?
/**
 * Třída pro obsluhu vyjímek modulů v jádře
 * Třída rozšiřuje třídu Exception
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu chyb modulů
 */
class CoreException extends Exception {
   public function  __construct($message = null, $code = null) {
      parent::__construct($message, $code);
   }
}
?>
