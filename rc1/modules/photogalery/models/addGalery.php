<?php
/*
 * Třída modelu se sekcí
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class addGalery extends Models {
	public $sectionArray = array();
	
	public $newSectionArray = array();
	
	public $newGaleryArray = array();
	
	public $idSelectedSection = null;
	
	public $linkToBack = null;
}

?>