<?php
/*
 * Třída modelu s listem Novinek
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class ChangesList extends Models {
	public $scroll = null;
	
	public $allChangesArray = array();
	
	public $changessTableOrder = array();
	
	public $changeSearchArray = array();
}

?>