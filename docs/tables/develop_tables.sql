-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Úterý 04. května 2010, 15:13
-- Verze MySQL: 5.1.41
-- Verze PHP: 5.3.2-1ubuntu4

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
-- Struktura tabulky `vypecky_actions`
--

DROP TABLE IF EXISTS `vypecky_actions`;
CREATE TABLE IF NOT EXISTS `vypecky_actions` (
  `id_action` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `subname_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `text_clear_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `urlkey_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `note_cs` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_en` varchar(50) DEFAULT NULL,
  `subname_en` varchar(200) DEFAULT NULL,
  `text_en` text,
  `text_clear_en` text,
  `urlkey_en` varchar(200) DEFAULT NULL,
  `note_en` varchar(500) DEFAULT NULL,
  `name_de` varchar(50) DEFAULT NULL,
  `subname_de` varchar(200) DEFAULT NULL,
  `text_de` text,
  `text_clear_de` text,
  `urlkey_de` varchar(200) DEFAULT NULL,
  `note_de` varchar(500) DEFAULT NULL,
  `author` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `start_date` date NOT NULL,
  `stop_date` date DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `preprice` smallint(6) DEFAULT NULL,
  `place` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `time_add` datetime NOT NULL,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_action`),
  UNIQUE KEY `urlkey_cs` (`urlkey_cs`),
  UNIQUE KEY `urlkey_de` (`urlkey_de`),
  KEY `id_user` (`id_user`),
  FULLTEXT KEY `label_cs` (`name_cs`),
  FULLTEXT KEY `label_en` (`name_en`),
  FULLTEXT KEY `label_de` (`name_de`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`),
  FULLTEXT KEY `place` (`place`),
  FULLTEXT KEY `subname_en` (`subname_en`),
  FULLTEXT KEY `subname_cs` (`subname_cs`),
  FULLTEXT KEY `subname_de` (`subname_de`),
  FULLTEXT KEY `author` (`author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `vypecky_actions`
--

INSERT INTO `vypecky_actions` (`id_action`, `id_category`, `id_user`, `name_cs`, `subname_cs`, `text_cs`, `text_clear_cs`, `urlkey_cs`, `note_cs`, `name_en`, `subname_en`, `text_en`, `text_clear_en`, `urlkey_en`, `note_en`, `name_de`, `subname_de`, `text_de`, `text_clear_de`, `urlkey_de`, `note_de`, `author`, `start_date`, `stop_date`, `image`, `time`, `price`, `preprice`, `place`, `public`, `time_add`, `changed`) VALUES
(1, 119, 1, 'text api', NULL, '<h2 class="HH2">Master your everyday correspondence</h2>\r\n<p>You write letters or short notes every day. Always in a hurry,         everything needs to be ready <em>now...</em> For that, you need a word         processor that relieves you of routine tasks:</p>\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="6">\r\n<tbody>\r\n<tr>\r\n<td valign="TOP">\r\n<p><img src="http://www.softmaker.com/english/images/bullet1.gif" border="0" alt="" hspace="4" vspace="6" width="7" height="7" align="BASELINE" /></p>\r\n</td>\r\n<td valign="TOP">\r\n<p>When you <strong>write a letter</strong> with TextMaker, you don''t have to type             in the recipient by hand. You simply pick him or her from TextMaker''s             built-in address book. TextMaker quickly inserts the recipient''s name             and address in your document, and you can concentrate on the content             of your letter.</p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td valign="TOP">\r\n<p><img src="http://www.softmaker.com/english/images/bullet1.gif" border="0" alt="" hspace="4" vspace="6" width="7" height="7" align="BASELINE" /></p>\r\n</td>\r\n<td valign="TOP">\r\n<p>TextMaker <strong>checks your spelling</strong> while you are typing, marking             typos with a red underline. This is not limited to just English:             TextMaker supports spell checking in 20 languages, even in Russian or Arabic.</p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td valign="TOP">\r\n<p><img src="http://www.softmaker.com/english/images/bullet1.gif" border="0" alt="" hspace="4" vspace="6" width="7" height="7" align="BASELINE" /></p>\r\n</td>\r\n<td valign="TOP">\r\n<p>Do you need an <strong>envelope</strong> for your letter? For this, TextMaker             comes with an envelope wizard that frees you from manually measuring             and positioning the address and sender fields.</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>TextMaker does all of this automatically. The only thing it doesn''t         do for you is actually write the letter.</p>\r\n<p><!-- $MVD$:spaceretainer() --></p>\r\n<h2 class="HH2">Using TextMaker at the office</h2>\r\n<p>When you use a word processor at the office, data exchange plays a         pivotal role. You receive a Word file by e-mail, work on it, then         forward it to somebody else.</p>\r\n<p><span style="font-size: x-small;">Many features in TextMaker have been designed with the         goal of allowing TextMaker users to work seamlessly in Microsoft         Word-dominated environments:</span></p>\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="6">\r\n<tbody>\r\n<tr>\r\n<td valign="TOP">\r\n<p><img src="http://www.softmaker.com/english/images/bullet1.gif" border="0" alt="" hspace="4" vspace="6" width="7" height="7" align="BASELINE" /></p>\r\n</td>\r\n<td valign="TOP">\r\n<p><span style="font-size: x-small;">TextMaker offers <strong>superior compatibility with             Microsoft Word</strong>. You won''t find any other word processor that is             as capable of handling complex Microsoft Word files as TextMaker.</span></p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="6">\r\n<tbody>\r\n<tr>\r\n<td valign="TOP">\r\n<p><img src="http://www.softmaker.com/english/images/bullet1.gif" border="0" alt="" hspace="4" vspace="6" width="7" height="7" align="BASELINE" /></p>\r\n</td>\r\n<td valign="TOP">\r\n<p><strong>Microsoft Word 2007:</strong> In addition to the DOC file format from             Word 2003 and before, TextMaker now faithfully reads <strong><span style="text-decoration: underline;">and</span></strong> writes the modern <strong>DOCX format</strong> of Microsoft Word 2007. With             that, the barriers to exchanging documents are gone.</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="6">\r\n<tbody>\r\n<tr>\r\n<td valign="TOP">\r\n<p><img src="http://www.softmaker.com/english/images/bullet1.gif" border="0" alt="" hspace="4" vspace="6" width="7" height="7" align="BASELINE" /></p>\r\n</td>\r\n<td valign="TOP">\r\n<p><strong>Tracking changes:</strong> As soon as you switch on the <strong>Track changes</strong> feature, TextMaker records all changes made to a document. If you let             a colleague or superior review this document, he or she can accept or             reject your changes one by one, no matter whether they work with             TextMaker or with Word.</p>\r\n</td>\r\n<td rowspan="2" valign="TOP" width="260">\r\n<p><span style="font-size: x-small;"><img src="http://www.softmaker.com/english/images/tml10_revisions_toolbar.gif" border="0" alt="" hspace="0" vspace="0" width="244" height="84" /></span></p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td valign="TOP">\r\n<p><img src="http://www.softmaker.com/english/images/bullet1.gif" border="0" alt="" hspace="4" vspace="6" width="7" height="7" align="BASELINE" /></p>\r\n</td>\r\n<td valign="TOP">\r\n<p><strong>Comments:</strong> TextMaker''s <strong>comments</strong> feature is just as             compatible: Comments and annotations appear as <strong>balloons</strong> in the             right page margin – just like in Microsoft Word.</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>It goes without saying that you can export documents to PDF format         and, if you so wish, send them out by e-mail.</p>', 'Master your everyday correspondence\r\nYou write letters or short notes every day. Always in a hurry,         everything needs to be ready now... For that, you need a word         processor that relieves you of routine tasks:\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nWhen you write a letter with TextMaker, you don''t have to type             in the recipient by hand. You simply pick him or her from TextMaker''s             built-in address book. TextMaker quickly inserts the recipient''s name             and address in your document, and you can concentrate on the content             of your letter.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nTextMaker checks your spelling while you are typing, marking             typos with a red underline. This is not limited to just English:             TextMaker supports spell checking in 20 languages, even in Russian or Arabic.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nDo you need an envelope for your letter? For this, TextMaker             comes with an envelope wizard that frees you from manually measuring             and positioning the address and sender fields.\r\n\r\n\r\n\r\n\r\nTextMaker does all of this automatically. The only thing it doesn''t         do for you is actually write the letter.\r\n\r\nUsing TextMaker at the office\r\nWhen you use a word processor at the office, data exchange plays a         pivotal role. You receive a Word file by e-mail, work on it, then         forward it to somebody else.\r\nMany features in TextMaker have been designed with the         goal of allowing TextMaker users to work seamlessly in Microsoft         Word-dominated environments:\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nTextMaker offers superior compatibility with             Microsoft Word. You won''t find any other word processor that is             as capable of handling complex Microsoft Word files as TextMaker.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nMicrosoft Word 2007: In addition to the DOC file format from             Word 2003 and before, TextMaker now faithfully reads and writes the modern DOCX format of Microsoft Word 2007. With             that, the barriers to exchanging documents are gone.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nTracking changes: As soon as you switch on the Track changes feature, TextMaker records all changes made to a document. If you let             a colleague or superior review this document, he or she can accept or             reject your changes one by one, no matter whether they work with             TextMaker or with Word.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nComments: TextMaker''s comments feature is just as             compatible: Comments and annotations appear as balloons in the             right page margin – just like in Microsoft Word.\r\n\r\n\r\n\r\n\r\nIt goes without saying that you can export documents to PDF format         and, if you so wish, send them out by e-mail.', 'text-api', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2010-03-29', NULL, NULL, '20:00:00', NULL, NULL, NULL, 1, '2010-03-29 14:47:39', '2010-03-29 14:47:39'),
(2, 119, 1, 'C''EST LA VIE aneb to je život', 'Valašský špalíček 2010', '<p><span style="font-family: arial,helvetica,sans-serif; font-size: small;">Multimediální představení – taneční a multimediální projekt, který vznikl ve spolupráci tanečního oboru valašskomeziříčské ZUŠ s místním Kulturním zařízením a městem Čadca. </span></p>\r\n<p><span style="font-family: arial,helvetica,sans-serif;"><span style="font-size: small;"><span style="text-decoration: underline;">Předkapela:</span> legendární funk, RnB a bluesová zpěvačka z Chicaga <span style="color: #ff0000;"><strong>Mrs. DEITRA FARR</strong></span></span></span></p>\r\n<p><span style="font-family: arial,helvetica,sans-serif;"><em><span style="font-size: small;">Vstupné 150 Kč.</span></em></span></p>', 'Multimediální představení – taneční a multimediální projekt, který vznikl ve spolupráci tanečního oboru valašskomeziříčské ZUŠ s místním Kulturním zařízením a městem Čadca. \r\nPředkapela: legendární funk, RnB a bluesová zpěvačka z Chicaga Mrs. DEITRA FARR\r\nVstupné 150 Kč.', 'CEST-LA-VIE-aneb-to-je-zivot', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2010-06-17', NULL, NULL, '19:30:00', NULL, NULL, NULL, 1, '2010-05-03 12:36:37', '2010-05-03 12:36:37');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_articles`
--

DROP TABLE IF EXISTS `vypecky_articles`;
CREATE TABLE IF NOT EXISTS `vypecky_articles` (
  `id_article` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_cat` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned DEFAULT '1',
  `add_time` datetime NOT NULL,
  `edit_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_user_last_edit` smallint(6) DEFAULT NULL,
  `viewed` smallint(6) NOT NULL DEFAULT '0',
  `name_cs` varchar(400) DEFAULT NULL,
  `text_cs` text,
  `text_clear_cs` text,
  `urlkey_cs` varchar(100) DEFAULT NULL,
  `name_en` varchar(400) DEFAULT NULL,
  `text_en` text,
  `text_clear_en` text,
  `urlkey_en` varchar(100) DEFAULT NULL,
  `name_de` varchar(400) DEFAULT NULL,
  `text_de` text,
  `text_clear_de` text,
  `urlkey_de` varchar(100) DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_article`),
  UNIQUE KEY `urlkey_cs` (`urlkey_cs`),
  UNIQUE KEY `urlkey_en` (`urlkey_en`),
  UNIQUE KEY `urlkey_de` (`urlkey_de`),
  FULLTEXT KEY `label_cs` (`name_cs`),
  FULLTEXT KEY `label_en` (`name_en`),
  FULLTEXT KEY `lebal_de` (`name_de`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `vypecky_articles`
--

INSERT INTO `vypecky_articles` (`id_article`, `id_cat`, `id_user`, `add_time`, `edit_time`, `is_user_last_edit`, `viewed`, `name_cs`, `text_cs`, `text_clear_cs`, `urlkey_cs`, `name_en`, `text_en`, `text_clear_en`, `urlkey_en`, `name_de`, `text_de`, `text_clear_de`, `urlkey_de`, `public`) VALUES
(1, 118, 1, '2010-03-29 13:36:51', '2010-04-12 18:56:03', NULL, 5, 'test API', '<p>Ve velkém urychlovači nejmenších částic (LHC) <a class="vvword" href="http://onlineshopcz.takeit.cz/lednicky-a-dalsi-vyhodne-nakupy-7156472-7156472?279360&amp;rtype=V&amp;rmain=149521&amp;ritem=7156472&amp;rclanek=6138592&amp;rslovo=457837&amp;showdirect=1" target="_blank">má</a> v úterý začít klíčová fáze pokusů. Cílem je v Evropské organizaci pro jaderný výzkum (CERN) vyvolat srážky nejmenších částeček hmoty. Vědci do obřího tubusu vpustí dosud rekordní množství energie a pokusí se tak napodobit podmínky, při kterých se zrodil vesmír.</p>\r\n<p>Velkým třeskem začalo podle současných teorií před 13,7 miliardy let pozorovatelné rozpínání vesmíru. Odpůrci experimentu se obávají vzniku černých děr, které pohltí svět.</p>\r\n<h3 class="not4bbtext tit">Zatím největší experiment v CERN</h3>\r\n<p>Dva paprsky částic kolují v prstenci energií 3 500 miliard elektronvoltů (3,5 TeV). To je dosavadní světový rekord. Energie se bude v následujících dnech zvyšovat. "První pokus s kolizemi o síle 7 TeV (3,5 TeV na každý paprsek) je plánován na 30. března," uvedl CERN.</p>\r\n<p>Elektronvolt odpovídá kinetické energii, kterou získá elektron urychlený ve vakuu napětím jednoho voltu.</p>\r\n<p>"Než vyvoláme srážky, může to trvat hodiny nebo dokonce dny," upozornil podle agentury Reuters generální ředitel CERN Rolf Heuer. "Už jenom setkání paprsků je samo o sobě výzva. Je to tak trochu jako vypálit z obou břehů Atlantiku jehly tak, aby se v polovině cesty srazily," přiblížil obtížnost experimentu technologický ředitel CERN Steve Myers.</p>\r\n<p>Každá ze srážek nejmenších částeček hmoty má vytvořit jakýsi "malý velký třesk" poskytující údaje, které budou v příštích letech analyzovat tisícovky vědců.</p>\r\n<p>Jakmile budou vysokorychlostní srážky spuštěny, měly by podle plánů nepřetržitě pokračovat 18 až 24 měsíců s výjimkou údajně krátké technické odstávky na konci roku.</p>\r\n<p>LHC má umožnit <a class="vvword" href="http://s-property-sro-1.takeit.cz/moderni-apartmany-v-ostrovni-ulici-v-praze-3678531-3678531?279935&amp;rtype=V&amp;rmain=126615&amp;ritem=3678531&amp;rclanek=6138592&amp;rslovo=420944&amp;showdirect=1" target="_blank">nový</a> pohled na podstatu hmoty a vesmíru. Vědci si od experimentu slibují odhalení takzvané temné či skryté hmoty, jejíž existence nikdy nebyla prokázána.</p>\r\n<p>Odborníci se domnívají, že dosud známe jen pět procent vesmíru a neviditelný zbytek tvoří právě skrytá hmota, zhruba čtvrtina vesmíru, a skrytá energie, 70 procent. "Pokud se nám podaří odhalit skrytou hmotu, naše znalosti budou zahrnovat 30 procent vesmíru, což by znamenalo obrovský pokrok," řekl Heuer.</p>', 'Ve velkém urychlovači nejmenších částic (LHC) má v úterý začít klíčová fáze pokusů. Cílem je v Evropské organizaci pro jaderný výzkum (CERN) vyvolat srážky nejmenších částeček hmoty. Vědci do obřího tubusu vpustí dosud rekordní množství energie a pokusí se tak napodobit podmínky, při kterých se zrodil vesmír.\r\nVelkým třeskem začalo podle současných teorií před 13,7 miliardy let pozorovatelné rozpínání vesmíru. Odpůrci experimentu se obávají vzniku černých děr, které pohltí svět.\r\nZatím největší experiment v CERN\r\nDva paprsky částic kolují v prstenci energií 3 500 miliard elektronvoltů (3,5 TeV). To je dosavadní světový rekord. Energie se bude v následujících dnech zvyšovat. "První pokus s kolizemi o síle 7 TeV (3,5 TeV na každý paprsek) je plánován na 30. března," uvedl CERN.\r\nElektronvolt odpovídá kinetické energii, kterou získá elektron urychlený ve vakuu napětím jednoho voltu.\r\n"Než vyvoláme srážky, může to trvat hodiny nebo dokonce dny," upozornil podle agentury Reuters generální ředitel CERN Rolf Heuer. "Už jenom setkání paprsků je samo o sobě výzva. Je to tak trochu jako vypálit z obou břehů Atlantiku jehly tak, aby se v polovině cesty srazily," přiblížil obtížnost experimentu technologický ředitel CERN Steve Myers.\r\nKaždá ze srážek nejmenších částeček hmoty má vytvořit jakýsi "malý velký třesk" poskytující údaje, které budou v příštích letech analyzovat tisícovky vědců.\r\nJakmile budou vysokorychlostní srážky spuštěny, měly by podle plánů nepřetržitě pokračovat 18 až 24 měsíců s výjimkou údajně krátké technické odstávky na konci roku.\r\nLHC má umožnit nový pohled na podstatu hmoty a vesmíru. Vědci si od experimentu slibují odhalení takzvané temné či skryté hmoty, jejíž existence nikdy nebyla prokázána.\r\nOdborníci se domnívají, že dosud známe jen pět procent vesmíru a neviditelný zbytek tvoří právě skrytá hmota, zhruba čtvrtina vesmíru, a skrytá energie, 70 procent. "Pokud se nám podaří odhalit skrytou hmotu, naše znalosti budou zahrnovat 30 procent vesmíru, což by znamenalo obrovský pokrok," řekl Heuer.', 'test-API', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_bands`
--

DROP TABLE IF EXISTS `vypecky_bands`;
CREATE TABLE IF NOT EXISTS `vypecky_bands` (
  `id_band` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `add_time` datetime NOT NULL,
  `edit_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_user_last_edit` smallint(6) DEFAULT NULL,
  `viewed` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(400) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `image` varchar(100) DEFAULT NULL,
  `clips` varchar(1000) DEFAULT NULL,
  `text_clear` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `urlkey` varchar(100) DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_band`),
  UNIQUE KEY `urlkey` (`urlkey`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `text_clear` (`text_clear`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Vypisuji data pro tabulku `vypecky_bands`
--

INSERT INTO `vypecky_bands` (`id_band`, `add_time`, `edit_time`, `id_user_last_edit`, `viewed`, `name`, `text`, `image`, `clips`, `text_clear`, `urlkey`, `public`) VALUES
(1, '2010-05-02 12:37:04', '2010-05-04 14:04:47', 1, 7, 'Kabát', '<p>Výbušninu ve voze Nissan Pathfinder objevil policista, který procházel  kolem a všiml si kouře, který z auta vycházel. Pyrotechnici pak našli  výbušné látky, benzin, propan a ohořelé dráty, uvedla agentura Reuters.</p>\r\n<p>Policie upřesnila, že dým vycházel z balíčku položeného v zadní části  odstaveného auta. Hasiči dokonce uhasili malý požár.</p>\r\n<p>Kvůli hrozícímu nebezpečí muselo být preventivně několik bloků v  turisticky oblíbené části Manhattanu, na čas bylo podle BBC uzavřeno i  metro.</p>\r\n<p>"Měli jsme velké štěstí. Vyhnuli jsme se útoku, který by měl tragické  následky. Kdo za nim stojí a proč, to nevíme, " řekl BBC starosta New  Yorku Michael Bloomberg.</p>\r\n<p>O vážnosti hrozby svědčí i reakce prezidenta Baracka Obamy. Ten  poděkoval policii za rychlou akci a zároveň nabídl případnou pomoc.</p>', 'james_foto_1.jpg', NULL, 'Výbušninu ve voze Nissan Pathfinder objevil policista, který procházel  kolem a všiml si kouře, který z auta vycházel. Pyrotechnici pak našli  výbušné látky, benzin, propan a ohořelé dráty, uvedla agentura Reuters.\r\nPolicie upřesnila, že dým vycházel z balíčku položeného v zadní části  odstaveného auta. Hasiči dokonce uhasili malý požár.\r\nKvůli hrozícímu nebezpečí muselo být preventivně několik bloků v  turisticky oblíbené části Manhattanu, na čas bylo podle BBC uzavřeno i  metro.\r\n"Měli jsme velké štěstí. Vyhnuli jsme se útoku, který by měl tragické  následky. Kdo za nim stojí a proč, to nevíme, " řekl BBC starosta New  Yorku Michael Bloomberg.\r\nO vážnosti hrozby svědčí i reakce prezidenta Baracka Obamy. Ten  poděkoval policii za rychlou akci a zároveň nabídl případnou pomoc.', 'Kabat', 1),
(2, '2010-05-02 16:15:01', '2010-05-04 14:08:16', 1, 4, 'MARTIN CHODÚR', '<p>Vítěz první Československé Superstar a hudebník tělem i duší Martin  Chodúr, není ve zpěvu žádným nováčkem, je studentem Janáčkovy  konzervatoře v Ostravě, byl také dlouholetým zpěvákem kapely Robson.  Jeho hlas a perfektní intonace Vás nechá stát před podiem s otevřenou  pusou, neuvěřitelné taneční kreace Vám naopak vyčarují úsměv na rtech.  Na Hrachovce Martin vystoupí se svou novou kapelou, složenou ze  špičkových hudebníků, zazpívá skladby z připravovaného alba,ale také  songy, které zazněly v Superstar. Autor fotografie: Bronislav Šimončík.</p>\r\n<p> </p>\r\n<p>\r\n<object width="425" height="350" data="http://www.youtube.com/v/RrFcUIvASZQ" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/RrFcUIvASZQ" />\r\n<param name="src" value="http://www.youtube.com/v/RrFcUIvASZQ" />\r\n</object>\r\n</p>', '424668_superstar4-martin-chodur.jpg', 'http://www.youtube.com/v/RrFcUIvASZQ', 'Vítěz první Československé Superstar a hudebník tělem i duší Martin  Chodúr, není ve zpěvu žádným nováčkem, je studentem Janáčkovy  konzervatoře v Ostravě, byl také dlouholetým zpěvákem kapely Robson.  Jeho hlas a perfektní intonace Vás nechá stát před podiem s otevřenou  pusou, neuvěřitelné taneční kreace Vám naopak vyčarují úsměv na rtech.  Na Hrachovce Martin vystoupí se svou novou kapelou, složenou ze  špičkových hudebníků, zazpívá skladby z připravovaného alba,ale také  songy, které zazněly v Superstar. Autor fotografie: Bronislav Šimončík.\r\n \r\n\r\n\r\n\r\n\r\n\r\n', 'MARTIN-CHODUR', 1),
(3, '2010-05-02 16:15:43', '2010-05-04 14:08:28', 1, 4, 'Polemic', '<p>Slovenská SKA legenda zraje jako víno. Sami si říkají SKA-pionýři nebo  taky jedna z nejdůležitějších SKA-reggae kapel ve střední Evropě a  nadsázka je to jenom trochu. Nejen že Polemic letos slaví 20 let od  vzniku, ale za dobu své existence předskakovali také slavným formacím,  jako je SKA-P nebo Yellow Umbrella. Melodicky vyvážený a rytmicky skvěle  rozjetý královský styl Polemic nás bavil poprvé a baví nás i po sté.</p>\r\n<p> </p>\r\n<p>\r\n<object width="425" height="350" data="http://www.youtube.com/v/95tvO0tEZ2E" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/95tvO0tEZ2E" />\r\n<param name="src" value="http://www.youtube.com/v/95tvO0tEZ2E" />\r\n</object>\r\n</p>\r\n<p> </p>\r\n<p>\r\n<object width="425" height="350" data="http://www.youtube.com/v/WIBPMHq2TgU" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/WIBPMHq2TgU" />\r\n<param name="src" value="http://www.youtube.com/v/WIBPMHq2TgU" />\r\n</object>\r\n</p>', 'polemic.jpg', 'http://www.youtube.com/v/95tvO0tEZ2E;http://www.youtube.com/v/WIBPMHq2TgU', 'Slovenská SKA legenda zraje jako víno. Sami si říkají SKA-pionýři nebo  taky jedna z nejdůležitějších SKA-reggae kapel ve střední Evropě a  nadsázka je to jenom trochu. Nejen že Polemic letos slaví 20 let od  vzniku, ale za dobu své existence předskakovali také slavným formacím,  jako je SKA-P nebo Yellow Umbrella. Melodicky vyvážený a rytmicky skvěle  rozjetý královský styl Polemic nás bavil poprvé a baví nás i po sté.\r\n \r\n\r\n\r\n\r\n\r\n\r\n\r\n \r\n\r\n\r\n\r\n\r\n\r\n', 'Polemic', 1),
(4, '2010-05-02 16:16:27', '2010-05-04 14:08:08', 1, 32, 'Charlie Straight', '<p>Debut She´s a Good Swimmer rozvířil v loňském roce české hudební  jezírko, a to tak, že vlny se na něm houpu ještě teď. Není se čemu  divit. Kapela s výrazně přiznaným britským zvukem si totiž vyhrnula  rukávy a chystá se nám ukázat, jak má muzika vypadat. Dokonale  vybroušené tóny hravých kytarových melodií, kterými se Charlie Straight  pyšní, zaujaly nejen u nás, ale také v Neměcku nebo ve Velké Británii.  Přestože průměrný věk členů kapely je 22 let, dělají tihle kluci muziku,  kterou jsme v Čechách dlouho neslyšeli.fdsfdsf</p>', '64485_straight.JPG', NULL, 'Debut She´s a Good Swimmer rozvířil v loňském roce české hudební  jezírko, a to tak, že vlny se na něm houpu ještě teď. Není se čemu  divit. Kapela s výrazně přiznaným britským zvukem si totiž vyhrnula  rukávy a chystá se nám ukázat, jak má muzika vypadat. Dokonale  vybroušené tóny hravých kytarových melodií, kterými se Charlie Straight  pyšní, zaujaly nejen u nás, ale také v Neměcku nebo ve Velké Británii.  Přestože průměrný věk členů kapely je 22 let, dělají tihle kluci muziku,  kterou jsme v Čechách dlouho neslyšeli.fdsfdsf', 'Charlie-Straight', 0),
(5, '2010-05-02 16:20:30', '2010-05-04 14:02:42', 1, 30, 'Jan Budař a Eliščin band', '<p>Tahle muzika je zase trochu jiný šálek čaje. Vlastně to není ani tak  muzika jako hudební atrakce. Rýmy a jazykohry, ze kterých skládá Budař  texty, se prolínají s hudbou která má kořeny ve folku, jazzu nebo blues a  sem tam zabrousí i do rockových forem. Výsledkem je nepopsatelná  atmosféra každého koncertu, který rozhodně stojí za to. Na posledním  albu Jana Budaře a Eliščina bandu navíc zpívá mimo jiné i Lucie Bílá, a  to tak, jak jsme ji ještě neslyšeli.</p>\r\n<p>\r\n<object width="425" height="350" data="http://www.youtube.com/v/g2SF6kw5XVA" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/g2SF6kw5XVA" />\r\n<param name="src" value="http://www.youtube.com/v/g2SF6kw5XVA" />\r\n</object>\r\n</p>\r\n<p> </p>\r\n<p>\r\n<object width="425" height="350" data="http://www.youtube.com/v/K9uoNUIzEFo" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/K9uoNUIzEFo" />\r\n<param name="src" value="http://www.youtube.com/v/K9uoNUIzEFo" />\r\n</object>\r\n</p>', 'foto-008.jpg', 'http://www.youtube.com/v/g2SF6kw5XVA;http://www.youtube.com/v/K9uoNUIzEFo', 'Tahle muzika je zase trochu jiný šálek čaje. Vlastně to není ani tak  muzika jako hudební atrakce. Rýmy a jazykohry, ze kterých skládá Budař  texty, se prolínají s hudbou která má kořeny ve folku, jazzu nebo blues a  sem tam zabrousí i do rockových forem. Výsledkem je nepopsatelná  atmosféra každého koncertu, který rozhodně stojí za to. Na posledním  albu Jana Budaře a Eliščina bandu navíc zpívá mimo jiné i Lucie Bílá, a  to tak, jak jsme ji ještě neslyšeli.\r\n\r\n\r\n\r\n\r\n\r\n\r\n \r\n\r\n\r\n\r\n\r\n\r\n', 'Jan-Budar-a-Eliscin-band', 1),
(6, '2010-05-02 16:21:41', '2010-05-04 15:12:45', 1, 5, 'The Plastic People of the Universe', '<p>Zakázaní, undergroundoví, kdysi věznění legendární členové legendární  kapely The Plastic People of the Universe hrají od roku 1968. To vypadá  na pěkně vyčpělou bandu, řekli bychom. Stačí však navštívit jediný  koncert a poznáte, že opak je pravdou. K dokonalosti dovedené  elektronické zvuky podtržené psychedelickým zpěvem a množstvím různých  nástrojů ve spojení s několikahodinovým vystoupením těchto vážených pánů  a jedné dámy vás zanechá s otevřenými ústy.</p>\r\n<p> </p>\r\n<p>\r\n<object width="425" height="350" data="http://www.youtube.com/v/YBOnDwxuLaE" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/YBOnDwxuLaE" />\r\n<param name="src" value="http://www.youtube.com/v/YBOnDwxuLaE" />\r\n</object>\r\n</p>', 'plastic_people.jpg', 'http://www.youtube.com/v/YBOnDwxuLaE', 'Zakázaní, undergroundoví, kdysi věznění legendární členové legendární  kapely The Plastic People of the Universe hrají od roku 1968. To vypadá  na pěkně vyčpělou bandu, řekli bychom. Stačí však navštívit jediný  koncert a poznáte, že opak je pravdou. K dokonalosti dovedené  elektronické zvuky podtržené psychedelickým zpěvem a množstvím různých  nástrojů ve spojení s několikahodinovým vystoupením těchto vážených pánů  a jedné dámy vás zanechá s otevřenými ústy.\r\n \r\n\r\n\r\n\r\n\r\n\r\n', 'The-Plastic-People-of-the-Universe', 1),
(7, '2010-05-02 16:23:09', '2010-05-04 14:08:01', 1, 8, 'Electrick mann', '<p>Valmezští miláčci Electrick mann jsou lahůdkou pro všechny, kteří to  mají radši neomaleně, až tvrdě. Tihle kluci si rozhodně neberou  servítky, ani nenasazují rukavice, a pokud ano, tak proto, aby s nimi  dělali něco, o čem byste mamce asi povídat nemohli. Rozhodně si však  nenechte ujít divokou show a nekonečně ujeté texty v bezprostředním  kontaktu, které nám Electrici servírují.</p>\r\n<p> </p>\r\n<p>\r\n<object width="425" height="350" data="http://www.youtube.com/v/3Wfol7ES_hY" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/3Wfol7ES_hY" />\r\n<param name="src" value="http://www.youtube.com/v/3Wfol7ES_hY" />\r\n</object>\r\n</p>', '116-electrick-mann.jpg', 'http://www.youtube.com/v/3Wfol7ES_hY', 'Valmezští miláčci Electrick mann jsou lahůdkou pro všechny, kteří to  mají radši neomaleně, až tvrdě. Tihle kluci si rozhodně neberou  servítky, ani nenasazují rukavice, a pokud ano, tak proto, aby s nimi  dělali něco, o čem byste mamce asi povídat nemohli. Rozhodně si však  nenechte ujít divokou show a nekonečně ujeté texty v bezprostředním  kontaktu, které nám Electrici servírují.\r\n \r\n\r\n\r\n\r\n\r\n\r\n', 'Electrick-mann', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

DROP TABLE IF EXISTS `vypecky_categories`;
CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL AUTO_INCREMENT,
  `module` varchar(20) DEFAULT NULL,
  `data_dir` varchar(100) DEFAULT NULL,
  `urlkey_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `label_cs` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `alt_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `urlkey_en` varchar(100) DEFAULT NULL,
  `label_en` varchar(50) DEFAULT NULL,
  `alt_en` varchar(200) DEFAULT NULL,
  `urlkey_de` varchar(100) DEFAULT NULL,
  `label_de` varchar(50) DEFAULT NULL,
  `alt_de` varchar(200) DEFAULT NULL,
  `keywords_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `description_cs` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `keywords_en` varchar(200) DEFAULT NULL,
  `description_en` varchar(500) DEFAULT NULL,
  `keywords_de` varchar(200) DEFAULT NULL,
  `description_de` varchar(500) DEFAULT NULL,
  `ser_params` varchar(1000) DEFAULT NULL,
  `params` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `priority` smallint(2) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'je-li kategorie aktivní',
  `individual_panels` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Jesltli jsou panely pro kategorii individuální',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'yearly',
  `sitemap_priority` float NOT NULL DEFAULT '0.1',
  `show_in_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Má li se položka zobrazit v menu',
  `show_when_login_only` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Jstli má bát položka zobrazena po přihlášení',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `default_right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  `feeds` tinyint(1) NOT NULL DEFAULT '0',
  `icon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_category`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `description_cs` (`description_cs`),
  FULLTEXT KEY `description_en` (`description_en`),
  FULLTEXT KEY `description_de` (`description_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=125 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `module`, `data_dir`, `urlkey_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `label_en`, `alt_en`, `urlkey_de`, `label_de`, `alt_de`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `ser_params`, `params`, `protected`, `priority`, `active`, `individual_panels`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`, `changed`, `default_right`, `feeds`, `icon`) VALUES
(100, 'categories', NULL, 'struktura/kategorie', 'kategorie', NULL, 'administration/categories', 'Categories', NULL, '', '', '', NULL, NULL, NULL, NULL, '', '', NULL, '', 1, 0, 1, 0, 'never', 0, 1, 1, '2010-03-21 11:51:54', '---', 0, NULL),
(101, 'login', 'ucet', 'ucet', 'účet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 1, '2010-03-21 11:44:07', 'r--', 0, NULL),
(103, 'panels', 'panely', 'struktura/panely', 'panely', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:52:28', '---', 0, NULL),
(102, 'empty', 'struktura', 'struktura', 'struktura', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:51:21', '---', 0, NULL),
(104, 'empty', 'uzivatele', 'uzivatele', 'uživatelé', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:53:13', '---', 0, NULL),
(105, 'users', 'uzivatele-a-skupiny', 'uzivatele/uzivatele-a-skupiny', 'uživatelé a skupiny', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:53:47', '---', 0, NULL),
(106, 'empty', 'nastaveni', 'nastaveni', 'nastavení', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:54:34', '---', 0, NULL),
(107, 'configuration', 'globalni-nastaveni-systemu', 'nastaveni/globalni-nastaveni-systemu', 'globální nastavení systému', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:55:27', '---', 0, NULL),
(108, 'empty', 'sprava', 'sprava', 'správa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:56:06', '---', 0, NULL),
(109, 'services', 'sprava-systemu', 'sprava/sprava-systemu', 'správa systému', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:57:04', '---', 0, NULL),
(110, 'empty', 'informace', 'informace', 'informace', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:57:32', '---', 0, NULL),
(111, 'phpinfo', 'php-info', 'informace/php-info', 'PHP info', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0, '2010-03-21 11:58:03', '---', 0, NULL),
(117, 'text', NULL, 'uvod', 'úvod', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, 'always', 0, 1, 0, '2010-05-03 16:11:35', 'r--', 0, NULL),
(118, 'articles', 'clanky', 'clanky', 'članky', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-05-02 16:29:01', 'r--', 1, NULL),
(119, 'actionswgal', NULL, 'akce', 'Akce', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-05-03 12:34:55', 'r--', 1, NULL),
(120, 'kzmainpage', NULL, 'main-page', 'main page', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-04-11 13:20:48', 'r--', 0, NULL),
(121, 'newsletter', NULL, 'novinky-emailem', 'novinky emailem', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'a:7:{s:15:"newmail_reg_rec";s:17:"normalni@dalsi.cz";s:26:"newmail_registered_subject";s:40:"Registroce k odběru novinek ze stránek";s:28:"newmail_registered_text_user";s:191:"%date% byla tato e-mailová adresa registrována k odběru novinek ze stránek %webname% (%weblink%).\r\nRegistarci lze zrušit na adrese %unregaddress%.\r\nRegistrace proběhla z IP adresy %ip%.";s:30:"newmail_registered_send_notice";s:1:"1";s:32:"newmail_registered_subject_admin";s:34:"Nová registroce k odběru novinek";s:29:"newmail_registered_text_admin";s:144:"%date% byla registrována nová e-mailová adresa "%email%" pro odběr novinek, ze stránek "%webname%"\r\n Registrace proběhla z IP adresy %ip%.";s:14:"newmail_admins";a:2:{i:0;s:1:"1";i:1;s:1:"3";}}', NULL, 0, 0, 1, 1, 'never', 0, 1, 0, '2010-05-04 12:42:11', 'r--', 0, NULL),
(124, 'bands', 'kapely', 'kapely', 'Kapely', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0, '2010-05-01 14:04:06', 'r--', 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_config`
--

DROP TABLE IF EXISTS `vypecky_config`;
CREATE TABLE IF NOT EXISTS `vypecky_config` (
  `id_config` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `value` text,
  `values` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('string','number','bool','list','listmulti','ser_object') NOT NULL DEFAULT 'string',
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

--
-- Vypisuji data pro tabulku `vypecky_config`
--

INSERT INTO `vypecky_config` (`id_config`, `key`, `label`, `value`, `values`, `protected`, `type`) VALUES
(1, 'DEFAULT_ID_GROUP', NULL, '2', NULL, 0, 'number'),
(2, 'DEFAULT_GROUP_NAME', NULL, 'guest', NULL, 0, 'string'),
(3, 'DEFAULT_USER_NAME', NULL, 'anonym', NULL, 0, 'string'),
(4, 'APP_LANGS', NULL, 'cs;en', 'cs;en;de;ru;sk', 0, 'listmulti'),
(5, 'DEFAULT_APP_LANG', NULL, 'cs', 'cs;en;de;ru;sk', 0, 'list'),
(6, 'IMAGES_DIR', NULL, 'images', NULL, 0, 'string'),
(7, 'IMAGES_LANGS_DIR', NULL, 'langs', NULL, 0, 'string'),
(8, 'DEBUG_LEVEL', NULL, '2', NULL, 0, 'number'),
(9, 'TEMPLATE_FACE', NULL, 'default', NULL, 0, 'string'),
(10, 'SITEMAP_PERIODE', NULL, 'weekly', NULL, 0, 'string'),
(11, 'SEARCH_RESULT_LENGHT', NULL, '300', NULL, 0, 'number'),
(12, 'SEARCH_HIGHLIGHT_TAG', NULL, 'strong', NULL, 0, 'string'),
(13, 'SESSION_NAME', NULL, 'vypecky_cookie', NULL, 0, 'string'),
(14, 'WEB_NAME', NULL, 'Vepřové Výpečky', NULL, 0, 'string'),
(61, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:6:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"117";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"118";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"119";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"120";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"121";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"124";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object'),
(58, 'USE_GLOBAL_ACCOUNTS_TB_PREFIXES', 'Prefixy tabulek pro které se má použít globální systém přihlašování', 'vypecky_', '', 0, 'string'),
(59, 'NAVIGATION_MENU_TABLE', 'Název tabulky s navigačním menu', 'vypecky_navigation_panel', NULL, 0, 'string'),
(60, 'SHARES_TABLE', 'Název tabulky s odkazy na sdílení (při global)', 'vypecky_shares', NULL, 0, 'string'),
(21, 'PAGE_TITLE_SEPARATOR', NULL, '|', NULL, 0, 'string'),
(16, 'NAVIGATION_SEPARATOR', NULL, '::', NULL, 0, 'string'),
(17, 'HEADLINE_SEPARATOR', NULL, ' - ', NULL, 0, 'string'),
(19, 'PANEL_TYPES', NULL, 'left;right;bottom', 'left;right;bottom;top', 0, 'listmulti'),
(18, 'USE_IMAGEMAGICK', NULL, 'false', NULL, 0, 'bool'),
(20, 'DATA_DIR', NULL, 'data', NULL, 0, 'string'),
(22, 'USE_GLOBAL_ACCOUNTS', 'Globální systém přihlašování', 'false', NULL, 0, 'bool'),
(23, 'GLOBAL_TABLES_PREFIX', 'Prefix globálních tabulek', 'global_', NULL, 0, 'string'),
(25, 'USE_SUBDOMAIN_HTACCESS_WORKAROUND', NULL, NULL, NULL, 0, 'string'),
(27, 'PDF_PAGE_FORMAT', 'Formát stránky pro pdf výstup', 'A4', NULL, 0, 'string'),
(28, 'PDF_PAGE_ORIENTATION', 'Natočení stránky pro pdf výstup (P=portrait, L=landscape)', 'P', 'P;L', 0, 'list'),
(29, 'PDF_CREATOR', 'Název pdf kreatoru', 'TCPDF', NULL, 0, 'string'),
(30, 'PDF_AUTHOR', 'Autor pdf', 'TCPDF', NULL, 0, 'string'),
(31, 'PDF_HEADER_LOGO', 'Název loga v hlavičce pdf', NULL, NULL, 0, 'string'),
(32, 'PDF_HEADER_LOGO_WIDTH', 'Šířka loga v hlavičce', NULL, NULL, 0, 'string'),
(33, 'PDF_UNIT', 'Jednotky použité u pdf (pt=point, mm=millimeter, cm=centimeter, in=inch)', 'mm', 'mm;pt;cm;in', 0, 'list'),
(34, 'PDF_MARGIN_HEADER', 'Odsazení hlavičky', '5', NULL, 0, 'string'),
(35, 'PDF_MARGIN_FOOTER', 'Odsazení zápatí', '10', NULL, 0, 'string'),
(36, 'PDF_MARGIN_TOP', 'Odsazení stránky z vrchu', '20', NULL, 0, 'string'),
(37, 'PDF_MARGIN_BOTTOM', 'Odsazení stránky od spodu', '25', NULL, 0, 'string'),
(38, 'PDF_MARGIN_LEFT', 'Odsazení z leva', '15', NULL, 0, 'string'),
(39, 'PDF_MARGIN_RIGHT', 'Odsazení z prava', '15', NULL, 0, 'string'),
(40, 'PDF_FONT_NAME_MAIN', 'Název hlavního fontu', 'arial', NULL, 0, 'string'),
(41, 'PDF_FONT_SIZE_MAIN', 'Velikost hlavního fontu', '12', NULL, 0, 'string'),
(42, 'PDF_FONT_NAME_DATA', 'Font pro data', 'arial', NULL, 0, 'string'),
(43, 'PDF_FONT_SIZE_DATA', 'Velikost fontu pro data', '6', NULL, 0, 'string'),
(44, 'PDF_FONT_MONOSPACED', 'Název pevného fontu', 'courier', NULL, 0, 'string'),
(45, 'PDF_IMAGE_SCALE_RATIO', 'Zvětšení obrázků ve výstupním pdf', '1', NULL, 0, 'string'),
(46, 'HEAD_MAGNIFICATION', 'zvětšovací poměr nadpisů', '1.1', NULL, 0, 'string'),
(51, 'WEB_DESCRIPTION', 'Popis stránek', 'Testovací stránky VVE enginu verze 6.0', NULL, 0, 'string'),
(50, 'FEED_NUM', 'Poček generovaných rss/atom kanálů', '10', NULL, 0, 'number'),
(52, 'WEB_MASTER_NAME', 'Jméno webmastera', 'Jakub Matas', NULL, 0, 'string'),
(53, 'WEB_MASTER_EMAIL', 'E-mail webmastera', 'jakubmatas@gmail.com', NULL, 0, 'string'),
(54, 'FEED_TTL', 'Počet minut kešování kanálu', '30', NULL, 0, 'number'),
(55, 'WEB_COPYRIGHT', 'Copyright poznámka k webu ({Y} - nahrazeno rokem)', 'Obsah toho webu je licencován podle ... Žádná s jeho částí nesmí být použita bez vědomí webmastera. Copyrigth {Y}', NULL, 0, 'string'),
(56, 'SEARCH_ARTICLE_REL_MULTIPLIER', 'Násobič pro relevanci nadpisu článku (1 - nekonečno)', '5', NULL, 0, 'number'),
(57, 'ADMIN_MENU_STRUCTURE', 'Administrační menu', 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:6:{i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"102";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";i:100;s:28:"\0Category_Structure\0idParent";s:3:"102";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"103";s:28:"\0Category_Structure\0idParent";s:3:"102";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"104";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"105";s:28:"\0Category_Structure\0idParent";s:3:"104";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"106";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"107";s:28:"\0Category_Structure\0idParent";s:3:"106";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"108";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"109";s:28:"\0Category_Structure\0idParent";s:3:"108";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"110";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:1:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:3:"111";s:28:"\0Category_Structure\0idParent";s:3:"110";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:6;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:3:"101";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object'),
(62, 'MAIN_PAGE_TITLE', 'Nadpis hlavní stránky', 'Vepřové Výpečky Titulní strana', NULL, 0, 'string');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_groups`
--

DROP TABLE IF EXISTS `vypecky_groups`;
CREATE TABLE IF NOT EXISTS `vypecky_groups` (
  `id_group` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID skupiny',
  `name` varchar(15) DEFAULT NULL COMMENT 'Nazev skupiny',
  `label` varchar(100) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1',
  `default_right` varchar(3) NOT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Vypisuji data pro tabulku `vypecky_groups`
--

INSERT INTO `vypecky_groups` (`id_group`, `name`, `label`, `used`, `default_right`) VALUES
(1, 'admin', 'Administrátor', 1, 'rwc'),
(2, 'guest', 'Host', 1, 'r--'),
(3, 'user', 'Uživatel', 1, 'r--'),
(4, 'poweruser', 'uživatel s většími právy', 1, 'rw-'),
(5, 'test', 'Testovací skupina', 1, 'r--'),
(6, 'instruktor', '', 1, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_modules_instaled`
--

DROP TABLE IF EXISTS `vypecky_modules_instaled`;
CREATE TABLE IF NOT EXISTS `vypecky_modules_instaled` (
  `id_module` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `version` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_module`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `vypecky_modules_instaled`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_newsletter_mails`
--

DROP TABLE IF EXISTS `vypecky_newsletter_mails`;
CREATE TABLE IF NOT EXISTS `vypecky_newsletter_mails` (
  `id_mail` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `group` varchar(10) DEFAULT NULL,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `vypecky_newsletter_mails`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_panels`
--

DROP TABLE IF EXISTS `vypecky_panels`;
CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL AUTO_INCREMENT,
  `id_cat` smallint(5) NOT NULL DEFAULT '0' COMMENT 'id kategorie panelu',
  `id_show_cat` smallint(5) unsigned DEFAULT '0' COMMENT 'id kategorie ve které se má daný panel zobrazit',
  `position` varchar(20) NOT NULL DEFAULT '' COMMENT 'Název boxu do kterého panel patří',
  `porder` smallint(5) NOT NULL DEFAULT '0' COMMENT 'Řazení panelu',
  `pparams` varchar(1000) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `background` varchar(100) DEFAULT NULL,
  `pname_cs` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `pname_en` varchar(100) DEFAULT NULL,
  `pname_de` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_panel`),
  KEY `id_cat` (`id_cat`),
  KEY `id_show_cat` (`id_show_cat`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `id_cat`, `id_show_cat`, `position`, `porder`, `pparams`, `icon`, `background`, `pname_cs`, `pname_en`, `pname_de`) VALUES
(1, 1, 0, 'left', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 121, 0, 'left', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 118, 117, 'left', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 124, 0, 'left', 0, 'a:1:{s:4:"type";s:8:"randclip";}', NULL, NULL, NULL, NULL, NULL),
(5, 124, 0, 'left', 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_images`
--

DROP TABLE IF EXISTS `vypecky_photogalery_images`;
CREATE TABLE IF NOT EXISTS `vypecky_photogalery_images` (
  `id_photo` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_article` smallint(5) unsigned DEFAULT NULL,
  `id_category` smallint(5) unsigned NOT NULL,
  `file` varchar(200) NOT NULL,
  `name_cs` varchar(300) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `desc_cs` varchar(1000) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_sk` varchar(300) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `desc_sk` varchar(1000) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `name_en` varchar(300) DEFAULT NULL,
  `desc_en` varchar(1000) DEFAULT NULL,
  `name_de` varchar(300) DEFAULT NULL,
  `desc_de` varchar(1000) DEFAULT NULL,
  `ord` smallint(5) unsigned NOT NULL DEFAULT '0',
  `edit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_photo`),
  KEY `id_category` (`id_category`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_images`
--

INSERT INTO `vypecky_photogalery_images` (`id_photo`, `id_article`, `id_category`, `file`, `name_cs`, `desc_cs`, `name_sk`, `desc_sk`, `name_en`, `desc_en`, `name_de`, `desc_de`, `ord`, `edit_time`) VALUES
(1, 1, 118, '0608hearsTower_lg.jpg', '0608hearsTower_lg.jpg', NULL, NULL, NULL, '0608hearsTower_lg.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:55:59'),
(2, 1, 118, '2632b6bc2b4b0530a96ceb236e5c72.jpg', '2632b6bc2b4b0530a96ceb236e5c72.jpg', NULL, NULL, NULL, '2632b6bc2b4b0530a96ceb236e5c72.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:55:59'),
(3, 1, 118, '11020Concorde_Tower.jpg', '11020Concorde_Tower.jpg', NULL, NULL, NULL, '11020Concorde_Tower.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:55:59'),
(4, 1, 118, 'City_tower.jpg', 'City_tower.jpg', NULL, NULL, NULL, 'City_tower.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:55:59'),
(5, 1, 118, 'devils-tower-02-500.jpg', 'devils-tower-02-500.jpg', NULL, NULL, NULL, 'devils-tower-02-500.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:55:59'),
(6, 1, 118, 'eiffel-tower-picture.jpg', 'eiffel-tower-picture.jpg', NULL, NULL, NULL, 'eiffel-tower-picture.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:55:59'),
(7, 1, 118, 'tower.jpg', 'tower.jpg', NULL, NULL, NULL, 'tower.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:56:00'),
(8, 1, 118, 'wave-tower-dubai-1.jpg', 'wave-tower-dubai-1.jpg', NULL, NULL, NULL, 'wave-tower-dubai-1.jpg', NULL, NULL, NULL, 0, '2010-04-12 18:56:00');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_rights`
--

DROP TABLE IF EXISTS `vypecky_rights`;
CREATE TABLE IF NOT EXISTS `vypecky_rights` (
  `id_right` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_category` smallint(6) NOT NULL,
  `id_group` smallint(6) NOT NULL,
  `right` enum('---','r--','-w-','--c','rw-','-wc','r-c','rwc') NOT NULL DEFAULT 'r--',
  PRIMARY KEY (`id_right`),
  KEY `id_category` (`id_category`,`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=139 ;

--
-- Vypisuji data pro tabulku `vypecky_rights`
--

INSERT INTO `vypecky_rights` (`id_right`, `id_category`, `id_group`, `right`) VALUES
(1, 100, 1, 'rwc'),
(2, 101, 1, 'rwc'),
(3, 101, 2, 'r--'),
(4, 101, 3, 'r--'),
(5, 101, 4, 'r--'),
(6, 100, 2, '---'),
(7, 100, 3, '---'),
(8, 100, 4, '---'),
(9, 102, 1, 'rwc'),
(10, 102, 2, '---'),
(11, 102, 3, '---'),
(12, 102, 4, '---'),
(13, 103, 1, 'rwc'),
(14, 103, 2, '---'),
(15, 103, 3, '---'),
(16, 103, 4, '---'),
(17, 104, 1, 'rwc'),
(18, 104, 2, '---'),
(19, 104, 3, '---'),
(20, 104, 4, '---'),
(21, 105, 1, 'rwc'),
(22, 105, 2, '---'),
(23, 105, 3, '---'),
(24, 105, 4, '---'),
(25, 106, 1, 'rwc'),
(26, 106, 2, '---'),
(27, 106, 3, '---'),
(28, 106, 4, '---'),
(29, 107, 1, 'rwc'),
(30, 107, 2, '---'),
(31, 107, 3, '---'),
(32, 107, 4, '---'),
(33, 108, 1, 'rwc'),
(34, 108, 2, '---'),
(35, 108, 3, '---'),
(36, 108, 4, '---'),
(37, 109, 1, 'rwc'),
(38, 109, 2, '---'),
(39, 109, 3, '---'),
(40, 109, 4, '---'),
(41, 110, 1, 'rwc'),
(42, 110, 2, '---'),
(43, 110, 3, '---'),
(44, 110, 4, '---'),
(45, 111, 1, 'rwc'),
(46, 111, 2, '---'),
(47, 111, 3, '---'),
(48, 111, 4, '---'),
(93, 117, 1, 'rwc'),
(94, 117, 2, 'r--'),
(95, 117, 3, 'r--'),
(96, 117, 4, 'r--'),
(108, 120, 4, 'r--'),
(107, 120, 3, 'r--'),
(106, 120, 2, 'r--'),
(105, 120, 1, 'rwc'),
(104, 119, 4, 'r--'),
(103, 119, 3, 'r--'),
(102, 119, 2, 'r--'),
(101, 119, 1, 'rwc'),
(97, 118, 1, 'rwc'),
(98, 118, 2, 'r--'),
(99, 118, 3, 'r--'),
(100, 118, 4, 'r--'),
(109, 121, 1, 'rwc'),
(110, 121, 2, 'r--'),
(111, 121, 3, 'r--'),
(112, 121, 4, 'r--'),
(130, 124, 6, 'r--'),
(129, 124, 5, 'r--'),
(128, 124, 4, 'r--'),
(127, 124, 3, 'r--'),
(126, 124, 2, 'r--'),
(125, 124, 1, 'rwc'),
(136, 117, 6, 'r--'),
(135, 117, 5, 'r--'),
(134, 119, 6, 'r--'),
(133, 119, 5, 'r--'),
(132, 118, 6, 'r--'),
(131, 118, 5, 'r--'),
(137, 121, 5, 'r--'),
(138, 121, 6, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_shares`
--

DROP TABLE IF EXISTS `vypecky_shares`;
CREATE TABLE IF NOT EXISTS `vypecky_shares` (
  `id_share` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(300) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id_share`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_shares`
--

INSERT INTO `vypecky_shares` (`id_share`, `link`, `icon`, `name`) VALUES
(1, 'http://www.linkuj.cz/?id=linkuj&amp;url={URL}&amp;title={TITLE}', 'http://linkuj.cz/img/linkuj_icon.gif', 'linkuj.cz'),
(2, 'http://www.jagg.cz/bookmarks.php?action=add&amp;address={URL}&amp;title={TITLE}', 'http://www.jagg.cz/icon.png', 'jagg.cz'),
(3, 'http://vybrali.sme.sk/submit.php?url={URL}', 'http://zena.sme.sk/storm/imgs/toolbar/doasdf_c.gif', 'vybrali.sme.sk'),
(4, 'http://www.google.com/bookmarks/mark?op=edit&amp;bkmk={URL}&amp;title={TITLE}', 'icons_logos/google.gif', 'Google'),
(5, 'http://www.facebook.com/share.php?u={URL}&amp;title={TITLE}', 'icons_logos/facebook.gif', 'Facebook'),
(6, 'http://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url={URL}&amp;title={TITLE}', 'http://static.delicious.com/img/delicious.gif', 'delicious.com'),
(7, 'http://digg.com/submit?url={URL}&amp;title={TITLE}&amp;topic={TITLE}', 'http://digg.com/img/badges/16x16-digg-guy.gif', 'digg.com'),
(8, 'http://www.diigo.com/post?url={URL}&amp;title={TITLE}', 'http://www.diigo.com/images/ii_blue.gif', 'diigo.com'),
(9, 'http://pridat.eu/zalozku/?url={URL}&amp;title={TITLE}', 'http://i.pridat.eu/wwwpridateu.gif', 'pridat.eu'),
(10, 'http://www.bookmarky.cz/a.php?cmd=add&amp;url={URL}&amp;title={TITLE}', 'http://www.bookmarky.cz/bookmarky16x16.gif', 'bookmarky.cz');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_texts`
--

DROP TABLE IF EXISTS `vypecky_texts`;
CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `subkey` varchar(30) NOT NULL DEFAULT 'nokey',
  `label_cs` varchar(200) DEFAULT NULL,
  `text_cs` mediumtext,
  `text_clear_cs` mediumtext,
  `text_panel_cs` varchar(1000) DEFAULT NULL,
  `changed` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label_en` varchar(200) DEFAULT NULL,
  `text_en` mediumtext,
  `text_clear_en` mediumtext,
  `text_panel_en` varchar(1000) DEFAULT NULL,
  `label_de` varchar(200) DEFAULT NULL,
  `text_de` mediumtext,
  `text_clear_de` mediumtext,
  `text_panel_de` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_clear_de` (`text_clear_de`),
  FULLTEXT KEY `text_clear_en` (`text_clear_en`),
  FULLTEXT KEY `text_clear_cs` (`text_clear_cs`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--

INSERT INTO `vypecky_texts` (`id_text`, `id_item`, `subkey`, `label_cs`, `text_cs`, `text_clear_cs`, `text_panel_cs`, `changed`, `label_en`, `text_en`, `text_clear_en`, `text_panel_en`, `label_de`, `text_de`, `text_clear_de`, `text_panel_de`) VALUES
(1, 117, 'main', 'jak''s to bee', '<p>jaks''s senf '' sdasd '' ''\\''<img src="data/images/batman.jpg" alt="" width="84" height="113" /></p>', 'jaks''s senf '' sdasd '' ''\\''', NULL, '2010-05-02 01:07:13', 'bvbvhhgf', '<p>hgfhgfhfg</p>', 'hgfhgfhfg', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_users`
--

DROP TABLE IF EXISTS `vypecky_users`;
CREATE TABLE IF NOT EXISTS `vypecky_users` (
  `id_user` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(100) DEFAULT NULL COMMENT 'Heslo',
  `id_group` smallint(3) unsigned DEFAULT '3',
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `note` varchar(500) DEFAULT NULL,
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `foto_file` varchar(30) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Vypisuji data pro tabulku `vypecky_users`
--

INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '35675e68f4b5af7b995d9205ad0fc43842f16450', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', '', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0);
