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

$face['panels'] = array(
   'left' => 'Levý',
   'left-hp' => 'Levý na HomePage',
   'right' => 'Pravý',
   'bottom' => 'Spodní',
);

/*
 * modules settings
 */ 
// PHOTOGALERY
$modules['photogalery']['small_width'] = 100;
$modules['photogalery']['small_height'] = 100;
$modules['photogalery']['small_crop'] = false;
// soubory ke stažení
$modules['downloadfiles']['cols'] = 3;
// banners
$modules['banners']['positions'] = array(
    'right' => array('label' => "Box v pravo", 'limit' => 3, 'random' => true), 
    'bottom' => array('label' => "Box dole", 'random' => true), 
    'in_articles' => array('label' => "Mezi články", 'limit' => 1, 'random' => true));

$modules['custommenu']['positions'] = array(
   'bottom' => 'Spodní menu',
);

$modules['hpslideshow']['enabled'] = true;
$modules['hpslideshow']['dimensions'] = array(
   'width' => 630,
   'height' => 150,
);
