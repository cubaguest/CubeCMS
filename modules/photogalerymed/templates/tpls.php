<?php
/* 
 * Seznam šablon
 */

$this->addTemplate('main', 'list.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));
$this->addTemplate('show', 'detail.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));
$this->addTemplate('archive', 'articles:archive.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));

$this->addPanelTemplate('articles:panel.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));