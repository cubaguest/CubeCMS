<?php
/* 
 * Seznam šablon
 * Parametry:
 * 
 * customFields - seznam volitelných polí
 * customFieldsEditor - určení typů editorů pro daná volitelná pole
 * 
 */
$this->addTemplate('main', 
    'main.phtml', 
    array('cs' => 'Šablona s jinými položkami', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'),
    array(
        'blocks' => 
            array(
                // dva obrázky nalevo a text napravo
               '2imgL_textR' => 
                  array(
                     'name' => array('cs' => '2x Video a text na pravo'),
                     'img' => '2imgL_textR.jpg',
                     'template' => '2imgL_textR.phtml',
                     'items' => array(
                         1 => array( // označuje index, který se použije v šabloně pro výběr daného provku
                             'model' => 'CustomBlocks_Model_Images', // dá se difonovat ve valstním modelu a kontroleru jako další položky
                             'name' => array('cs' => 'První obrázek'),
                         ),
                         2 => array( 
                             'model' => 'CustomBlocks_Model_Images',
                             'name' => array('cs' => 'Druhý obrázek'),
                         ),
                         3 => array( // označuje index, který je pak v šabloně
                             'model' => 'CustomBlocks_Model_Texts',
                             'name' => array('cs' => 'Textové pole'),
                             'tinymce' => true,
                         ),
                     ) 
                  ),
               'text' => 
                  array(
                     'name' => array('cs' => 'Textové pole - tři sloupce'),
                     'template' => 'text.phtml',
                     'img' => 'text.jpg',
                     'items' => array(
                         1 => array( 
                             'model' => 'CustomBlocks_Model_Texts',
                             'name' => array('cs' => 'Text'),
                             'tinymce' => true,
                         ),
                     ) 
                  ),
                
               'youtube' => 
                  array(
                     'name' => array('cs' => 'Youtube video'),
                     'template' => 'youtube.phtml',
                     'items' => array(
                         1 => array( 
                             'model' => 'CustomBlocks_Model_Videos',
                             'name' => array('cs' => 'Url adresa videa'),
                         ),
                     ) 
                  ),
               'map' => 
                  array(
                     'name' => array('cs' => 'Mapa'),
                     'template' => 'map.phtml',
                     'items' => array(
                         1 => array( 
                             'model' => 'CustomBlocks_Model_Embeds',
                             'name' => array('cs' => 'Kód mapy'),
                         ),
                     ) 
                  ),
                
                
                
            ),
      )
   );