<?php 
// Remove admin categories and rights - is in admmenu.xml

$modelCats = new Model_Category();

$adminModules = array("categories",'configuration', 'crontab', 'mails', 'panels' ,'services', 'templates', 'trstaticstexts', 'upgrade', 'users');

$modelRights = new Model_Rights();

// $cats = $modelCats->records();

?>