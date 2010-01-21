
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;




CREATE TABLE IF NOT EXISTS `vypecky_articles` (
  `id_article` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned default '1',
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) default NULL,
  `label_cs` varchar(400) default NULL,
  `text_cs` text,
  `label_en` varchar(400) default NULL,
  `text_en` text,
  `lebal_de` varchar(400) default NULL,
  `text_de` text,
  PRIMARY KEY  (`id_article`),
  KEY `id_item` (`id_item`,`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `lebal_de` (`lebal_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


INSERT INTO `vypecky_articles` (`id_article`, `id_item`, `id_user`, `add_time`, `edit_time`, `label_cs`, `text_cs`, `label_en`, `text_en`, `lebal_de`, `text_de`) VALUES
(1, 7, 1, 1239003796, 1239112581, 'lore ipsum', '<p style="text-align: justify;">Nic takového, žádný román se nekoná a přiznejme si, že ke značnému zklamání mnoha z nás, kteří věřili tomu, že Kundera vydá na sklonku svého života dílo, v němž rozvine témata, kterých se dotkl už ve svých předchozích pracích - emigraci, zradu, odcizení, identitu. Spisovatel je opravdu rozvíjí, opakuje a upřesňuje - avšak pouze formou esejů s názvem Une rencontre (Setkání).</p>\r\n<p><a title="image" rel="lightbox" href="data/userimages/anree.jpg"><img style="float: left;" title="anree.jpg" src="data/userimages/anree.jpg" alt="anree.jpg" width="150" height="200" /></a></p>\r\n<p style="text-align: justify;">V kontextu celé knihy by byl možná příhodnější název Setkání s Milanem Kunderou. V jejím úvodu totiž autor tvrdí, že pokud umělec mluví či píše o někom jiném, mluví, přímo či nepřímo, především sám o sobě a tím je ovlivněn i jeho soud. "Pokud mluví o Beckettovi, co nám Bacon vlastně sděluje o sobě?" ptá se.</p>\r\n<p> </p>\r\n<p>Milan Kundera oslavil osmdesátiny</p>\r\n<p> </p>\r\n<p style="text-align: justify;">Ačkoliv patří mezi osobnosti přijímané kontroverzně prakticky celý život, zejména v posledních měsících jako by nutil zaujímat postoje vůči sobě i ty, kteří od něj nepřečetli jediné slovo.</p>\r\n<p> </p>\r\n<p style="text-align: justify;">Takže co říká Setkání o Kunderovi? V jedné ze svých posledních knih Pomalost přichází s paralelou mezi rychlostí chůze a schopností paměti vybavovat si jisté momenty. Pokud zrychlujeme, snažíme se na některé věci zapomenout, pokud naopak zpomalujeme, pomáháme si je vybavit, tvrdí a lamentuje nad tím, že svět je příliš rychlý, a abychom dokázali ocenit drobné radosti, je potřeba zpomalit. V Setkání učinil Kundera přímo zastávku s ohlédnutím.</p>\r\n<p> </p>', NULL, NULL, NULL, NULL),
(2, 7, 1, 1239094244, 1239094244, 'TinyMCE Plugin Media integration', '<h2>Plugin: media</h2>\r\n<p>This plugin handles embedded media such as QuickTime, Flash, ShockWave, RealPlayer and Windows Media Player. It has two output methods. One is normal embed/object tags and the other is using Javascript; the latter was added to work around the IE embedded media issue, read more about that below.</p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Installation Instructions" href="http://wiki.moxiecode.com/index.php?title=TinyMCE:Plugins/media&amp;action=edit&amp;section=2">edit</a>]</div>\r\n<p><a name="Installation_Instructions"></a></p>\r\n<h4>Installation Instructions</h4>\r\n<ol>\r\n<li> Add plugin to TinyMCE plugin option list example: plugins : "media". </li>\r\n<li> Add the button control name to a toolbar row in the theme. </li>\r\n<li> Verify init option <a title="TinyMCE:Configuration/cleanup" href="http://wiki.moxiecode.com/index.php/TinyMCE:Configuration/cleanup">cleanup</a> is omitted or set to true. </li>\r\n</ol>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Initialization Example" href="http://wiki.moxiecode.com/index.php?title=TinyMCE:Plugins/media&amp;action=edit&amp;section=3">edit</a>]</div>\r\n<p><a name="Initialization_Example"></a></p>\r\n<h4>Initialization Example</h4>\r\n<pre>tinyMCE.init({<br />	theme : "advanced",<br />	mode : "textareas",<br />	plugins : "media",<br />	theme_advanced_buttons1_add : "media"<br />});<br /></pre>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Using the JavaScript output method" href="http://wiki.moxiecode.com/index.php?title=TinyMCE:Plugins/media&amp;action=edit&amp;section=4">edit</a>]</div>\r\n<p><a name="Using_the_JavaScript_output_method"></a></p>\r\n<h4>Using the JavaScript output method</h4>\r\n<p>Object/embed tags are output to the HTML code by default when using this plugin but there is an alternative JS output method. This enables you to workaround the issue with IE not being able to "seamlessly embed" media objects in a HTML page due to a lawsuit.</p>\r\n<p>You will have to add a specific media embed script to your page in order to use this output method. This script includes the functions needed to output the various media types using a document.write method. This script is located at this path "tiny_mce/plugins/media/jscripts/embed.js". Add this script to your page header.</p>\r\n<p>We recommend that you copy the script from the TinyMCE directory to your sites/systems script directory since deeplinking into TinyMCE from pagelevel isn\\''t recommended since files such as this might be moved in the future.</p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Example on how to add the script to your page header:" href="http://wiki.moxiecode.com/index.php?title=TinyMCE:Plugins/media&amp;action=edit&amp;section=5">edit</a>]</div>\r\n<p><a name="Example_on_how_to_add_the_script_to_your_page_header:"></a></p>\r\n<h4>Example on how to add the script to your page header:</h4>\r\n<pre>&lt;html&gt;<br />&lt;head&gt;<br />	&lt;script type="text/javascript" src="embed.js"&gt;&lt;/script&gt;<br />&lt;head&gt;<br />&lt;body&gt;<br />	Some page with a TinyMCE instance.<br />&lt;/body&gt;<br />&lt;/html&gt;<br /></pre>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Plugin options" href="http://wiki.moxiecode.com/index.php?title=TinyMCE:Plugins/media&amp;action=edit&amp;section=6">edit</a>]</div>\r\n<p><a name="Plugin_options"></a></p>\r\n<h4>Plugin options</h4>\r\n<dl><dt> media_use_script </dt><dd> True/false option that gives you the ability to have a JavaScript embed method instead of using object/embed tags. Defaults to: false </dd><dt> media_wmp6_compatible </dt><dd> True/false option that enables you to force Windows media player 6 compatiblity by returning that clsid, but some features and options for WMP may not work if you use this option. You can find a reference on these options at w3schools. Defaults to: false </dd><dt> media_skip_plugin_css </dt><dd> Skips the loading of the default plugin CSS file, this can be useful if your content CSS already defined the media specific CSS information, Defaults to: false. </dd><dt> media_external_list_url </dt><dd> URL to a JS file containing files to be listed in the media dropdown list similar to the one found in the advimg dialog. The name of the array variable in the JS file should be \\''tinyMCEMediaList\\''. </dd><dt> media_types </dt><dd> Name/Value list of format mappings to file extensions. Defaults to: flash=swf;shockwave=dcr;qt=mov,qt,mpg,mp3,mp4,mpeg;shockwave=dcr;wmp=avi,wmv,wm,asf,asx,wmx,wvx;rmp=rm,ra,ram. </dd><dt> <a title="TinyMCE:Configuration/media strict" href="http://wiki.moxiecode.com/index.php/TinyMCE:Configuration/media_strict">media_strict</a> </dt><dd> This option enables you to switch strict output on/off. </dd></dl>', NULL, NULL, NULL, NULL);



CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL auto_increment,
  `id_section` smallint(3) NOT NULL,
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
  PRIMARY KEY  (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;


INSERT INTO `vypecky_categories` (`id_category`, `id_section`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `cparams`, `protected`, `priority`, `active`, `left_panel`, `right_panel`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`) VALUES
(2, 1, 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'monthly', 0.8, 1, 0),
(1, 1, 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, NULL, 0, 10, 1, 1, 1, 'monthly', 0.9, 1, 0),
(3, 2, 'Novinky', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'weekly', 0.7, 1, 0),
(4, 5, 'Login', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'never', 0.1, 1, 0),
(5, 1, 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'hourly', 0.5, 1, 0),
(6, 3, 'Reference', NULL, 'References', NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'monthly', 0.8, 1, 0),
(7, 2, 'Články', NULL, 'Articles', NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'weekly', 0.8, 1, 0),
(8, 3, 'Kontakty', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'yearly', 0.5, 1, 0),
(9, 4, 'Pokus', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'yearly', 0.1, 1, 0),
(10, 4, 'Produkty', 'Naše produkty', NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 1, 'monthly', 0.8, 1, 0);



CREATE TABLE IF NOT EXISTS `vypecky_contacts` (
  `id_contact` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_city` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) default NULL,
  `text_cs` text,
  `name_en` varchar(300) default NULL,
  `text_en` text,
  `name_de` varchar(300) default NULL,
  `text_de` text,
  `file` varchar(200) default NULL,
  `changed_time` int(11) default NULL,
  PRIMARY KEY  (`id_contact`),
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `name_cs` (`name_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `name_en` (`name_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `name_de` (`name_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;


INSERT INTO `vypecky_contacts` (`id_contact`, `id_item`, `id_city`, `name_cs`, `text_cs`, `name_en`, `text_en`, `name_de`, `text_de`, `file`, `changed_time`) VALUES
(2, 8, 203, 'Prodejna a sklad, centrála společnosti', '<p>Telefon: 571 611 801, 571 618 970<br />Mobil:739 619 605<br /> Fax: 571 611 801</p>\r\n<p> </p>\r\n<p>E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a></p>\r\n<p> </p>\r\n<p>Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem.jpg', 1239209498),
(4, 8, 21, 'Prodejna', '<p>Telefon: 571 611 801, 571 618 970,Mobil:739 619 605<br /> Fax: 571 611 801<br /> E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a><br /> Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem2.jpg', 1239207032),
(5, 8, 100, 'Prodejna a sklad', '<p>Telefon: 571 611 801, 571 618 970,Mobil:739 619 605<br /> Fax: 571 611 801<br /> E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a><br /> Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem3.jpg', 1239207080);



CREATE TABLE IF NOT EXISTS `vypecky_contacts_areas` (
  `id_area` int(11) NOT NULL auto_increment,
  `area_name` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65536 ;


INSERT INTO `vypecky_contacts_areas` (`id_area`, `area_name`) VALUES
(1, 'Hlavní město Praha'),
(2, 'Jihočeský kraj'),
(3, 'Jihomoravský kraj'),
(4, 'Karlovarský kraj'),
(5, 'Královéhradecký kraj'),
(6, 'Liberecký kraj'),
(7, 'Moravskoslezský kraj'),
(8, 'Olomoucký kraj'),
(9, 'Pardubický kraj'),
(10, 'Plzeňský kraj'),
(11, 'Středočeský kraj'),
(12, 'Ústecký kraj'),
(13, 'Vysočina'),
(14, 'Zlínský kraj'),
(65535, 'Nezařazeno');



CREATE TABLE IF NOT EXISTS `vypecky_contacts_cities` (
  `id_city` int(11) NOT NULL auto_increment,
  `id_area` int(11) NOT NULL,
  `city_name` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_city`),
  KEY `id_area` (`id_area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65536 ;


INSERT INTO `vypecky_contacts_cities` (`id_city`, `id_area`, `city_name`) VALUES
(1, 1, 'Praha'),
(2, 2, 'Blatná'),
(3, 2, 'České Budějovice'),
(4, 2, 'Český Krumlov'),
(5, 2, 'Dačice'),
(6, 2, 'Jindřichův Hradec'),
(7, 2, 'Kaplice'),
(8, 2, 'Milevsko'),
(9, 2, 'Písek'),
(10, 2, 'Prachatice'),
(11, 2, 'Soběslav'),
(12, 2, 'Strakonice'),
(13, 2, 'Tábor'),
(14, 2, 'Trhové Sviny'),
(15, 2, 'Třeboň'),
(16, 2, 'Týn nad Vltavou'),
(17, 2, 'Vimperk'),
(18, 2, 'Vodňany'),
(19, 3, 'Blansko'),
(20, 3, 'Boskovice'),
(21, 3, 'Brno'),
(22, 3, 'Břeclav'),
(23, 3, 'Bučovice'),
(24, 3, 'Hodonín'),
(25, 3, 'Hustopeče'),
(26, 3, 'Ivančice'),
(27, 3, 'Kuřim'),
(28, 3, 'Kyjov'),
(29, 3, 'Mikulov'),
(30, 3, 'Moravský Krumlov'),
(31, 3, 'Pohořelice'),
(32, 3, 'Rosice'),
(33, 3, 'Slavkov u Brna'),
(34, 3, 'Šlapanice'),
(35, 3, 'Tišnov'),
(36, 3, 'Veselí nad Moravou'),
(37, 3, 'Vyškov'),
(38, 3, 'Znojmo'),
(39, 3, 'Židlochovice'),
(40, 4, 'Aš'),
(41, 4, 'Cheb'),
(42, 4, 'Karlovy Vary'),
(43, 4, 'Kraslice'),
(44, 4, 'Mariánské Lázně'),
(45, 4, 'Ostrov'),
(46, 4, 'Sokolov'),
(47, 5, 'Broumov'),
(48, 5, 'Dobruška'),
(49, 5, 'Dvůr Králové nad Labem'),
(50, 5, 'Hořice'),
(51, 5, 'Hradec Králové'),
(52, 5, 'Jaroměř'),
(53, 5, 'Jičín'),
(54, 5, 'Kostelec nad Orlicí'),
(55, 5, 'Náchod'),
(56, 5, 'Nová Paka'),
(57, 5, 'Nové Město nad Metují'),
(58, 5, 'Nový Bydžov'),
(59, 5, 'Rychnov nad Kněžnou'),
(60, 5, 'Trutnov'),
(61, 5, 'Vrchlabí'),
(62, 6, 'Česká Lípa'),
(63, 6, 'Frýdlant'),
(64, 6, 'Jablonec nad Nisou'),
(65, 6, 'Jilemnice'),
(66, 6, 'Liberec'),
(67, 6, 'Nový Bor'),
(68, 6, 'Semily'),
(69, 6, 'Tanvald'),
(70, 6, 'Turnov'),
(71, 6, 'Železný Brod'),
(72, 7, 'Bílovec'),
(73, 7, 'Bohumín'),
(74, 7, 'Bruntál'),
(75, 7, 'Český Těšín'),
(76, 7, 'Frenštát pod Radhoštěm'),
(77, 7, 'Frýdek-Místek'),
(78, 7, 'Frýdlant nad Ostravicí'),
(79, 7, 'Havířov'),
(80, 7, 'Hlučín'),
(81, 7, 'Jablunkov'),
(82, 7, 'Karviná'),
(83, 7, 'Kopřivnice'),
(84, 7, 'Kravaře'),
(85, 7, 'Krnov'),
(86, 7, 'Nový Jičín'),
(87, 7, 'Odry'),
(88, 7, 'Opava'),
(89, 7, 'Orlová'),
(90, 7, 'Ostrava'),
(91, 7, 'Rýmařov'),
(92, 7, 'Třinec'),
(93, 7, 'Vítkov'),
(94, 8, 'Hranice'),
(95, 8, 'Jeseník'),
(96, 8, 'Konice'),
(97, 8, 'Lipník nad Bečvou'),
(98, 8, 'Litovel'),
(99, 8, 'Mohelnice'),
(100, 8, 'Olomouc'),
(101, 8, 'Prostějov'),
(102, 8, 'Přerov'),
(103, 8, 'Šternberk'),
(104, 8, 'Šumperk'),
(105, 8, 'Uničov'),
(106, 8, 'Zábřeh'),
(107, 9, 'Česká Třebová'),
(108, 9, 'Hlinsko'),
(109, 9, 'Holice'),
(110, 9, 'Chrudim'),
(111, 9, 'Králíky'),
(112, 9, 'Lanškroun'),
(113, 9, 'Litomyšl'),
(114, 9, 'Moravská Třebová'),
(115, 9, 'Pardubice'),
(116, 9, 'Polička'),
(117, 9, 'Přelouč'),
(118, 9, 'Svitavy'),
(119, 9, 'Ústí nad Orlicí'),
(120, 9, 'Vysoké Mýto'),
(121, 9, 'Žamberk'),
(122, 10, 'Blovice'),
(123, 10, 'Domažlice'),
(124, 10, 'Horažďovice'),
(125, 10, 'Horšovský Týn'),
(126, 10, 'Klatovy'),
(127, 10, 'Kralovice'),
(128, 10, 'Nepomuk'),
(129, 10, 'Nýřany'),
(130, 10, 'Plzeň'),
(131, 10, 'Přeštice'),
(132, 10, 'Rokycany'),
(133, 10, 'Stod'),
(134, 10, 'Stříbro'),
(135, 10, 'Sušice'),
(136, 10, 'Tachov'),
(137, 11, 'Benešov'),
(138, 11, 'Beroun'),
(139, 11, 'Brandýs nad Labem-Stará Boleslav'),
(140, 11, 'Čáslav'),
(141, 11, 'Černošice'),
(142, 11, 'Český Brod'),
(143, 11, 'Dobříš'),
(144, 11, 'Hořovice'),
(145, 11, 'Kladno'),
(146, 11, 'Kolín'),
(147, 11, 'Kralupy nad Vltavou'),
(148, 11, 'Kutná Hora'),
(149, 11, 'Lysá nad Labem'),
(150, 11, 'Mělník'),
(151, 11, 'Mladá Boleslav'),
(152, 11, 'Mnichovo Hradiště'),
(153, 11, 'Neratovice'),
(154, 11, 'Nymburk'),
(155, 11, 'Poděbrady'),
(156, 11, 'Příbram'),
(157, 11, 'Rakovník'),
(158, 11, 'Říčany'),
(159, 11, 'Sedlčany'),
(160, 11, 'Slaný'),
(161, 11, 'Vlašim'),
(162, 11, 'Votice'),
(163, 12, 'Bílina'),
(164, 12, 'Děčín'),
(165, 12, 'Chomutov'),
(166, 12, 'Kadaň'),
(167, 12, 'Litoměřice'),
(168, 12, 'Litvínov'),
(169, 12, 'Louny'),
(170, 12, 'Lovosice'),
(171, 12, 'Most'),
(172, 12, 'Podbořany'),
(173, 12, 'Roudnice nad Labem'),
(174, 12, 'Rumburk'),
(175, 12, 'Teplice'),
(176, 12, 'Ústí nad Labem'),
(177, 12, 'Varnsdorf'),
(178, 12, 'Žatec'),
(179, 13, 'Bystřice nad Pernštejnem'),
(180, 13, 'Havlíčkův Brod'),
(181, 13, 'Humpolec'),
(182, 13, 'Chotěboř'),
(183, 13, 'Jihlava'),
(184, 13, 'Moravské Budějovice'),
(185, 13, 'Náměšť nad Oslavou'),
(186, 13, 'Nové Město na Moravě'),
(187, 13, 'Pacov'),
(188, 13, 'Pelhřimov'),
(189, 13, 'Světlá nad Sázavou'),
(190, 13, 'Telč'),
(191, 13, 'Třebíč'),
(192, 13, 'Velké Meziříčí'),
(193, 13, 'Žďár nad Sázavou'),
(194, 14, 'Bystřice pod Hostýnem'),
(195, 14, 'Holešov'),
(196, 14, 'Kroměříž'),
(197, 14, 'Luhačovice'),
(198, 14, 'Otrokovice'),
(199, 14, 'Rožnov pod Radhoštěm'),
(200, 14, 'Uherské Hradiště'),
(201, 14, 'Uherský Brod'),
(202, 14, 'Valašské Klobouky'),
(203, 14, 'Valašské Meziříčí'),
(204, 14, 'Vizovice'),
(205, 14, 'Vsetín'),
(206, 14, 'Zlín'),
(65535, 65535, 'Nezařazeno');



CREATE TABLE IF NOT EXISTS `vypecky_eplugin_sendmails` (
  `id_mail` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned default NULL,
  `mail` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_mail`),
  KEY `id_item` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;


INSERT INTO `vypecky_eplugin_sendmails` (`id_mail`, `id_item`, `id_article`, `mail`) VALUES
(1, 9, 0, 'jakubmatas@gmail.com'),
(2, 9, 0, 'cuba@vypecky.info');



CREATE TABLE IF NOT EXISTS `vypecky_eplugin_sendmailstexts` (
  `id_text` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned default NULL,
  `subject` varchar(500) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`id_text`),
  KEY `id_item` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


INSERT INTO `vypecky_eplugin_sendmailstexts` (`id_text`, `id_item`, `id_article`, `subject`, `text`) VALUES
(1, 9, NULL, 'Předmět emalu', 'Text mailu %pokus%.\r\n\r\npočet znaků je: %pocet%/%sudy[sudý/lichý]%\r\n\r\npočet znaků je: %pocet%/%sudy[sudý/lichý]%');



CREATE TABLE IF NOT EXISTS `vypecky_groups` (
  `id_group` smallint(3) unsigned NOT NULL auto_increment COMMENT 'ID skupiny',
  `name` varchar(15) default NULL COMMENT 'Nazev skupiny',
  `label` varchar(20) default NULL,
  `used` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;


INSERT INTO `vypecky_groups` (`id_group`, `name`, `label`, `used`) VALUES
(1, 'admin', 'Administrátor', 1),
(2, 'guest', 'Host', 1),
(3, 'user', 'Uživatel', 1),
(4, 'poweruser', 'uživatel s většími p', 1);



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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;


INSERT INTO `vypecky_items` (`id_item`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `group_admin`, `group_user`, `group_guest`, `group_poweruser`, `params`, `priority`, `id_category`, `id_module`) VALUES
(1, 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=true;theme=advanced', 0, 1, 1),
(2, 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=false;theme=simple', 0, 2, 1),
(3, 'Novinky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10;scrollpanel=2', 0, 3, 2),
(4, 'Login', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 4, 4),
(5, 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 5, 1),
(6, 'Reference', NULL, 'References', NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'width=800;height=600;smallwidth=200;smallheight=150', 0, 6, 19),
(7, 'Články', NULL, 'Articles', NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10', 0, 7, 20),
(8, 'Kontakty', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 8, 21),
(9, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 9, 7),
(10, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10', 0, 10, 22);



CREATE TABLE IF NOT EXISTS `vypecky_modules` (
  `id_module` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `mparams` varchar(100) default NULL,
  `datadir` varchar(100) default NULL,
  `dbtable1` varchar(50) default NULL,
  `dbtable2` varchar(50) default NULL,
  `dbtable3` varchar(50) default NULL,
  PRIMARY KEY  (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;


INSERT INTO `vypecky_modules` (`id_module`, `name`, `mparams`, `datadir`, `dbtable1`, `dbtable2`, `dbtable3`) VALUES
(1, 'text', NULL, NULL, 'texts', NULL, NULL),
(2, 'news', NULL, NULL, 'news', NULL, NULL),
(3, 'dwfiles', NULL, 'dwfiles', 'dwfiles', NULL, NULL),
(4, 'login', NULL, NULL, 'users', NULL, NULL),
(5, 'minigalery', NULL, 'minigalery', 'minigalery', NULL, NULL),
(6, 'workers', NULL, 'workers', 'workers', NULL, NULL),
(7, 'pokus', NULL, '', '', NULL, NULL),
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
(18, 'photogalery', NULL, 'photogalery', 'photogalery_galeries', 'photogalery_photos', NULL),
(19, 'references', NULL, 'references', 'references', 'texts', NULL),
(20, 'articles', NULL, NULL, 'articles', NULL, NULL),
(21, 'contacts', NULL, 'contacts', 'contacts', 'contacts_areas', 'contacts_cities'),
(22, 'products', NULL, 'products', 'products', 'products_documents', 'products_photos');



CREATE TABLE IF NOT EXISTS `vypecky_news` (
  `id_new` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
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
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;


INSERT INTO `vypecky_news` (`id_new`, `id_item`, `id_user`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time`, `deleted`) VALUES
(7, 3, 3, 'Novinky (Jitrničky) na Výpečkách', '<p>Tak první novinka na <a href="http://www.vypecky.info">Výpečkách</a> je\r\nvlasně zavedení <strong>novinek</strong>, kde můžete psát krátké novinky.\r\nTak hodně zdaru! :-D (těch novinek je tu až moc :-D )</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1210695435, 0),
(10, 3, 3, 'Upravený layout a je ještě LEPŠÍ!', 'Konečně jsem si našel trochu času a upravil layout výpeček. Teď by se\r\nměl korektně zobrazovat v FF a Opeře, jenom v IE zůstává pár chybiček.\r\nCelý mám (částečně) měnitelnou šířku takže potěším lidi co\r\nnepoužívají velké rozlišení.\r\n', 'Better layout', NULL, NULL, NULL, 1211050696, 0),
(33, 3, 3, 'Další novinka', 'ggfd gdfg fdg df', NULL, NULL, NULL, NULL, 1237116725, 1),
(19, 3, 3, 'Novinka v češtině s popisem', 'česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky ', 'English news with label', 'english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english ', NULL, NULL, 1218921753, 0),
(20, 3, 3, 'label', '&lt;b&gt;text&lt;/b&gt;', NULL, NULL, NULL, NULL, 1218975043, 1),
(21, 3, 3, 'cs label nový pěkný', 'fdsafsdafasfd', NULL, NULL, NULL, NULL, 1218979456, 1),
(22, 3, 3, 'Popis novinky', 'pokusný text', 'dsadasda', 'English news', NULL, NULL, 1222705858, 0),
(23, 3, 3, 'Zcela nová novinka a uprava', 'Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky ', 'Realy new News', 'News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text ', NULL, NULL, 1222705915, 0),
(24, 3, 3, 'Nová novinka', '&lt;b&gt;tučně&lt;/b&gt;', NULL, NULL, NULL, NULL, 1222705983, 1),
(25, 3, 3, 'pes', 'tak tohle je novinka o psu', NULL, NULL, NULL, NULL, 1222706254, 1),
(26, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204814, 1),
(27, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204880, 1),
(28, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205108, 1),
(29, 3, 3, 'jakubmatas_gmail_com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205120, 0),
(30, 3, 3, 'Upravený popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232206214, 0),
(31, 3, 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220144, 0),
(32, 3, 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220169, 0);



CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL auto_increment,
  `priority` smallint(2) NOT NULL default '0',
  `label` varchar(30) NOT NULL,
  `id_item` smallint(5) unsigned default NULL,
  `position` enum('left','right') NOT NULL default 'left',
  `enable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_panel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;


INSERT INTO `vypecky_panels` (`id_panel`, `priority`, `label`, `id_item`, `position`, `enable`) VALUES
(8, 60, 'NovinkyKyy', 3, 'right', 1);



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





CREATE TABLE IF NOT EXISTS `vypecky_products` (
  `id_product` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned default '1',
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) default NULL,
  `label_cs` varchar(400) default NULL,
  `text_cs` text,
  `label_en` varchar(400) default NULL,
  `text_en` text,
  `lebal_de` varchar(400) default NULL,
  `text_de` text,
  `main_image` varchar(200) default NULL,
  PRIMARY KEY  (`id_product`),
  KEY `id_item` (`id_item`,`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `lebal_de` (`lebal_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;


INSERT INTO `vypecky_products` (`id_product`, `id_item`, `id_user`, `add_time`, `edit_time`, `label_cs`, `text_cs`, `label_en`, `text_en`, `lebal_de`, `text_de`, `main_image`) VALUES
(2, 10, 1, 1239816255, 1239964857, 'pokus', '<p>Pokusný text produktu</p>\r\n<p> </p>\r\n<p class="para">Instead of lots of commands to output HTML (as seen in C or Perl),     PHP pages contain HTML with embedded code that does     "something" (in this case, output "Hi, I\\\\\\\\\\\\\\''m a PHP script!").     The PHP code is enclosed in special <a class="link" href="file:////home/cuba/Docs/PHP/html/language.basic-syntax.phpmode.html">start and end processing     instructions <code class="code"> and <code class="code">?&gt;</code></code></a> that allow you to jump into and out of "PHP mode."</p>\r\n<p class="para">What distinguishes PHP from something like client-side JavaScript     is that the code is executed on the server, generating HTML which     is then sent to the client. The client would receive     the results of running that script, but would not know     what the underlying code was. You can even configure your web server     to process all your HTML files with PHP, and then there\\\\\\\\\\\\\\''s really no     way that users can tell what you have up your sleeve.</p>\r\n<p class="para">The best things in using PHP are that it is extremely simple     for a newcomer, but offers many advanced features for     a professional programmer. Don\\\\\\\\\\\\\\''t be afraid reading the long     list of PHP\\\\\\\\\\\\\\''s features. You can jump in, in a short time, and     start writing simple scripts in a few hours.</p>\r\n<p class="para">Although PHP\\\\\\\\\\\\\\''s development is focused on server-side scripting,     you can do much more with it. Read on, and see more in the     <a class="link" href="file:////home/cuba/Docs/PHP/html/intro-whatcando.html">What can PHP do?</a> section,     or go right to the <a class="link" href="file:////home/cuba/Docs/PHP/html/tutorial.html">introductory     tutorial</a> if you are only interested in web programming.</p>', NULL, NULL, NULL, NULL, 'okno-titul-stitulkem.jpg'),
(3, 10, 1, 1239817269, 1239817269, 'Dveře k oknům', '<p class="para">Instead of lots of commands to output HTML (as seen in C or Perl),     PHP pages contain HTML with embedded code that does     "something" (in this case, output "Hi, I\\''m a PHP script!").     The PHP code is enclosed in special <a class="link" href="file:////home/cuba/Docs/PHP/html/language.basic-syntax.phpmode.html">start and end processing     instructions <code class="code">&lt;?php</code> and <code class="code">?&gt;</code></a> that allow you to jump into and out of "PHP mode."</p>\r\n<p class="para">What distinguishes PHP from something like client-side JavaScript     is that the code is executed on the server, generating HTML which     is then sent to the client. The client would receive     the results of running that script, but would not know     what the underlying code was. You can even configure your web server     to process all your HTML files with PHP, and then there\\''s really no     way that users can tell what you have up your sleeve.</p>\r\n<p class="para">The best things in using PHP are that it is extremely simple     for a newcomer, but offers many advanced features for     a professional programmer. Don\\''t be afraid reading the long     list of PHP\\''s features. You can jump in, in a short time, and     start writing simple scripts in a few hours.</p>\r\n<p class="para">Although PHP\\''s development is focused on server-side scripting,     you can do much more with it. Read on, and see more in the     <a class="link" href="file:////home/cuba/Docs/PHP/html/intro-whatcando.html">What can PHP do?</a> section,     or go right to the <a class="link" href="file:////home/cuba/Docs/PHP/html/tutorial.html">introductory     tutorial</a> if you are only interested in web programming.</p>', NULL, NULL, NULL, NULL, 'madagaskar-2-1226738485.jpg'),
(4, 10, 1, 1239875036, 1239965458, 'Dveře k okýnkům', '<p>Málokterý element má při návrhu fasády takovou důležitost, jako vchodové dveře. Dveře by měly být vizitkou každého domu a zároveň zárukou maximální bezpečnosti a trvanlivosti. <br /> <br />Široká nabídka dveřních systémů TROCAL sahá od balkonových dveří, přes vedlejší vstupní dveře s hliníkovým prahem, až po vchodové dveře tuhostí srovnatelné s hliníkovými. Pro sladění designu s okenními systémy nabízíme samozřejmě i provedení elegance. I vchodové dveře mohou být opatřeny osvědčenou barevnou technologií AcrylProtect, DecoStyle s designem dřeva, nebo TROCAL AluClip, skýtajícím takřka neomezenou barevnou volbu dle vzorníku RAL. Standardem je otvírání dovnitř i ven. Všechny dveřní systémy jsou koncipovány na nejvyšší tuhost. Rohy jsou zesíleny rohovými spojkami.</p>\r\n<p> </p>\r\n<p><a rel="lightbox" href="data/userfiles/budova-milenium-center-s-parkovacim-domem.jpg"><img title="budova-milenium-center-s-parkovacim-domem.jpg" src="data/userfiles/budova-milenium-center-s-parkovacim-domem.jpg" alt="budova-milenium-center-s-parkovacim-domem.jpg" width="300" height="225" /></a></p>\r\n<p><br />Všechny dveře TROCAL odpovídají požadavkům normy DIN 18103. <br /> <br />Skutečný "vzhled" vašim dveřím propůjčí kromě různých kombinací ze sloupků vyráběných přímo v produkci oken i dveřní výplně. Informujte se u svého dodavatele oken TROCAL. <br /> <br />Kombinace tvarů, skel, barev a rozměrů jdou do tisíců. Naši specialisté jsou připraveni Vám zpracovat a prezentovat tu neefektivnější variantu.</p>', NULL, NULL, NULL, NULL, 'okna.jpg'),
(5, 10, 1, 1239963926, 1239963926, 'Plastová okna innoNova', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vulputate nibh. Etiam ut odio. Donec vel mauris. Nullam ut urna. Morbi sapien lectus, rutrum ac, malesuada in, tincidunt at, ligula. Proin non ipsum. Nunc nulla lectus, varius non, facilisis id, eleifend sed, justo. Duis ac nulla non eros rutrum condimentum. Etiam nunc velit, feugiat ac, vestibulum id, blandit quis, orci. Phasellus ultricies, mauris semper fringilla commodo, enim arcu porta orci, et auctor ligula ante non est. In faucibus, libero vitae eleifend porta, purus sem elementum quam, et dapibus mi urna sit amet nisl. Nullam sodales. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nam libero dolor, porta nec, sodales eget, sodales sed, ipsum. Proin a arcu non mi adipiscing ultricies. In gravida, nisi id ornare cursus, eros felis dapibus justo, vitae convallis massa lorem laoreet ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut eu libero quis purus condimentum suscipit. Proin eu nisl quis eros suscipit tempor. Curabitur sed metus vitae metus molestie feugiat.</p>\r\n<p>Nunc tempus. Mauris risus. Praesent porttitor, risus vel euismod feugiat, justo ligula ornare sem, eu ullamcorper ante justo sit amet erat. Etiam bibendum. Donec pellentesque. Pellentesque pellentesque lectus at nibh. Duis sollicitudin, leo non dapibus congue, erat orci placerat ipsum, nec semper lectus mauris ut risus. Nam auctor ullamcorper mauris. Nulla non augue. Suspendisse luctus convallis ipsum. Cras a leo sed felis faucibus auctor. Maecenas nec erat eget elit cursus commodo.</p>\r\n<p>Aliquam ut nulla. Suspendisse sodales libero fringilla odio. Vivamus turpis nisi, aliquam in, vehicula vel, tristique fermentum, nisl. Mauris urna justo, placerat vel, sodales cursus, mollis a, turpis. Quisque metus. Maecenas et magna eget urna ullamcorper fringilla. Vivamus ut nisi. Praesent quis sapien sit amet urna faucibus interdum. Vestibulum posuere, quam eleifend egestas tincidunt, nisi sapien viverra mauris, eget luctus dui nisl eu augue. Sed dictum, eros vitae mattis mattis, nunc augue lacinia eros, vel cursus urna ligula quis leo. Nam tempor. Nulla facilisi. Nunc risus. Pellentesque tortor. Donec purus. Morbi suscipit.</p>', NULL, NULL, NULL, NULL, 'okno-titul.jpg');



CREATE TABLE IF NOT EXISTS `vypecky_references` (
  `id_reference` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) default NULL,
  `label_cs` text,
  `name_en` varchar(300) default NULL,
  `label_en` text,
  `name_de` varchar(300) default NULL,
  `label_de` text,
  `file` varchar(200) default NULL,
  `changed_time` int(11) default NULL,
  PRIMARY KEY  (`id_reference`),
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `name_cs` (`name_cs`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `name_en` (`name_en`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `name_de` (`name_de`),
  FULLTEXT KEY `label_de` (`label_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;


INSERT INTO `vypecky_references` (`id_reference`, `id_item`, `name_cs`, `label_cs`, `name_en`, `label_en`, `name_de`, `label_de`, `file`, `changed_time`) VALUES
(7, 6, 'Stránky hudba valmez 2009', '<p>tránky k projektu valašského CD, které vychází každých 5 let. obsahují různé kapely od známých, jako mňága a žďorp až po úplné neznámé.</p>', 'english label', NULL, NULL, NULL, 'madagaskar-2-1226738485.jpg', 1238325126);



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


INSERT INTO `vypecky_sections` (`id_section`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `priority`) VALUES
(1, 'section 1', NULL, NULL, NULL, NULL, NULL, 0),
(2, 'section 2', NULL, NULL, NULL, NULL, NULL, 0),
(3, 'section 3', NULL, NULL, NULL, NULL, NULL, 0),
(4, 'section 4', NULL, NULL, NULL, NULL, NULL, 0),
(5, 'účet', NULL, NULL, NULL, NULL, NULL, 0);



CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `text_cs` mediumtext,
  `changed_time` int(11) default NULL,
  `text_en` mediumtext,
  `text_de` mediumtext,
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;


INSERT INTO `vypecky_texts` (`id_text`, `id_item`, `text_cs`, `changed_time`, `text_en`, `text_de`) VALUES
(9, 2, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>', 1237143858, NULL, NULL),
(8, 1, '<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/userimages/imag0001.jpg" alt="imag0001.jpg" width="201" height="149" /></a><strong><span style="font-size: xx-large;">Od 29.3. méme na skladě nové druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v barvách duhy</span></strong></p>\r\n<p> </p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p> </p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p> </p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p> </p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p> </p>', 1239205247, NULL, NULL),
(10, 5, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', 1237145467, NULL, NULL),
(11, 6, '<ul>\r\n<li>Další reference</li>\r\n<li>dalfvdv</li>\r\n<li>fvfvfdvdf</li>\r\n<li>gvfdfvfdvfdv</li>\r\n<li>fdvfdvdfvdfv</li>\r\n<li>fdvdfvdfvdfvfdv</li>\r\n<li>dfvdfvdfvdfvf</li>\r\n<li>vdfvdfvdvfd</li>\r\n</ul>', 1238325822, NULL, NULL);



CREATE TABLE IF NOT EXISTS `vypecky_userfiles` (
  `id_file` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  `file` varchar(50) NOT NULL,
  `type` enum('file','image','flash') NOT NULL default 'file',
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `size` int(11) default NULL,
  `time` int(10) unsigned default NULL,
  PRIMARY KEY  (`id_file`),
  KEY `id_category` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;


INSERT INTO `vypecky_userfiles` (`id_file`, `id_item`, `id_article`, `id_user`, `file`, `type`, `width`, `height`, `size`, `time`) VALUES
(31, 1, 1, 3, 'obraz-02.jpg', 'file', NULL, NULL, 55552, 1237142860),
(46, 1, 1, 1, 'recoverdatareiserfstrial.tar.gz', 'file', NULL, NULL, 4346556, 1238771976),
(48, 7, 1, 1, 'anree1.jpg', 'image', 1200, 1600, 674756, 1239023394),
(49, 7, 1, 1, 'anree2.jpg', 'image', 1200, 1600, 674756, 1239023578),
(50, 7, 1, 1, 'teo.jpg', 'image', 46, 58, 1320, 1239023663),
(51, 7, 1, 1, 'buttony.swf', 'flash', 120, 120, 2981, 1239029679),
(53, 10, 4, 1, 'budova-milenium-center-s-parkovacim-domem.jpg', 'image', 2048, 1536, 1038239, 1239874962);



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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;


INSERT INTO `vypecky_userimages` (`id_file`, `id_item`, `id_article`, `id_user`, `file`, `width`, `height`, `size`, `time`) VALUES
(59, 1, 1, 1, 'madagaskar-2-1226738485.jpg', 640, 360, 42248, 1238776317),
(54, 1, 1, 1, 'anree2.jpg', 1200, 1600, 674756, 1238774419),
(60, 1, 1, 1, 'imag0001.jpg', 1600, 1200, 566328, 1238776360),
(58, 1, 1, 1, 'teo.jpg', 46, 58, 1320, 1238775865),
(61, 1, 1, 1, 'imag0014.jpg', 1600, 1200, 479262, 1238776956),
(62, 7, 1, 1, 'anree.jpg', 1200, 1600, 674756, 1239003699);



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


INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', 'guest', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0);
