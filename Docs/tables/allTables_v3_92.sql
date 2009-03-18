-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Středa 18. března 2009, 17:10
-- Verze MySQL: 5.0.70
-- Verze PHP: 5.2.8-pl2-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `dev`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL auto_increment,
  `id_section` smallint(3) NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `cparams` varchar(200) default NULL,
  `protected` tinyint(1) NOT NULL default '0',
  `priority` smallint(2) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1' COMMENT 'je-li kategorie aktivní',
  `left_panel` tinyint(1) NOT NULL default '1' COMMENT 'Je li zobrazen levý panel',
  `right_panel` tinyint(1) NOT NULL default '1' COMMENT 'Ja li zobrazen pravý panel',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL default 'yearly',
  `sitemap_priority` float NOT NULL default '0.1',
  `show_in_menu` tinyint(1) NOT NULL default '1' COMMENT 'Má li se položka zobrazit v menu',
  `show_when_login_only` tinyint(1) NOT NULL default '0' COMMENT 'Jstli má bát položka zobrazena po přihlášení',
  PRIMARY KEY  (`id_category`),
  KEY `key` (`urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `id_section`, `urlkey`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `cparams`, `protected`, `priority`, `active`, `left_panel`, `right_panel`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`) VALUES
(2, 1, '', 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'monthly', 0.8, 1, 0),
(1, 1, '', 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, NULL, 0, 10, 1, 1, 1, 'monthly', 0.9, 1, 0),
(3, 2, '', 'Novinky', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'weekly', 0.7, 1, 0),
(4, 5, '', 'Login', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'never', 0.1, 1, 0),
(5, 1, '', 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'hourly', 0.5, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_groups`
--

CREATE TABLE IF NOT EXISTS `vypecky_groups` (
  `id_group` smallint(3) unsigned NOT NULL auto_increment COMMENT 'ID skupiny',
  `name` varchar(15) default NULL COMMENT 'Nazev skupiny',
  `label` varchar(20) default NULL,
  `used` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `vypecky_groups`
--

INSERT INTO `vypecky_groups` (`id_group`, `name`, `label`, `used`) VALUES
(1, 'admin', 'Administrátor', 1),
(2, 'guest', 'Host', 1),
(3, 'user', 'Uživatel', 1),
(4, 'poweruser', 'uživatel s většími p', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_items`
--

CREATE TABLE IF NOT EXISTS `vypecky_items` (
  `id_item` smallint(6) NOT NULL auto_increment,
  `label_cs` varchar(100) default NULL,
  `alt_cs` varchar(500) default NULL,
  `label_en` varchar(100) default NULL,
  `alt_en` varchar(500) default NULL,
  `label_de` varchar(100) default NULL,
  `alt_de` varchar(500) default NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rw-',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'r--',
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `params` varchar(500) default NULL COMMENT 'parametry pro daný modul itemu - jsouv popsány v docs',
  `priority` smallint(6) NOT NULL default '0',
  `id_category` smallint(6) NOT NULL,
  `id_module` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id_item`),
  KEY `id_category` (`id_category`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_items`
--

INSERT INTO `vypecky_items` (`id_item`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `group_admin`, `group_user`, `group_guest`, `group_poweruser`, `params`, `priority`, `id_category`, `id_module`) VALUES
(1, 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=true;theme=advanced', 0, 1, 1),
(2, 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=false;theme=simple', 0, 2, 1),
(3, 'Novinky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10;scrollpanel=5', 0, 3, 2),
(4, 'Login', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 4, 4),
(5, 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 5, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_modules`
--

CREATE TABLE IF NOT EXISTS `vypecky_modules` (
  `id_module` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `mparams` varchar(100) default NULL,
  `datadir` varchar(100) default NULL,
  `dbtable1` varchar(50) default NULL,
  `dbtable2` varchar(50) default NULL,
  `dbtable3` varchar(50) default NULL,
  PRIMARY KEY  (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Vypisuji data pro tabulku `vypecky_modules`
--

INSERT INTO `vypecky_modules` (`id_module`, `name`, `mparams`, `datadir`, `dbtable1`, `dbtable2`, `dbtable3`) VALUES
(1, 'text', NULL, NULL, 'texts', NULL, NULL),
(2, 'news', NULL, NULL, 'news', NULL, NULL),
(3, 'dwfiles', NULL, 'dwfiles', 'dwfiles', NULL, NULL),
(4, 'login', NULL, NULL, 'users', NULL, NULL),
(5, 'minigalery', NULL, 'minigalery', 'minigalery', NULL, NULL),
(6, 'workers', NULL, 'workers', 'workers', NULL, NULL),
(7, 'sendmail', NULL, 'sendmail', 'sendmails', NULL, NULL),
(8, 'photogalerymax', 'photosingalerylist=3', 'photogalery', 'photos', 'photo_galeries', 'photo_sections'),
(9, 'guestbook', NULL, '', 'guestbook', NULL, NULL),
(10, 'iframe', NULL, NULL, 'iframe_targets', NULL, NULL),
(11, 'blog', NULL, NULL, 'blogs', 'blogs_sections', NULL),
(12, 'flashpage', '', 'flashpages', NULL, NULL, NULL),
(13, 'comics', NULL, 'comics', 'comics', NULL, NULL),
(14, 'links', NULL, 'links', 'link_sections', NULL, NULL),
(15, 'errors', NULL, NULL, 'errors', NULL, NULL),
(16, 'partners', NULL, 'partners', 'partners', NULL, NULL),
(17, 'users', NULL, 'users', 'users', 'groups', NULL),
(18, 'photogalery', NULL, 'photogalery', 'photogalery_galeries', 'photogalery_photos', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_news`
--

CREATE TABLE IF NOT EXISTS `vypecky_news` (
  `id_new` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `label_cs` varchar(50) NOT NULL,
  `text_cs` varchar(500) NOT NULL,
  `label_en` varchar(50) default NULL,
  `text_en` varchar(500) default NULL,
  `label_de` varchar(50) default NULL,
  `text_de` varchar(500) default NULL,
  `time` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_new`),
  KEY `id_user` (`id_user`),
  KEY `key` (`urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Vypisuji data pro tabulku `vypecky_news`
--

INSERT INTO `vypecky_news` (`id_new`, `id_item`, `urlkey`, `id_user`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time`, `deleted`) VALUES
(7, 3, 'novinky-jitrnicky-na-vypeckach', 3, 'Novinky (Jitrničky) na Výpečkách', '<p>Tak první novinka na <a href="http://www.vypecky.info">Výpečkách</a> je\r\nvlasně zavedení <strong>novinek</strong>, kde můžete psát krátké novinky.\r\nTak hodně zdaru! :-D (těch novinek je tu až moc :-D )</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1210695435, 0),
(8, 3, 'pjekne', 10, 'Pjekne', '<p>Holahola… Zahradní restaurace u Valentů v Hrachovci zahajuje zítra\r\nv 17:00 svůj letní provoz slavnostním naražením sudu Radegast 10°.\r\nNásledovat budou sudy Zubr 11° (17:05), Radegast 12° (17:07) a Kofola (17:10\r\nesli bude). Po slavnostním naražení se koná slavnostní vypití. Bronik\r\nletos připravil 10 vylepšení oproti loňsku a kdo je všechny objeví,\r\ndostane pivečko zadarmo:-)</p>\r\n', NULL, NULL, NULL, NULL, 1210887011, 0),
(9, 3, 'karamazovi', 4, 'Karamazovi', '\n<p>Žijte kulturně a zajděte si na nový Zelenkův film Karamazovi. Po dlouhé\ndobě zase jeden dobrý český film. Zelenka je holt pašák :-)</p>\n\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211016870, 0),
(10, 3, 'upraveny-layout', 3, 'Upravený layout a je ještě LEPŠÍ!', 'Konečně jsem si našel trochu času a upravil layout výpeček. Teď by se\r\nměl korektně zobrazovat v FF a Opeře, jenom v IE zůstává pár chybiček.\r\nCelý mám (částečně) měnitelnou šířku takže potěším lidi co\r\nnepoužívají velké rozlišení.\r\n', 'Better layout', NULL, NULL, NULL, 1211050696, 0),
(11, 3, 'indiana-jones', 4, 'Indiana Jones', '<p>S výpečky do kina. Zítra jdeme s Míšou a Synkem na nového Jonese, tak\r\nneváhejte a připojte se – the more the merrier :-)</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211467174, 0),
(12, 3, 'indiana-jones2', 4, 'Indiana Jones2', '<p>Tak na jonese se nakonec jde až v neděli. Včera byla noc kejklířů,\r\ndneska je ta muzejní. Obě dvě akce mají samozřejmě vyšší prioritu.\r\nDnes na pryglu začnou soutěžní ohňostroje, nenechte si ujit – see ya there.</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211620130, 0),
(13, 3, 'indiana-jones-potreti', 4, 'Indiana Jones potřetí', '\n<p>Tak na Jonese se nakonec šlo až v pondělí. Kdo byl v něděli, tomu se\nonlouvám. Jinak to samozřejmě stálo za to. Smáli jsem se, báli i plakali.\nV závěru se nám sice Jones oženil, ale i to se dalo přežít. Tak kdo\nnebyl, šup šup do kina.</p>\n\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211980229, 0),
(14, 3, 'garden-party', 4, 'garden party', '<p>pro všechny, kdo jsou ve Valmezu: V Hrachovci na zahrádce u Broni se\r\nkoná party na oslavu narozenin jeho zeny (me sestrenky) Svatavy. Kdo pujdete kolem,\r\nnezapomente se stavit – ma tam byt i sele, ci co.</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1212250241, 0),
(16, 3, 'sittin-at-a-bar', 10, 'Sittin&#039; at a bar', '<p>Kdo by neměl chuť se tu a tam ožrat do němoty…? Zde je takový malý\r\ntrenažer pro výpitky jak dosáhnout dobré opice a nenadělat příliš\r\nostudy… Vysoce návykový shledávám především song… <a\r\nhref="http://www.tinymania.com/play/sittinatabar/">Odkaz</a></p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1212482056, 0),
(33, 3, '', 3, 'Další novinka', 'ggfd gdfg fdg df', NULL, NULL, NULL, NULL, 1237116725, 1),
(19, 3, 'novinka-v-cestine', 3, 'Novinka v češtině s popisem', 'česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky ', 'English news with label', 'english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english ', NULL, NULL, 1218921753, 0),
(20, 3, '-label--', 3, 'label', '&lt;b&gt;text&lt;/b&gt;', NULL, NULL, NULL, NULL, 1218975043, 1),
(21, 3, 'cs-label-novy-pekny', 3, 'cs label nový pěkný', 'fdsafsdafasfd', NULL, NULL, NULL, NULL, 1218979456, 1),
(22, 3, 'popis-novinky', 3, 'Popis novinky', 'pokusný text', 'dsadasda', 'English news', NULL, NULL, 1222705858, 0),
(23, 3, 'zcela-nova-novinka', 3, 'Zcela nová novinka a uprava', 'Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky ', 'Realy new News', 'News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text ', NULL, NULL, 1222705915, 0),
(24, 3, 'nova-novinka', 3, 'Nová novinka', '&lt;b&gt;tučně&lt;/b&gt;', NULL, NULL, NULL, NULL, 1222705983, 1),
(25, 3, '...pes...', 3, 'pes', 'tak tohle je novinka o psu', NULL, NULL, NULL, NULL, 1222706254, 1),
(26, 3, '', 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204814, 1),
(27, 3, '', 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204880, 1),
(28, 3, '', 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205108, 1),
(29, 3, '', 3, 'jakubmatas_gmail_com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205120, 0),
(30, 3, '', 3, 'Upravený popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232206214, 0),
(31, 3, '', 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220144, 0),
(32, 3, '', 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220169, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_panels`
--

CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL auto_increment,
  `priority` smallint(2) NOT NULL default '0',
  `label` varchar(30) NOT NULL,
  `id_item` smallint(5) unsigned default NULL,
  `position` enum('left','right') NOT NULL default 'left',
  `enable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_panel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `priority`, `label`, `id_item`, `position`, `enable`) VALUES
(1, 0, 'Nejnovější tlačenka', 1, 'right', 0),
(2, 4, 'Aktuální komiks', 6, 'right', 0),
(3, 5, 'Fotogalerie', 4, 'right', 0),
(4, 10, 'Výpečky', 2, 'left', 0),
(5, 5, 'Účet', 17, 'left', 0),
(6, 0, 'Jitrničky', 5, 'left', 0),
(7, 0, 'Sponzoři', 18, 'right', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_galeries`
--

CREATE TABLE IF NOT EXISTS `vypecky_photogalery_galeries` (
  `id_galery` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(6) NOT NULL,
  `label_cs` varchar(200) default NULL,
  `text_cs` varchar(1000) default NULL,
  `label_en` varchar(200) default NULL,
  `text_en` varchar(1000) default NULL,
  `label_de` varchar(200) default NULL,
  `text_de` varchar(1000) default NULL,
  `time_add` int(11) default NULL,
  `time_edit` int(11) default NULL,
  PRIMARY KEY  (`id_galery`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_photos`
--

CREATE TABLE IF NOT EXISTS `vypecky_photogalery_photos` (
  `id_photo` smallint(5) unsigned NOT NULL auto_increment,
  `id_galery` smallint(5) unsigned NOT NULL,
  `label_cs` varchar(500) default NULL,
  `label_en` varchar(500) default NULL,
  `label_de` varchar(500) default NULL,
  `file` varchar(200) NOT NULL,
  `time_add` int(11) default NULL,
  PRIMARY KEY  (`id_photo`),
  KEY `id_galery` (`id_galery`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sections`
--

CREATE TABLE IF NOT EXISTS `vypecky_sections` (
  `id_section` smallint(3) NOT NULL auto_increment,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `priority` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_sections`
--

INSERT INTO `vypecky_sections` (`id_section`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `priority`) VALUES
(1, 'section 1', NULL, NULL, NULL, NULL, NULL, 0),
(2, 'section 2', NULL, NULL, NULL, NULL, NULL, 0),
(3, 'section 3', NULL, NULL, NULL, NULL, NULL, 0),
(4, 'section 4', NULL, NULL, NULL, NULL, NULL, 0),
(5, 'účet', NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_texts`
--

CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `text_cs` mediumtext,
  `changed_time` int(11) default NULL,
  `text_en` mediumtext,
  `text_de` mediumtext,
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `id_article` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--

INSERT INTO `vypecky_texts` (`id_text`, `id_item`, `text_cs`, `changed_time`, `text_en`, `text_de`) VALUES
(9, 2, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>', 1237143858, NULL, NULL),
(8, 1, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p> </p>', 1237143737, NULL, NULL),
(10, 5, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', 1237145467, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_userfiles`
--

CREATE TABLE IF NOT EXISTS `vypecky_userfiles` (
  `id_file` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  `file` varchar(50) NOT NULL,
  `size` int(11) default NULL,
  `time` int(10) unsigned default NULL,
  PRIMARY KEY  (`id_file`),
  KEY `id_category` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Vypisuji data pro tabulku `vypecky_userfiles`
--

INSERT INTO `vypecky_userfiles` (`id_file`, `id_item`, `id_article`, `id_user`, `file`, `size`, `time`) VALUES
(31, 1, 1, 3, 'obraz-02.jpg', 55552, 1237142860);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_userimages`
--

CREATE TABLE IF NOT EXISTS `vypecky_userimages` (
  `id_file` int(4) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(3) NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  `file` varchar(50) NOT NULL,
  `width` smallint(6) default NULL,
  `height` smallint(6) default NULL,
  `size` int(11) default NULL,
  `time` int(11) default NULL,
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Vypisuji data pro tabulku `vypecky_userimages`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_users`
--

CREATE TABLE IF NOT EXISTS `vypecky_users` (
  `id_user` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(40) default NULL COMMENT 'Heslo',
  `id_group` smallint(3) unsigned default '3',
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `note` varchar(500) default NULL,
  `blocked` tinyint(1) NOT NULL default '0',
  `foto_file` varchar(30) default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Vypisuji data pro tabulku `vypecky_users`
--

INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', 'guest', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0);
