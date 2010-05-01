<?php
/**
 * Třída slouží pro validaci url adres
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id$ VVE 6.0.5 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci URL adresy
 */
class Validator_Url extends Validator {
	/**
	 * Metoda kontroluje správnost url adresy
	 * @return boolean -- vrací true pokud se jedná o url adresu
	 */
	public function validate() {
		return true;
	}
}
?>