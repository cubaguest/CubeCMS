<?php
/*
 * Třída modelu s listem Novinek
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class GaleryList extends Models {
	public $allGaleryArray = array();
	
	public $sectionName = null;
	public $sectionId = null;
	
	public $countOfPhotos = null;
	
	public $dirToImages = null;
	public $dirToMediumImages = null;
	public $dirToSmallImages = null;
	
	public $scroll = null;
	
	public $linkToBack = null;
	
	public $linkToAddGalery = null;
	public $linkToAddSection = null;
	public $linkToAddPhotos = null;

	public $linkToEdit = null;
	
	public $inSection = false;
	
}

?>