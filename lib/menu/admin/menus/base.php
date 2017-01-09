<?php
$lastID = 32800;
/* 
 * Základní sekce menu
 */
$this->addSection(Menu_Admin::SECTION_STRUCT, array(
   'cs' => 'Website',
   'en' => 'Website',
), 'globe');


$this->addSection(Menu_Admin::SECTION_CONTENT, array(
   'cs' => 'Obsah a šablony',
   'en' => 'Content and templates',
), 'file-text');

$this->addSection(Menu_Admin::SECTION_SHOP, array(
   'cs' => 'Obchod',
   'en' => 'Shop',
), 'shopping-cart');

$this->addSection(Menu_Admin::SECTION_LISTS, array(
   'cs' => 'Seznamy/číselníky',
   'en' => 'Lists',
), 'list');

$this->addSection(Menu_Admin::SECTION_EMAIL, array(
   'cs' => 'E-maily',
   'en' => 'E-mails',
), 'envelope');

$this->addSection(Menu_Admin::SECTION_USER, array(
   'cs' => 'Uživatel',
   'en' => 'User',
), 'user');

$this->addSection(Menu_Admin::SECTION_SETTINGS, array(
   'cs' => 'Nastavení',
   'en' => 'Settings',
), 'cog');

$this->addSection(Menu_Admin::SECTION_INFORMATION, array(
   'cs' => 'Informace',
   'en' => 'Information',
), 'info');

$this->addSection(Menu_Admin::SECTION_ACCOUNT, array(
   'cs' => 'Uživatelské účty',
   'en' => 'Users accounts',
), 'users', Menu_Admin::POSITION_RIGHT);

/* 
 * Základní položky menu
 */
$this->addItem(Menu_Admin::SECTION_STRUCT, new Menu_Admin_Item(
    32769, array( 'cs' => 'Struktura', 'en' => 'Structure'),
   'structure/categories', 'categories', 'list'
));
$this->addItem(Menu_Admin::SECTION_STRUCT, new Menu_Admin_Item(
    32791, array( 'cs' => 'Hromadná úprava kategorií', 'en' => 'Bulk Edit of categories'),
   'structure/cats-bulk-edit', 'catsbulkedit', 'copy'
));
$this->addItem(Menu_Admin::SECTION_STRUCT, new Menu_Admin_Item(
    32786, array( 'cs' => 'Panely', 'en' => 'Panels'),
   'structure/panels', 'adminpanels', 'window-maximize'
));

/* 
 * Uživatelé
 */
$this->addItem(Menu_Admin::SECTION_USER, new Menu_Admin_Item(
    32771, array( 'cs' => 'Uživatelé a skupiny', 'en' => 'Users and Groups'),
   'accounts/user-and-groups', 'users', 'users'
));

/* 
 * Obsah
 */
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32792, array( 'cs' => 'Šablony', 'en' => 'Templates'),
   'content/templates', 'templates', 'file-code-o'
));
//$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
//    32779, array( 'cs' => 'Rychlé nástroje', 'en' => 'Quick-Tools'),
//   'content/quicktools', 'quicktools', 'star.png'
//));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32785, array( 'cs' => 'Bannery', 'en' => 'Banners'),
   'content/banners', 'banners', 'image'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32784, array( 'cs' => 'Formuláře', 'en' => 'Forms'),
   'content/forms', 'forms', 'list-alt'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32794, array( 'cs' => 'Banner na úvodní stránce', 'en' => 'Homepage SlideShow'),
   'content/slideshow', 'hpslideshow', 'camera'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32799, array( 'cs' => 'Pokročilý banner na úvodní stránce', 'en' => 'Homepage Advanced SlideShow'),
   'content/advslideshow', 'hpslideshowadv', 'camera'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32789, array( 'cs' => 'Volitelná menu', 'en' => 'Custom menus'),
   'content/custom-menu', 'custommenu', 'list'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32777, array( 'cs' => 'Úprava zdrojů vzhledu', 'en' => 'Face source edit'),
   'content/faceedit', 'faceedit', 'code'
));
$this->addItem(Menu_Admin::SECTION_CONTENT, new Menu_Admin_Item(
    32800, array( 'cs' => 'Prohlášení o použití cookies', 'en' => 'Allow of cookie usage'),
   'content/cookieinfo', 'text', 'file-text-o'
));


