<?php 
/**
 * Hlavní konfigurační soubor vzhledu
 * Default face
 */

$face['name'] = "Bootstrap default template";
$face['desc'] = "Default Bootstrap CubeCMS tepmlate";
$face['version'] = "1.0";

// jquery theme
$face['jquery_theme'] = "base";

$face['panels'] = array(
   'left' => 'Levý',
//   'left-hp' => 'Levý na HomePage',
   'right' => 'Pravý',
   'bottom' => 'Spodní',
);

/*
 * modules settings
 */ 
// banners
$modules['banners']['positions'] = array(
    'left' => array('label' => "Box v levo"), 
    'right' => array('label' => "Box v pravo"), 
    'bottom' => array('label' => "Box dole", 'random' => true), 
);

$modules['custommenu']['positions'] = array(
   'bottom' => 'Spodní menu',
);

$modules['hpslideshow']['enabled'] = true;
$modules['hpslideshow']['wysiwyg'] = true;
$modules['hpslideshow']['dimensions'] = array(
   'width' => 1140,
   'height' => 250,
);
