<?php
/*
 * Třída modelu s listem blogů
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class BlogsList extends Models {
	public $allBlogsArray = array();
	
	public $scroll = null;
	
	public $linkToAddBlog = null;
	public $linkToAddSection = null;
	
	public $inSection = null;
	
}
?>