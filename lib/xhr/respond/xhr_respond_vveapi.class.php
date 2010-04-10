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
   /**
    * Metoda vytvoří řetězec s výsledky operace
    * @return string -- výsledek operace ve struktuře (viz. výše)
    */
   public function  __toString() {
      // spojení messages
      $retData = $this->data;
      $retData['infomsg'] = AppCore::getInfoMessages()->getMessages();
      $retData['errmsg'] = AppCore::getUserErrors()->getMessages();

      Template_Output::setOutputType('json');
      return json_encode($retData);
   }
}
?>
