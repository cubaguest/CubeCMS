<?php
/*
 * Třída modelu s listem Novinek
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class TextDetail extends Models {
	public $text = null;
	public $textEdit = array();
	public $link = null;
	
	public $inDb = false;
	
	public $files = null;
	public $images = null;
}

?>