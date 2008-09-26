<?php
/*
 * Třída modelu s Novinkou
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class NewsDetail extends Models {
	public $newsArray = array();

	public $idNews = null;
	
	public $newsDefaultLabel = null;
	public $linkToBack = null;
}

?>