<?php
/*
 * Třída modelu s listem Novinek
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class UsersList extends Models {
	public $scroll = null;
	
	public $allUserssArray = array();
	
	public $userssTableOrder = array();
	
	public $usersSearchArray = array();
	
	public $isControll = false;
	
	public $linkToAdd = null;
}

?>