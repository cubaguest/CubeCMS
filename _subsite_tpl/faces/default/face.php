<?
/**
 * Hlavní konfigurační soubor vzhledu
 * Default face
 */

$face['name'] = "default";
$face['desc'] = "Default CubeCMS tepmlate";
$face['version'] = "1.0";

// jquery theme
$face['jquery_theme'] = "base";

/*
 * modules settings
 */ 
// PHOTOGALERY
$modules['photogalery']['small_width'] = 100;
$modules['photogalery']['small_height'] = 100;
$modules['photogalery']['small_crop'] = false;
// banners
$modules['banners']['positions'] = array(
    'right' => array('label' => "Box v pravo", 'limit' => 3, 'random' => true), 
    'bottom' => array('label' => "Box dole", 'random' => true), 
    'in_articles' => array('label' => "Mezi články", 'limit' => 1, 'random' => true));

$modules['custommenu']['positions'] = array(
   'bottom' => 'Spodní menu',
);

