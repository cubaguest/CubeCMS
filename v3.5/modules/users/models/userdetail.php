<?php
/*
 * Třída modelu s detailem zástupce
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class UserDetail extends Models {
	public $userDetailArray = array();
	public $linkToEdit = null;
	public $linkToBack = null;

	public $groupsArray = array();
	
	public $changes = null;
}

?>