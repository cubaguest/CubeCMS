<?php
/*
 * Třída modelu s Novinkou
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class PhotoEditDetail extends Models {
	public $photoArray = array();

	public $idPhoto = null;
	
	public $photoFile = null;
	public $photoName = null;
	
	public $dirToSmallImages = null;
	
	public $linkToBack = null;
	
//	public $scroll = null;

//	public $linkToAddGalery = null;
//	public $linkToAddSection = null;
//	public $linkToAddPhotos = null;
}

?>