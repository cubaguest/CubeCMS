<?php
/*
 * Třída modelu se sekcí
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class addPhoto extends Models {
	public $sectionArray = array();
	public $galeryArray = array();
	public $photoArray = array();
	
	public $newSectionArray = array();
	
	public $newGaleryArray = array();
	
	public $idSelectedSection = null;
	public $idSelectedGalery = null;
	
	public $linkToBack = null;
}

?>