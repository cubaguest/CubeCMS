<?php
/* 
 * Seznam šablon
 */

$this->addTemplate('main', 'list.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));
$this->addTemplate('show', 'articles:detail.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));
$this->addTemplate('archive', 'articles:archive.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));

$this->addPanelTemplate('panel.phtml', array('cs' => 'Výchozí šablona', 'en' => 'Default template', 'sk' => 'Predvolená šablóna', 'de' => 'Standardvorlage'));