<?php
/**
 * Třída slouží pro validaci emailů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: validator_url.class.php 639 2009-07-07 20:59:50Z jakub $ VVE 6.0.5 $Revision: 639 $
 * @author        $Author: jakub $ $Date: 2009-07-07 22:59:50 +0200 (Út, 07 čec 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-07-07 22:59:50 +0200 (Út, 07 čec 2009) $
 * @abstract 		Třída pro validaci emailové adresy
 * @see           http://fightingforalostcause.net/misc/2006/compare-email-regex.php
 */
class Validator_EMail extends Validator {

   public function validate() {
      if(!preg_match("/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i", $this->values)) {
         $this->isValid = false;
      } else {
         $this->isValid = true;
      }
   }
}
?>