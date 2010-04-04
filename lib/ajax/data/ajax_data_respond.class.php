<?php
/**
 * Třída pro jednodušší práci s odpověďmi na ajax požadavky pro klienty.
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id$ VVE 6.0.3 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro tvorbu jaxových odpovědí pro klienta
 */
class Ajax_Data_Respond {
   /**
    * Status vrácený k uživateli
    * @var string
    */
   private $status = false;

   /**
    * Data přenesená zpšt ke klientovi
    * @var array
    */
   private $data = null;

   /**
    * Typ odpovědi na požadavek (json, xml, ...)
    * @var string
    */
   private $respondType = null;

   /**
    * Konstruktor vytváří základní odkaz
    */
   public function  __construct($type = 'json') {
      switch ($type) {
         case 'json':
         default:
            
            break;
      }
      // přenasatvení info msg protože není třeba je ukládat
      AppCore::getInfoMessages()->setSaveStatus(false);
   }


   public function setStatus($status){
      $this->status = $status;
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
    */
   public function  __toString() {
      // spojení messages
      $retData = array();
      $retData['infomsg'] = AppCore::getInfoMessages()->getMessages();
      $retData['errmsg'] = AppCore::getUserErrors()->getMessages();
      $retData['data'] = $this->getData();
      $retData['status'] = $this->status;

      switch ($this->respondType) {
         case 'json':
         default:
            Template_Output::setOutputType('json');
            return json_encode($retData);
            break;
      }
      return null;
   }
}
?>
