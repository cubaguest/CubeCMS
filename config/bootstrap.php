<?php
// base app init
//Auth::addAuthenticator(new Auth_Provider_Google(3));
//Auth::addAuthenticator(new Auth_Provider_OpenID(3));

function extendAdminMenu(Menu_Admin $menu)
{
   // další položky
   $menu->addItem( Menu_Admin::SECTION_LISTS, 
       new Menu_Admin_Item(34000, array('cs' => 'Druhy místností'), 'places-list', 'text','page.png') );
   $menu->addItem( Menu_Admin::SECTION_LISTS, 
       new Menu_Admin_Item(34001, array('cs' => 'Druhy řemeslníků'), 'peoples-list', 'text','page.png') );

   // vlastní sekce
   $menu->addSection('cubecms', array('cs' => 'Cube CMS - testing'), 'cog.png');
   $menu->addItem( 'cubecms', 
       new Menu_Admin_Item(Menu_Admin_Item::getLastID(), array('cs' => 'Text v záhlaví'), 'text-header', 'text','page.png') );
   $menu->addItem( 'cubecms', 
       new Menu_Admin_Item(Menu_Admin_Item::getLastID(), array('cs' => 'Text v zápatí'), 'text-footer', 'text','page.png') );
}