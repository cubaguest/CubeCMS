<?php
/**
 * Třída slouží pro validaci emailů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: validator_url.class.php 639 2009-07-07 20:59:50Z jakub $ VVE3.9.4 $Revision: 639 $
 * @author        $Author: jakub $ $Date: 2009-07-07 22:59:50 +0200 (Út, 07 čec 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-07-07 22:59:50 +0200 (Út, 07 čec 2009) $
 * @abstract 		Třída pro validaci formulářových prvků
 */
class Validator_EMail extends Validator {

   public function validate() {
      $name = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
      $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
      if(!eregi("^$name+(\\.$name+)*@($domain?\\.)+$domain\$", $this->values)) {
         $this->isValid = false;
      }
   }
}
?>