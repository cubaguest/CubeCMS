<?php
/* 
 * Seznam šablon
 * Parametry:
 * 
 * customFields - seznam volitelných polí
 * customFieldsEditor - určení typů editorů pro daná volitelná pole
 * 
 */
$this->addTemplate('main', 'text.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));
$this->addTemplate('main', 
    'text_custom.phtml', 
    array('cs' => 'Šablona s vlastníma položkama', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'),
    array(
        'customFields' => 
            array(
               'bottomName1' => array('cs' => 'Volitelný nadpis 1'),
               'bottomTextArea1' => array('cs' => 'Volitelný text 1'),
               'bottomImage1' => array('cs' => 'Obrázek 1'),
            ),
        'customFieldsType' => 
            array(
               'bottomName1' => 'text',
               'bottomTextArea1' => 'textarea',
               'bottomImage1' => 'image',
            ),
      )
   );
$this->addPanelTemplate('panel.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));