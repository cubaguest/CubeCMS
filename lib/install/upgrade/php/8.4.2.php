<?php

// Přesun custom menus do struktury
// načtení static boxů

$mItems = new AdminCustomMenu_Model_Items();


$menusStatic = Face::getCurrent()->getParam('positions', 'custommenu');

if (!empty($menusStatic)) {
   foreach ($menusStatic as $key => $name) {
      // vytvoříme menu box
      
      // vytvoříme rodiče
      /* @var $parent Model_ORM_Tree_Record */
      $parent = AdminCustomMenu_Model_Items::getNewRecord();
      $parent->{AdminCustomMenu_Model_Items::COLUMN_NAME} = array(Locales::getDefaultLang() => $name);
      $parent->{AdminCustomMenu_Model_Items::COLUMN_BOX} = $key;
      $parent->{AdminCustomMenu_Model_Items::COLUMN_IS_TPL_MENU} = 1;
      $parent->save();
      $parent->setAsRoot();

      // vybrání položek z boxu podle pořadí a zařazení do stromu
      $items = $mItems->where(AdminCustomMenu_Model_Items::COLUMN_BOX . ' = :box', array('box' => $key))
              ->order(array(AdminCustomMenu_Model_Items::COLUMN_ORDER))
              ->records();

      // zařazení položek do menu boxu
      if (!empty($items)) {
         foreach ($items as $i) {
            /* @var $i Model_ORM_Tree_Record */
            $parent->addNode($i);
         }
      }
   }
}