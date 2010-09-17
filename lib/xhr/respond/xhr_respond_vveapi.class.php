<?php
/**
 * Třída pro jednodušší práci s odpověďmi na XHR požadavky pro klienty ve formátu JSON.
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.3 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro tvorbu jaxových odpovědí pro klienta ve formátu JSON.
 */
class XHR_Respond_VVEAPI extends XHR_Respond {
   protected $outputType = 'json';

   /**
    * Metoda vytvoří řetězec s výsledky operace
    * @return string -- výsledek operace ve struktuře (viz. výše)
    */
   public function  __toString() {
      // spojení messages
      $retData = $this->data;
      $retData['infomsg'] = AppCore::getInfoMessages()->getMessages();
      $retData['errmsg'] = AppCore::getUserErrors()->getMessages();
      if(VVE_DEBUG_LEVEL > 0 AND !CoreErrors::isEmpty()){
         $coreErrors = (array)CoreErrors::getErrorsInArrayForPrint();
         // převedení core errors na stringy
         $retData['errmsg'] = array_merge($retData['errmsg'], $coreErrors);
//         CoreErrors::eraseErrors(); // není třeba je již vypisovat
      }
      return json_encode($retData);
   }
}
?>
