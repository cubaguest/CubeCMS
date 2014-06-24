<?php

/* 
 * Tákladní položky menu
 */
$this->addItem(Menu_Admin::SECTION_SHOP, new Menu_Admin_Item(
    32904, array( 'cs' => 'Zboží', 'en' => 'Products'),
   'eshop/products', 'shopproductgeneraladmin', 'drink.png'
));
$this->addItem(Menu_Admin::SECTION_SHOP, new Menu_Admin_Item(
    32900, array( 'cs' => 'Objednávky', 'en' => 'Orders'),
   'eshop/orders', 'shoporders', 'application_view_detail.png'
));
$this->addItem(Menu_Admin::SECTION_SHOP, new Menu_Admin_Item(
    32903, array( 'cs' => 'Zákazníci', 'en' => 'Customers'),
   'eshop/customers', 'shopcustomers', 'group.png'
));
$this->addItem(Menu_Admin::SECTION_SHOP, new Menu_Admin_Item(
    32902, array( 'cs' => 'Varianty zboží', 'en' => 'Products Variants'),
   'eshop/variants', 'shopproductvariants', 'brick.png'
));
$this->addItem(Menu_Admin::SECTION_SHOP, new Menu_Admin_Item(
    32901, array( 'cs' => 'Nastavení', 'en' => 'Settings'),
   'eshop/settings', 'shopsettings', 'wrench.png'
));
