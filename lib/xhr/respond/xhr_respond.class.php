<?php
/**
 * Třída pro jednodušší práci s odpověďmi na XHR požadavky pro klienty.
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.3 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro tvorbu XHR odpovědí pro klienta
 */
class XHR_Respond {
   /**
    * Data přenesená zpšt ke klientovi
    * @var array
    */
   protected $data = null;

   /**
    * Konstruktor vytváří základní odkaz
    */
   public function  __construct() {
      // přenasatvení info msg protože není třeba je ukládat
      AppCore::getInfoMessages()->changeSaveStatus(false);
   }


   public function setData($data){
      $this->data = $data;
   }

   public function getData(){
      return $this->data;
   }


   /**
    * Metoda vytvoří řetězec s výsledky operace
    * @return string -- výsledek operace ve struktuře (viz. výše)
    * @todo -- dořešit výchozí výstup
    */
   public function  __toString() {
      // spojení messages
      $retData = $this->data;
      $retData['infomsg'] = AppCore::getInfoMessages()->getMessages();
      $retData['errmsg'] = AppCore::getUserErrors()->getMessages();
   }

   /**
    * Metoda provede render odpovědi
    */
   public function renderRespond() {
      print $this;
   }
}
?>
