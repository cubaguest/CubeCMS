<?php
/* 
 * Seznam šablon
 */
$this->addTemplate('main', 'text.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));
$this->addTemplate(
    'main', 
    'text2.phtml', 
    array('cs' => 'testovací šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'),
    array(
        'customFields' => array('pokus' => array('cs' => 'Pokusný text')),
        'customFieldsEditor' => array('pokus' => 'advanced'),
        )
    );
$this->addPanelTemplate('panel.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));