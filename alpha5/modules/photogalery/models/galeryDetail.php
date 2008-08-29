<?php
/*
 * Třída modelu s Novinkou
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class GaleryDetail extends Models {
	public $galeryArray = array();

	public $idGalery = null;
	
	public $galeryInfo = null;
	
	public $dirToImages = null;
	public $dirToSmallImages = null;
	
	public $linkToBack = null;
	
//	public $linkToAddGalery = null;
//	public $linkToAddSection = null;
	public $linkToAddPhotos = null;
	public $linkToEditGalery = null;
}

?>