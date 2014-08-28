<?php
$lastID = 32794;
/* 
 * Základní sekce menu
 */
$this->addSection(Menu_Admin::SECTION_STRUCT, array(
   'cs' => 'Struktura',
   'en' => 'Structure',
), 'application_side_tree.png');


$this->addSection(Menu_Admin::SECTION_CONTENT, array(
   'cs' => 'Obsah a šablony',
   'en' => 'Content and templates',
), 'page_gear.png');

$this->addSection(Menu_Admin::SECTION_SHOP, array(
   'cs' => 'Obchod',
   'en' => 'Shop',
), 'cart.png');

$this->addSection(Menu_Admin::SECTION_LISTS, array(
   'cs' => 'Seznamy/číselníky',
   'en' => 'Lists',
), 'application_view_list.png');

$this->addSection(Menu_Admin::SECTION_EMAIL, array(
   'cs' => 'E-maily',
   'en' => 'E-mails',
), 'email.png');

$this->addSection(Menu_Admin::SECTION_USER, array(
   'cs' => 'Uživatel',
   'en' => 'User',
), 'page_gear.png');

$this->addSection(Menu_Admin::SECTION_SETTINGS, array(
   'cs' => 'Nastavení',
   'en' => 'Settings',
), 'cog.png');

$this->addSection(Menu_Admin::SECTION_INFORMATION, array(
   'cs' => 'Informace',
   'en' => 'Information',
), 'information.png');

$this->addSection(Menu_Admin::SECTION_ACCOUNT, array(
   'cs' => 'Uživatelské účty',
   'en' => 'Users accounts',
), 'group.png');

/* 
 * Základní položky menu
 */
$this->addItem(Menu_Admin::SECTION_STRUCT, new Menu_Admin_Item(
    32769, array( 'cs' => 'Struktura', 'en' => 'Structure'),
   'structure/categories', 'categories', 'tree.png'
));
$this->addItem(Menu_Admin::SECTION_STRUCT, new Menu_Admin_Item(
    32791, array( 'cs' => 'Hromadná úprava kategorií', 'en' => 'Bulk Edit of categories'),
   'structure/categories-bulk-edit', 'catsbulkedit', 'table_edit.png'
));
$this->addItem(Menu_Admin::SECTION_STRUCT, new Menu_Admin_Item(
    32786, array( 'cs' => 'Panely', 'en' => 'Panels'),
   'structure/panels', 'panels', 'application_side_contract.png'
));

/* 
 * Uživatelé
 */
$this->addItem(Menu_Admin::SECTION_USER, new Menu_Admin_Item(
    32771, array( 'cs' => 'Uživatelé a skupiny', 'en' => 'Users and Groups'),
   'accounts/user-and-groups', 'users', 'group.png'
));

/* 
 * Obsah
 */
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32792, array( 'cs' => 'Šablony', 'en' => 'Templates'),
   'content/templates', 'templates', 'page_code.png'
));
//$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
//    32779, array( 'cs' => 'Rychlé nástroje', 'en' => 'Quick-Tools'),
//   'content/quicktools', 'quicktools', 'star.png'
//));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32785, array( 'cs' => 'Bannery', 'en' => 'Banners'),
   'content/banners', 'banners', 'image.png'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32784, array( 'cs' => 'Formuláře', 'en' => 'Forms'),
   'content/forms', 'templates', 'application_form.png'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32794, array( 'cs' => 'Banner na úvodní stránce', 'en' => 'Homepage SlideShow'),
   'content/slideshow', 'hpslideshow', 'images.png'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32789, array( 'cs' => 'Volitelná menu', 'en' => 'Custom menus'),
   'content/custom-menu', 'custommenu', 'application_side_contract.png'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32777, array( 'cs' => 'Úprava zdrojů vzhledu', 'en' => 'Face source edit'),
   'content/faceedit', 'faceedit', 'page_white_code_red.png'
));


/* 
 * Emaily
 */
$this->addItem(Menu_Admin::SECTION_EMAIL, new Menu_Admin_Item(
    32790, array( 'cs' => 'Poslat e-mail', 'en' => 'Send e-mail'),
   'e-mails/send-email', 'mails', 'email_edit.png'
));
$this->addItem(Menu_Admin::SECTION_EMAIL, new Menu_Admin_Item(
    32788, array( 'cs' => 'Newslettery', 'en' => 'Newsletters'),
   'e-mails/newsletters', 'mailsnewsletters', 'email.png'
));
$this->addItem(Menu_Admin::SECTION_EMAIL, new Menu_Admin_Item(
    32787, array( 'cs' => 'Adresář a skupiny', 'en' => 'Address Book and Groups'),
   'e-mails/addressbook-groups', 'mailsaddressbook', 'book_addresses.png'
));

/* 
 * Systém
 */
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32772, array( 'cs' => 'Nastavení systému', 'en' => 'System settings'),
   'system/system-settings', 'configuration', 'wrench.png'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32773, array( 'cs' => 'Služby', 'en' => 'Services'),
   'system/management', 'services', 'cog.png'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32775, array( 'cs' => 'Plánování úloh', 'en' => 'Cron'),
   'system/cron', 'crontab', 'time.png'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32776, array( 'cs' => 'Překlady', 'en' => 'Translations'),
   'system/translate', 'trstaticstexts', 'translate.png'
));

/* 
 * Informace
 */
$this->addItem(Menu_Admin::SECTION_INFORMATION, new Menu_Admin_Item(
    32780, array( 'cs' => 'PHP info', 'en' => 'PHP info'),
   'info/php', 'phpinfo', 'information.png'
));
$this->addItem(Menu_Admin::SECTION_INFORMATION, new Menu_Admin_Item(
    32774, array( 'cs' => 'Verze systému a modulů', 'en' => 'System and module versions'),
   'system/versions', 'upgrade', 'bricks.png'
));
$help = new Menu_Admin_Item(
    32782, array( 'cs' => 'Nápověda', 'en' => 'Help - only in Czech'),
   'info/help', 'redirect', 'help.png'
);
$help->setParams(array(
   'url' => 'https://docs.google.com/document/d/1JOj7JThWEIwWiwWtRl--aLOsXNtPfxGMkucnEe77mBM/edit?hl=cs',
   'code' => '301'
));
$this->addItem(Menu_Admin::SECTION_INFORMATION, $help);

$bug = new Menu_Admin_Item(
    32781, array( 'cs' => 'Chyby/požadavky', 'en' => 'Bugs'),
   'info/bug', 'redirect', 'bug.png'
);
$bug->setParams(array(
   'url' => 'https://spreadsheets.google.com/spreadsheet/viewform?formkey=dG1VSFQ4Wm5Ja0VKbUNHTGpLMFZIVXc6MQ',
   'code' => '301'
));
$this->addItem(Menu_Admin::SECTION_INFORMATION, $bug);

/* 
 * Informace
 */
$this->addItem(Menu_Admin::SECTION_ACCOUNT, new Menu_Admin_Item(
    32783, array( 'cs' => 'Účet', 'en' => 'Account'),
   'account', 'login', 'user.png'
));

