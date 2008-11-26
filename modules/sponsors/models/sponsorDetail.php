<?php
/*
 * Třída modelu s detailem Sponzora
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class SponsorDetail extends Models {
	public $sponsorArray = array();
	
	public $sponsorUrl = null;
	
	public $idSponsor = null;
	public $sponsorDefaultName = null;
	public $sponsorImageFile = null;
	public $dirToImages = null;
}
?>