/* 
 * Emaily
 */
$this->addItem(Menu_Admin::SECTION_EMAIL, new Menu_Admin_Item(
    32790, array( 'cs' => 'Poslat e-mail', 'en' => 'Send e-mail'),
   'e-mails/send-email', 'mails', 'paper-plane'
));
$this->addItem(Menu_Admin::SECTION_EMAIL, new Menu_Admin_Item(
    32788, array( 'cs' => 'Newslettery', 'en' => 'Newsletters'),
   'e-mails/newsletters', 'mailsnewsletters', 'newspaper-o'
));
$this->addItem(Menu_Admin::SECTION_EMAIL, new Menu_Admin_Item(
    32787, array( 'cs' => 'Adresář a skupiny', 'en' => 'Address Book and Groups'),
   'e-mails/addressbook-groups', 'mailsaddressbook', 'address-book-o'
));

/* 
 * Systém
 */
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32798, array( 'cs' => 'Základní nastavení', 'en' => 'Base settings'),
   'system/base-settings', 'adminenviroment', 'wrench'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32772, array( 'cs' => 'Pokročilé nastavení systému', 'en' => 'Advanced system settings'),
   'system/system-settings', 'configuration', 'cog'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32773, array( 'cs' => 'Služby', 'en' => 'Services'),
   'system/management', 'services', 'cog'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32775, array( 'cs' => 'Plánování úloh', 'en' => 'Cron'),
   'system/cron', 'crontab', 'clock-o'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32776, array( 'cs' => 'Překlady', 'en' => 'Translations'),
   'system/translate', 'trstaticstexts', 'language'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32796, array( 'cs' => 'Blokace IP adres', 'en' => 'IP address block'),
   'system/ipblock', 'adminipblock', 'ban'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32795, array( 'cs' => 'Úprava htaccess', 'en' => 'Htaccess edit'),
   'system/htaccess', 'adminhtaccess', 'cog'
));
$this->addItem(Menu_Admin::SECTION_SETTINGS, new Menu_Admin_Item(
    32797, array( 'cs' => 'Podweby', 'en' => 'Subsites'),
   'system/subsites', 'adminsites', 'globe'
));


/* 
 * Informace
 */
$this->addItem(Menu_Admin::SECTION_INFORMATION, new Menu_Admin_Item(
    32780, array( 'cs' => 'PHP info', 'en' => 'PHP info'),
   'info/php', 'phpinfo', 'info'
));
$this->addItem(Menu_Admin::SECTION_INFORMATION, new Menu_Admin_Item(
    32774, array( 'cs' => 'Verze systému a modulů', 'en' => 'System and module versions'),
   'system/versions', 'upgrade', 'cubes'
));
$help = new Menu_Admin_Item(
    32782, array( 'cs' => 'Nápověda', 'en' => 'Help - only in Czech'),
   'info/help', 'redirect', 'question'
);
$help->setParams(array(
   'url' => 'https://docs.google.com/document/d/1JOj7JThWEIwWiwWtRl--aLOsXNtPfxGMkucnEe77mBM/edit?hl=cs',
   'code' => '301'
));
$this->addItem(Menu_Admin::SECTION_INFORMATION, $help);

$bug = new Menu_Admin_Item(
    32781, array( 'cs' => 'Chyby/požadavky', 'en' => 'Bugs'),
   'info/bug', 'redirect', 'bug'
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
   'account', 'login', 'user'
));

