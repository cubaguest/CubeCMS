-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Neděle 03. ledna 2010, 15:58
-- Verze MySQL: 5.1.37
-- Verze PHP: 5.2.10-2ubuntu6.3

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
-- Struktura tabulky `global_groups`
--

DROP TABLE IF EXISTS `global_groups`;
CREATE TABLE IF NOT EXISTS `global_groups` (
  `id_group` smallint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID skupiny',
  `name` varchar(15) DEFAULT NULL COMMENT 'Nazev skupiny',
  `label` varchar(100) DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `global_groups`
--

INSERT INTO `global_groups` (`id_group`, `name`, `label`, `used`) VALUES
(1, 'admin', 'Administrátor', 1),
(2, 'guest', 'Host', 1),
(3, 'user', 'Uživatel', 1),
(4, 'poweruser', 'uživatel s většími právy', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `global_users`
--

DROP TABLE IF EXISTS `global_users`;
CREATE TABLE IF NOT EXISTS `global_users` (
  `id_user` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(40) DEFAULT NULL COMMENT 'Heslo',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Vypisuji data pro tabulku `global_users`
--

INSERT INTO `global_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', 'guest', 2, 'host', 'host', '', 'host systému', 0, NULL, 0);

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
  `text_cs` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `urlkey_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_en` varchar(50) DEFAULT NULL,
  `text_en` text,
  `urlkey_en` varchar(200) DEFAULT NULL,
  `name_de` varchar(50) DEFAULT NULL,
  `text_de` text,
  `urlkey_de` varchar(200) DEFAULT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `stop_date` int(11) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_action`),
  KEY `id_user` (`id_user`),
  KEY `urlkey_cs` (`urlkey_cs`),
  KEY `urlkey_en` (`urlkey_en`),
  KEY `urlkey_de` (`urlkey_de`),
  FULLTEXT KEY `label_cs` (`name_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`name_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `label_de` (`name_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

--
-- Vypisuji data pro tabulku `vypecky_actions`
--

INSERT INTO `vypecky_actions` (`id_action`, `id_category`, `id_user`, `name_cs`, `text_cs`, `urlkey_cs`, `name_en`, `text_en`, `urlkey_en`, `name_de`, `text_de`, `urlkey_de`, `edit_time`, `start_date`, `stop_date`, `image`, `public`) VALUES
(24, 77, 1, 'Dějiny umění', '<h3 class="title">Examples</h3>\r\n<p class="para">&nbsp;</p>\r\n<div class="example">\r\n<p><strong>Example #1&nbsp;<strong>mktime()</strong>&nbsp;example</strong></p>\r\n<div class="para example-contents">\r\n<p><strong>mktime()</strong>&nbsp;is useful for doing date arithmetic and validation, as it will automatically calculate the correct value for out-of-range input. For example, each of the following lines produces the string "Jan-01-1998".</p>\r\n</div>\r\n<div class="programlisting example-contents">\r\n<div class="phpcode"><code><span style="color: #000000;"><span style="color: #0000bb;">&lt;?php<br /></span><span style="color: #007700;">echo&nbsp;</span><span style="color: #0000bb;">date</span><span style="color: #007700;">(</span><span style="color: #dd0000;">"M-d-Y"</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">mktime</span><span style="color: #007700;">(</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">12</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">32</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1997</span><span style="color: #007700;">));<br />echo&nbsp;</span><span style="color: #0000bb;">date</span><span style="color: #007700;">(</span><span style="color: #dd0000;">"M-d-Y"</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">mktime</span><span style="color: #007700;">(</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">13</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1997</span><span style="color: #007700;">));<br />echo&nbsp;</span><span style="color: #0000bb;">date</span><span style="color: #007700;">(</span><span style="color: #dd0000;">"M-d-Y"</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">mktime</span><span style="color: #007700;">(</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1998</span><span style="color: #007700;">));<br />echo&nbsp;</span><span style="color: #0000bb;">date</span><span style="color: #007700;">(</span><span style="color: #dd0000;">"M-d-Y"</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">mktime</span><span style="color: #007700;">(</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">1</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">98</span><span style="color: #007700;">));<br /></span><span style="color: #0000bb;">?&gt;</span></span></code></div>\r\n</div>\r\n</div>\r\n<p>&nbsp;</p>\r\n<p class="para">&nbsp;</p>\r\n<div class="example">\r\n<p><strong>Example #2 Last day of next month</strong></p>\r\n<div class="para example-contents">\r\n<p>The last day of any given month can be expressed as the "0" day of the next month, not the -1 day. Both of the following examples will produce the string "The last day in Feb 2000 is: 29".</p>\r\n</div>\r\n<div class="programlisting example-contents">\r\n<div class="phpcode"><code><span style="color: #000000;"><span style="color: #0000bb;">&lt;?php<br />$lastday&nbsp;</span><span style="color: #007700;">=&nbsp;</span><span style="color: #0000bb;">mktime</span><span style="color: #007700;">(</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">3</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">2000</span><span style="color: #007700;">);<br />echo&nbsp;</span><span style="color: #0000bb;">strftime</span><span style="color: #007700;">(</span><span style="color: #dd0000;">"Last&nbsp;day&nbsp;in&nbsp;Feb&nbsp;2000&nbsp;is:&nbsp;%d"</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">$lastday</span><span style="color: #007700;">);<br /></span><span style="color: #0000bb;">$lastday&nbsp;</span><span style="color: #007700;">=&nbsp;</span><span style="color: #0000bb;">mktime</span><span style="color: #007700;">(</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">0</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">4</span><span style="color: #007700;">,&nbsp;-</span><span style="color: #0000bb;">31</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">2000</span><span style="color: #007700;">);<br />echo&nbsp;</span><span style="color: #0000bb;">strftime</span><span style="color: #007700;">(</span><span style="color: #dd0000;">"Last&nbsp;day&nbsp;in&nbsp;Feb&nbsp;2000&nbsp;is:&nbsp;%d"</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">$lastday</span><span style="color: #007700;">);<br /></span><span style="color: #0000bb;">?&gt;</span></span></code></div>\r\n</div>\r\n</div>\r\n<p>&nbsp;</p>', 'Dejiny-umeni', '', '', '', NULL, NULL, NULL, 1262433968, 1249941600, 1256770800, '', 1),
(28, 77, 1, 'akce 1', 'fe opwjf poiewfpoiweoiwf', 'akce-1', '', '', '', NULL, NULL, NULL, 1262434170, 1261350000, 1261609200, '', 0),
(29, 77, 1, 'akce 2 brzy', '<p style="font-size: 12px; margin-top: 0px; margin-right: 0px; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;">If you use a different element for the header, specify the header-option with an appropriate selector, eg. header: ''h3''. The content element must be always next to its header.</p>\r\n<p style="font-size: 12px; margin-top: 0px; margin-right: 0px; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;"><img style="border: 0px initial initial;" src="data/actions/okna.jpg" alt="" width="420" height="134" /><br /><br />If you have links inside the accordion content and use a-elements as headers, add a class to them and use that as the header, eg. header: ''a.header''.</p>\r\n<p style="font-size: 12px; margin-top: 0px; margin-right: 0px; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;"><br /><br />Use activate(Number) to change the active content programmatically.</p>\r\n<a name="NOTE:_If_you_want_multiple_sections_open_at_once.2C_don.27t_use_an_accordion"></a>\r\n<h4 style="margin-top: 0px; margin-right: 0px; margin-bottom: 0.5em; margin-left: 0px; font-weight: bold; border-bottom-style: dashed; border-bottom-color: #999999; font-size: 12px; color: #000000; padding: 0px; border: 0px initial initial;">NOTE: If you want multiple sections open at once, don''t use an accordion</h4>\r\n<p style="font-size: 12px; margin-top: 0px; margin-right: 0px; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;">An accordion doesn''t allow more than one content panel to be open at the same time, and it takes a lot of effort to do that. If you are looking for a widget that allows more than one content panel to be open, don''t use this. Usually it can be written with a few lines of jQuery instead, something like this:</p>\r\n<pre>jQuery(document).ready(function(){\r\n	$(''.accordion .head'').click(function() {\r\n		$(this).next().toggle();\r\n		return false;\r\n	}).next().hide();\r\n});</pre>\r\n<p style="font-size: 12px; margin-top: 0px; margin-right: 0px; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;">Or animated:</p>\r\n<pre>jQuery(document).ready(function(){\r\n	$(''.accordion .head'').click(function() {\r\n		$(this).next().toggle(''slow'');\r\n		return false;\r\n	}).next().hide();\r\n});</pre>', 'akce-2-brzy', '', '', '', NULL, NULL, NULL, 1262435371, 1261695600, 1263423600, '', 1),
(30, 77, 1, 'akce 3', '<p>Proin elit arcu, rutrum commodo, vehicula tempus, <a href="http://seznam.cz">commodo</a> a, risus. <strong>Curabitur nec arcu</strong>. <em>Donec sollicitudin</em> mi sit amet mauris. Nam <span style="text-decoration: underline;">elementum quam ullamcorper</span> ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />&nbsp;</p>', 'akce-3', '', '', '', NULL, NULL, NULL, 1262422063, 1262300400, 1264719600, 'flash_icon.png', 1),
(31, 77, 1, 'akce 4', '<p style="font-size: 11px; margin-top: 5px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. <br /><br />Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. <br /><br />Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing.&nbsp;</p>\r\n<hr />\r\n<p><span style="font-size: 11px;">Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</span></p>\r\n<hr />\r\n<p style="font-size: 11px; margin-top: 5px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;"><img style="float: left; margin: 10px; border: 1px solid black;" title="akce" src="data/actions/okna.jpg" alt="akce" width="420" height="134" />Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>\r\n<p style="font-size: 11px; margin-top: 5px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>\r\n<p style="font-size: 11px; margin-top: 5px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>\r\n<p style="font-size: 11px; margin-top: 5px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>\r\n<p style="font-size: 11px; margin-top: 5px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>\r\n<p style="font-size: 11px; margin-top: 5px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.<br /><br />&nbsp;</p>', 'akce-4', '', '', '', NULL, NULL, NULL, 1262427705, 1264806001, 1264806001, 'reports-256x256.png', 1),
(27, 77, 1, 'Svoboda na plátně', 'Some examples of&nbsp;<strong>date()</strong>&nbsp;formatting. Note that you should escape any other characters, as any which currently have a special meaning will produce undesirable results, and other characters may be assigned meaning in future PHP versions. When escaping, be sure to use single quotes to prevent characters like \\n from becoming newlines.<br /><br />Some examples of&nbsp;<strong>date()</strong>&nbsp;formatting. Note that you should escape any other characters, as any which currently have a special meaning will produce undesirable results, and other characters may be assigned meaning in future PHP versions. When escaping, be sure to use single quotes to prevent characters like \\n from becoming newlines.<br /><br />Some examples of&nbsp;<strong>date()</strong>&nbsp;formatting. Note that you should escape any other characters, as any which currently have a special meaning will produce undesirable results, and other characters may be assigned meaning in future PHP versions. When escaping, be sure to use single quotes to prevent characters like \\n from becoming newlines.<br /><br />Some examples of&nbsp;<strong>date()</strong>&nbsp;formatting. Note that you should escape any other characters, as any which currently have a special meaning will produce undesirable results, and other characters may be assigned meaning in future PHP versions. When escaping, be sure to use single quotes to prevent characters like \\n from becoming newlines.<br /><br />Some examples of&nbsp;<strong>date()</strong>&nbsp;formatting. Note that you should escape any other characters, as any which currently have a special meaning will produce undesirable results, and other characters may be assigned meaning in future PHP versions. When escaping, be sure to use single quotes to prevent characters like \\n from becoming newlines.<br /><br />Some examples of&nbsp;<strong>date()</strong>&nbsp;formatting. Note that you should escape any other characters, as any which currently have a special meaning will produce undesirable results, and other characters may be assigned meaning in future PHP versions. When escaping, be sure to use single quotes to prevent characters like \\n from becoming newlines.', 'Svoboda-na-platne', '', '', '', NULL, NULL, NULL, 1262426293, 1261004400, 1261695600, '', 1),
(35, 77, 1, 'akce 5', '<h3 class="title">Examples</h3>\r\n<p class="para">&nbsp;</p>\r\n<div class="example">\r\n<p><strong>Example #1&nbsp;<strong>strip_tags()</strong>&nbsp;example</strong></p>\r\n<div class="programlisting example-contents">\r\n<div class="phpcode"><code><span style="color: #000000;"><span style="color: #0000bb;">$text&nbsp;</span><span style="color: #007700;">=&nbsp;</span><span style="color: #dd0000;">''\r\n<p>Test&nbsp;paragraph.</p>\r\n<!--&nbsp;Comment&nbsp;-->&nbsp;Other&nbsp;text''</span><span style="color: #007700;">;<br />echo&nbsp;</span><span style="color: #0000bb;">strip_tags</span><span style="color: #007700;">(</span><span style="color: #0000bb;">$text</span><span style="color: #007700;">);<br />echo&nbsp;</span><span style="color: #dd0000;">"\\n"</span><span style="color: #007700;">;<br /><br /></span><span style="color: #ff8000;">//&nbsp;Allow&nbsp;\r\n<p>&nbsp;and&nbsp;<a><br /><span style="color: #007700;">echo&nbsp;</span><span style="color: #0000bb;">strip_tags</span><span style="color: #007700;">(</span><span style="color: #0000bb;">$text</span><span style="color: #007700;">,&nbsp;</span><span style="color: #dd0000;">''</span></a></p>\r\n<a></a></span></span></code>\r\n<p><code><a>''<span style="color: #007700;">);<br /></span><span style="color: #0000bb;">?&gt;</span></a></code><a></a></p>\r\n</div>\r\n<a> </a></div>\r\n<a>\r\n<div class="para example-contents">\r\n<p>The above example will output:</p>\r\n</div>\r\n</a>\r\n<div class="screen example-contents">\r\n<div class="cdata">\r\n<pre><a>Test paragraph. Other text\r\n<p>Test paragraph.</p> </a><a href="#fragment">Other text</a></pre>\r\n</div>\r\n</div>\r\n</div>', 'akce-5', '', '', '', NULL, NULL, NULL, 1262506814, 1266361200, 1267311600, '', 1),
(36, 77, 1, 'Dějiny uměnia', '<p style="font-size: 12px; margin-top: 0px; margin-right: 15em; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;">The jQuery UI Accordion plugin uses the jQuery UI CSS Framework to style its look and feel, including colors and background textures. We recommend using the ThemeRoller tool to create and download custom themes that are easy to build and maintain.</p>\r\n<p style="font-size: 12px; margin-top: 0px; margin-right: 15em; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;">If a deeper level of customization is needed, there are widget-specific classes referenced within the ui.accordion.css stylesheet that can be modified. These classes are highlighed in bold below.</p>\r\n<h3 style="margin-top: 1.5em; margin-right: 0px; margin-bottom: 0.5em; margin-left: 0px; font-weight: normal; font-size: 14px; color: #e6820e; padding: 0px;">Sample markup with jQuery UI CSS Framework classes</h3>\r\n&lt;div class="ui-helper-reset <strong>ui-accordion</strong>&nbsp;ui-widget"&gt;<br />&nbsp;&nbsp;&lt;h3 class="ui-corner-top ui-state-active <strong>ui-accordion-header</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="ui-icon-triangle-1-s ui-icon"/&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="#"&gt;Section 1&lt;/a&gt;<br />&nbsp;&nbsp;&lt;/h3&gt;<br />&nbsp;&nbsp;&lt;div class="ui-corner-bottom&nbsp;<strong>ui-accordion-content-active</strong> ui-widget-content <strong>ui-accordion-content</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;Section 1 content<br />&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&lt;h3 class="ui-corner-all ui-state-default <strong>ui-accordion-header</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="ui-icon-triangle-1-e ui-icon"/&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="#"&gt;Section 2&lt;/a&gt;<br />&nbsp;&nbsp;&lt;/h3&gt;<br />&nbsp;&nbsp;&lt;div class="ui-corner-bottom ui-widget-content <strong>ui-accordion-content</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;Section 2 content<br />&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&lt;h3 class="ui-corner-all ui-state-default <strong>ui-accordion-header</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="ui-icon-triangle-1-e ui-icon"/&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="#"&gt;Section 3&lt;/a&gt;<br />&nbsp;&nbsp;&lt;/h3&gt;<br />&nbsp;&nbsp;&lt;div class="ui-corner-bottom ui-widget-content <strong>ui-accordion-content</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;Section 3 content<br />&nbsp;&nbsp;&lt;/div&gt;<br />&lt;/div&gt;<br />\r\n<p class="theme-note" style="font-size: 12px; margin-top: 1.2em; margin-right: 15em; margin-bottom: 1.2em; margin-left: 0px; background-color: #f6f6f6; padding: 8px; border: 1px solid #eeeeee;"><strong>Note: This is a sample of markup generated by the accordion plugin, not markup you should use to create a accordion. The only markup needed for that is&nbsp;<br />&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&lt;h3&gt;&lt;a href="#"&gt;Section 1&lt;/a&gt;&lt;/h3&gt;<br />&nbsp;&nbsp;&nbsp;&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Section 1 content<br />&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&nbsp;&lt;h3&gt;&lt;a href="#"&gt;Section 2&lt;/a&gt;&lt;/h3&gt;<br />&nbsp;&nbsp;&nbsp;&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Section 2 content<br />&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&nbsp;&lt;h3&gt;&lt;a href="#"&gt;Section 3&lt;/a&gt;&lt;/h3&gt;<br />&nbsp;&nbsp;&nbsp;&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Section 3 content<br />&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br />&lt;/div&gt;.</strong></p>', 'Dejiny-umenia', NULL, NULL, NULL, NULL, NULL, NULL, 1262429656, 1225666800, 1229382000, NULL, 1),
(37, 77, 1, 'Naše třička', '<p style="font-size: 12px; margin-top: 0px; margin-right: 15em; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;">The jQuery UI Accordion plugin uses the jQuery UI CSS Framework to style its look and feel, including colors and background textures. We recommend using the ThemeRoller tool to create and download custom themes that are easy to build and maintain.</p>\r\n<p style="font-size: 12px; margin-top: 0px; margin-right: 15em; margin-bottom: 1.2em; margin-left: 0px; padding: 0px;">If a deeper level of customization is needed, there are widget-specific classes referenced within the ui.accordion.css stylesheet that can be modified. These classes are highlighed in bold below.</p>\r\n<h3 style="margin-top: 1.5em; margin-right: 0px; margin-bottom: 0.5em; margin-left: 0px; font-weight: normal; font-size: 14px; color: #e6820e; padding: 0px;">Sample markup with jQuery UI CSS Framework classes</h3>\r\n&lt;div class="ui-helper-reset <strong>ui-accordion</strong>&nbsp;ui-widget"&gt;<br />&nbsp;&nbsp;&lt;h3 class="ui-corner-top ui-state-active <strong>ui-accordion-header</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="ui-icon-triangle-1-s ui-icon"/&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="#"&gt;Section 1&lt;/a&gt;<br />&nbsp;&nbsp;&lt;/h3&gt;<br />&nbsp;&nbsp;&lt;div class="ui-corner-bottom&nbsp;<strong>ui-accordion-content-active</strong> ui-widget-content <strong>ui-accordion-content</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;Section 1 content<br />&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&lt;h3 class="ui-corner-all ui-state-default <strong>ui-accordion-header</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="ui-icon-triangle-1-e ui-icon"/&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="#"&gt;Section 2&lt;/a&gt;<br />&nbsp;&nbsp;&lt;/h3&gt;<br />&nbsp;&nbsp;&lt;div class="ui-corner-bottom ui-widget-content <strong>ui-accordion-content</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;Section 2 content<br />&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&lt;h3 class="ui-corner-all ui-state-default <strong>ui-accordion-header</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;span class="ui-icon-triangle-1-e ui-icon"/&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="#"&gt;Section 3&lt;/a&gt;<br />&nbsp;&nbsp;&lt;/h3&gt;<br />&nbsp;&nbsp;&lt;div class="ui-corner-bottom ui-widget-content <strong>ui-accordion-content</strong>&nbsp;ui-helper-reset"&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;Section 3 content<br />&nbsp;&nbsp;&lt;/div&gt;<br />&lt;/div&gt;<br />\r\n<p class="theme-note" style="font-size: 12px; margin-top: 1.2em; margin-right: 15em; margin-bottom: 1.2em; margin-left: 0px; background-color: #f6f6f6; padding: 8px; border: 1px solid #eeeeee;"><strong>Note: This is a sample of markup generated by the accordion plugin, not markup you should use to create a accordion. The only markup needed for that is&nbsp;<br />&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&lt;h3&gt;&lt;a href="#"&gt;Section 1&lt;/a&gt;&lt;/h3&gt;<br />&nbsp;&nbsp;&nbsp;&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Section 1 content<br />&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&nbsp;&lt;h3&gt;&lt;a href="#"&gt;Section 2&lt;/a&gt;&lt;/h3&gt;<br />&nbsp;&nbsp;&nbsp;&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Section 2 content<br />&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br />&nbsp;&nbsp;&nbsp;&lt;h3&gt;&lt;a href="#"&gt;Section 3&lt;/a&gt;&lt;/h3&gt;<br />&nbsp;&nbsp;&nbsp;&lt;div&gt;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Section 3 content<br />&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br />&lt;/div&gt;.</strong></p>', 'Nase-tricka', NULL, NULL, NULL, NULL, NULL, NULL, 1262429705, 1294786800, 1297638000, NULL, 1),
(38, 82, 1, 'Testovací akce s galerií', '<h3>getlastmod</h3>\r\n<p class="verinfo">(PHP 4, PHP 5)</p>\r\n<p class="refpurpose"><span class="refname">getlastmod</span>&nbsp;&mdash;&nbsp;<span class="dc-title">Gets time of last page modification<br /><br /></span></p>\r\n<h3 class="title">Return Values</h3>\r\n<p class="para">Returns the time of the last modification of the current page. The value returned is a Unix timestamp, suitable for feeding to<a class="function" href="function.date.html">date()</a>. Returns&nbsp;<strong><tt class="constant">FALSE</tt></strong>&nbsp;on error.</p>\r\n<h3 class="title">Examples</h3>\r\n<p class="para">&nbsp;</p>\r\n<div class="example">\r\n<p><strong>Example #1&nbsp;<strong>getlastmod()</strong>&nbsp;example</strong></p>\r\n<div class="programlisting example-contents">\r\n<div class="phpcode"><code><span style="color: #000000;"><span style="color: #ff8000;">//&nbsp;outputs&nbsp;e.g.&nbsp;''Last&nbsp;modified:&nbsp;March&nbsp;04&nbsp;1998&nbsp;20:43:59.''<br /></span><span style="color: #007700;">echo&nbsp;</span><span style="color: #dd0000;">"Last&nbsp;modified:&nbsp;"&nbsp;</span><span style="color: #007700;">.&nbsp;</span><span style="color: #0000bb;">date&nbsp;</span><span style="color: #007700;">(</span><span style="color: #dd0000;">"F&nbsp;d&nbsp;Y&nbsp;H:i:s."</span><span style="color: #007700;">,&nbsp;</span><span style="color: #0000bb;">getlastmod</span><span style="color: #007700;">());<br /></span><span style="color: #0000bb;">?&gt;</span></span></code></div>\r\n</div>\r\n</div>\r\n<p>&nbsp;</p>', 'Testovaci-akce-s-galerii', '', '', '', NULL, NULL, NULL, 1262449343, 1262300400, 1264892400, 'reports-256x256.png', 1),
(39, 82, 1, 'bez fotek', '&nbsp;fsd gf gs', 'bez-fotek', NULL, NULL, NULL, NULL, NULL, NULL, 1262448613, 1264114800, 1265324400, NULL, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_articles`
--

DROP TABLE IF EXISTS `vypecky_articles`;
CREATE TABLE IF NOT EXISTS `vypecky_articles` (
  `id_article` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_cat` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned DEFAULT '1',
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `is_user_last_edit` smallint(6) DEFAULT NULL,
  `viewed` smallint(6) NOT NULL DEFAULT '0',
  `name_cs` varchar(400) DEFAULT NULL,
  `text_cs` text,
  `urlkey_cs` varchar(400) DEFAULT NULL,
  `name_en` varchar(400) DEFAULT NULL,
  `text_en` text,
  `urlkey_en` varchar(400) DEFAULT NULL,
  `name_de` varchar(400) DEFAULT NULL,
  `text_de` text,
  `urlkey_de` varchar(400) DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_article`),
  KEY `id_item` (`id_cat`,`id_user`),
  FULLTEXT KEY `label_cs` (`name_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`name_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `lebal_de` (`name_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Vypisuji data pro tabulku `vypecky_articles`
--

INSERT INTO `vypecky_articles` (`id_article`, `id_cat`, `id_user`, `add_time`, `edit_time`, `is_user_last_edit`, `viewed`, `name_cs`, `text_cs`, `urlkey_cs`, `name_en`, `text_en`, `urlkey_en`, `name_de`, `text_de`, `urlkey_de`, `public`) VALUES
(11, 66, 1, 1261658853, 1261658853, 1, 29, 'Mills loses bribery case appeal', '<p class=\\"\\\\&quot;first\\\\&quot;\\">David Mills, the estranged husband of UK cabinet minister Tessa Jowell, has lost his appeal against a bribery conviction in Italy.</p>\r\n<p><strong>He was convicted of accepting &pound;400,000 from the Italian Prime Minister Silvio Berlusconi in 1997.</strong></p>\r\n<p>Mills had been sentenced in February to four-and-a-half years in prison for corruption.</p>\r\n<p>The Italian PM had been shielded from prosecution by a law he proposed but judges overturned it this month.</p>\r\n<!-- E SF -->\r\n<p>Mr Berlusconi has denied paying a bribe and has said he does not even know Mills.</p>\r\n<p>That defence is likely to be re-examined after Italy\\\\\\''s most senior court ruled that Mr Berlusconi\\\\\\''s protection from prosecution violated the constitution.</p>\r\n<p>Mills was one of Mr Berlusconi\\\\\\''s consultants on offshore tax havens. He was accused of accepting the money as payment for keeping quiet about offshore companies during two previous trials in 1997 and 1998.</p>\r\n<p>Much of the evidence against Mills stemmed from a letter he sent to a British accountant in 2004, in which he said the payment came from \\\\\\"Mr B\\\\\\".</p>\r\n<p>The trial verdict said there was no evidence that the money came directly from Mr Berlusconi.</p>\r\n<p>Mills initially confirmed having received money from Mr Berlusconi \\\\\\"in recognition\\\\\\" of the evidence he gave, but later said the money had come from an Italian shipping magnate.</p>\r\n<p>Speaking after the appeal decision, Mills\\\\\\'' lawyer, Federico Cecconi, said the case \\\\\\"does not end here\\\\\\".</p>\r\n<p>\\\\\\"We have strong elements in our favour [that] will bring a change in the sentence\\\\\\", he said.</p>', 'mills-loses-bribery-case-appeal', '', '', '', NULL, NULL, NULL, 1),
(16, 66, 1, 1261358854, 1262436562, 1, 11, 'US charges two over ''terror plot''', '<a class="pirobox" href="data/photogalerymed/medium/IMAG0003.JPG"><img style="float: left; margin-bottom: 10px; margin-right: 10px;" title="sasa" src="data/photogalerymed/medium/IMAG0003.JPG" alt="as" width="200" height="150" /></a>\r\n<p class="first"><strong>Two men in the US city of Chicago have been charged with planning attacks on foreign targets, the US justice department has said.</strong></p>\r\n<p>David Coleman Headley, 49, and Tahawwur Hussain Rana, 48, were both arrested earlier this month.</p>\r\n<p>Among their alleged targets was a Danish newspaper that printed a cartoon of the Prophet Mohammed in 2005, sparking angry protests from Muslims.</p>\r\n<p>Prosecutors say Mr Headley travelled to Denmark twice to plan an attack.</p>\r\n<!-- E SF -->\r\n<p>The justice department said it had uncovered "a serious plot against overseas targets".</p>\r\n<p>Mr Headley, who changed his name from Daood Gilani in 2006, was arrested on 3 October as he was about to travel to Pakistan.</p>\r\n<p>He has been charged with "conspiracy to commit terrorist acts involving murder and maiming" outside the US and also with conspiracy to provide material support for the attack.</p>\r\n<p>Prosecutors allege he visited the Copenhagen and Arhus offices of the Jyllands-Posten newspaper, which printed the cartoons, for surveillance purposes.</p>\r\n<p>The charge sheet alleges he also travelled to Pakistan to meet members of the Islamic militant group Lashkar-e-Taiba.</p>\r\n<p>Mr Rana, who officials say is a native of Pakistan and citizen of Canada, was arrested at his home on 18 October. He is alleged to have helped Mr Headley plan and finance the attacks.</p>\r\n<p>The men both live in the Chicago area.</p>', 'us-charges-two-over-terror-plot', '', '', '', NULL, NULL, NULL, 1),
(18, 66, 1, 1221658855, 2009, 1, 10, 'Star Wars: The Force Unleashed na PC slibuje pořádnou porci Hvězdných válek', 'emn&aacute; strana s&iacute;ly by se dala charakterizovat třemi slovy: hněv, nen&aacute;vist, strach. Přesně s takov&yacute;mi atributy se vyprav&iacute;te do dobrodružstv&iacute;, kter&eacute; v&aacute;m nab&iacute;dne akčn&iacute; titul <strong><a href=\\"http://bonusweb.idnes.cz/xbox360/recenze/star-wars-the-force-unleashed-sila-na-vsechny-zpusoby-pdh-/clanek.A080916_190204_bw-xbox360-recenze_mnd.idn\\">Star Wars: Force Unleashed</a></strong>, kde stanete pr&aacute;vě na straně těch \\"&scaron;patn&yacute;ch\\".\r\n<p>A že PC hr&aacute;či budou tyto pocity opravdu c&iacute;tit, si můžeme b&yacute;t prakticky jisti. Hněv kvůli tomu, že poč&iacute;tačov&aacute; verze <a class=\\"vvword\\" href=\\"http://us-action-spol-s-ro.takeit.cz/fotopapir-pelikan-photo-paper-premium-a4-857133?41787&amp;rtype=V&amp;rmain=57995&amp;ritem=857133&amp;rclanek=3641073&amp;rslovo=419531&amp;showdirect=1\\" target=\\"_blank\\">hry</a> nevy&scaron;la souběžně s těmi konzolov&yacute;mi minul&yacute; rok, nen&aacute;vist vůči vydavateli Activision Blizzard či v&yacute;voj&aacute;ři z LucasArts proto, že to s konverz&iacute; tak dlouho trv&aacute;. A strach z toho, že se varianta pro poč&iacute;tače př&iacute;li&scaron; nepovede.</p>\r\n<p style=\\"text-align: center;\\">\r\n<object id=\\"gtembed\\" width=\\"480\\" height=\\"392\\" data=\\"http://www.gametrailers.com/remote_wrap.php?mid=53017\\" type=\\"application/x-shockwave-flash\\">\r\n<param name=\\"data\\" value=\\"http://www.gametrailers.com/remote_wrap.php?mid=53017\\" />\r\n<param name=\\"allowScriptAccess\\" value=\\"sameDomain\\" />\r\n<param name=\\"allowFullScreen\\" value=\\"true\\" />\r\n<param name=\\"quality\\" value=\\"high\\" />\r\n<param name=\\"src\\" value=\\"http://www.gametrailers.com/remote_wrap.php?mid=53017\\" />\r\n<param name=\\"name\\" value=\\"gtembed\\" />\r\n<param name=\\"align\\" value=\\"middle\\" />\r\n<param name=\\"allowfullscreen\\" value=\\"true\\" />\r\n</object>\r\n</p>\r\n<p>\r\n<object width=\\"480\\" height=\\"392\\" data=\\"http://www.gametrailers.com/remote_wrap.php?mid=53017\\" type=\\"application/x-shockwave-flash\\">\r\n<param name=\\"name\\" value=\\"gtembed\\" />\r\n<param name=\\"src\\" value=\\"http://www.gametrailers.com/remote_wrap.php?mid=53017\\" />\r\n<param name=\\"allowfullscreen\\" value=\\"true\\" />\r\n<param name=\\"quality\\" value=\\"high\\" />\r\n</object>\r\n</p>\r\n<p>Zanedlouho se dozv&iacute;me, nakolik jsou tyto pocity opr&aacute;vněn&eacute;. V Evropě by PC verze měla<a onclick=\\"return !Win.open(this, \\''foto_fullscreen\\'');\\" href=\\"http://bonusweb.idnes.cz/pc/preview/star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek-1te-/fotka.MND25cf8b_screen7_large.jpg.idn\\" target=\\"bonusweb\\"><img class=\\"fr\\" src=\\"http://i.idnes.cz/08/092/sph/MND25cf8b_screen7_large.jpg\\" border=\\"0\\" alt=\\"Star Wars: The Force Unleashed Xbox360\\" width=\\"244\\" height=\\"183\\" align=\\"right\\" /></a> vyj&iacute;t až v prosinci, zat&iacute;mco Američan&eacute; si ji budou moct osahat už v listopadu. Každop&aacute;dně půjde o zvl&aacute;&scaron;tn&iacute; edici, kter&aacute; nese podtitul Ultimate Sith Edition.</p>\r\n<p>Co takov&eacute; označen&iacute; znamen&aacute;? Předev&scaron;&iacute;m skutečnost, že v balen&iacute; kromě origin&aacute;ln&iacute;ho obsahu naraz&iacute;te je&scaron;tě na trojici bonusov&yacute;ch mis&iacute;, za něž museli konzolist&eacute; platit coby za stažiteln&eacute; př&iacute;davky. V těchto př&iacute;davn&yacute;ch mis&iacute;ch přitom nejde o m&aacute;lo, autoři si v nich pohr&aacute;vaj&iacute; s touto ot&aacute;zkou: \\"Co kdyby Sithov&eacute; přemohli Jedie?\\".</p>\r\n<p>The Force Unleashed by se dalo označit za jak&yacute;si most&iacute;k mezi třet&iacute;m a čtvrt&yacute;m d&iacute;lem filmov&eacute; s&aacute;gy Hvězdn&yacute;ch v&aacute;lek. Nemus&iacute;te se přitom b&aacute;t, že byste se na tomto most&iacute;ku pot&yacute;kali s prachsprostou z&aacute;pletkou \\"vymydli v&scaron;echny, co ti přijdou do cesty\\".</p>\r\n<p><a onclick=\\"return !Win.open(this, \\''foto_fullscreen\\'');\\" href=\\"http://bonusweb.idnes.cz/pc/preview/star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek-1te-/fotka.MND25cf8a_screen6_large.jpg.idn\\" target=\\"bonusweb\\"><img class=\\"fl\\" src=\\"http://i.idnes.cz/08/092/sph/MND25cf8a_screen6_large.jpg\\" border=\\"0\\" alt=\\"Star Wars: The Force Unleashed Xbox360\\" width=\\"244\\" height=\\"183\\" align=\\"left\\" /></a>Naopak, hra disponuje poměrně siln&yacute;m př&iacute;během, jenž se <a class=\\"vvword\\" href=\\"http://archer-reality.takeit.cz/archer-reality-nabizi-prodej-pozemku-a-zahrad-2421998?145236&amp;rtype=V&amp;rmain=75190&amp;ritem=2421998&amp;rclanek=3641073&amp;rslovo=421071&amp;showdirect=1\\" target=\\"_blank\\">m&iacute;sty</a> může porovn&aacute;vat dokonce s t&iacute;m filmov&yacute;m. Přinejmen&scaron;&iacute;m ho sympaticky doplňuje. Pot&eacute;, co Imper&aacute;tor vydal \\"Rozkaz 66\\", kter&yacute;m vyhladil velkou č&aacute;st Jediů, nah&aacute;n&iacute; nyn&iacute; Darth Vader osobně jejich zbytky, kter&eacute; chce pochopitelně tak&eacute; zlikvidovat.</p>\r\n<p>Na planetě Kashyyyk tento arcipadouch nenajde pouze prchaj&iacute;c&iacute;ho Jedie, ale tak&eacute; jeho syna, kter&yacute; se ukazuje b&yacute;t nezvykle nadan&yacute; (co do zach&aacute;zen&iacute; se Silou). Vader vezme sirotka pod svou ochranu a uděl&aacute; z něj sv&eacute;ho padavana. M&aacute; s n&iacute;m zcela specifick&eacute; pl&aacute;ny. Chce ho totiž využ&iacute;t jako jeden z n&aacute;strojů pro svržen&iacute; Imper&aacute;tora a k vlastn&iacute; instalaci na nejvy&scaron;&scaron;&iacute; post.</p>\r\n<p>Ačkoliv v The Force Unelashed převezmete pr&aacute;vě roli Vaderova ž&aacute;ka, zahrajete si v prvn&iacute; mise př&iacute;mo za tohoto antihrdinu, typick&eacute;ho sv&yacute;m kost&yacute;mem. Střihnete si za něj bitvu na zm&iacute;něn&eacute;m Kashyyyku.</p>\r\n<p><a onclick=\\"return !Win.open(this, \\''foto_fullscreen\\'');\\" href=\\"http://bonusweb.idnes.cz/pc/preview/star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek-1te-/fotka.MND25cf89_screen5_large.jpg.idn\\" target=\\"bonusweb\\"><img class=\\"fr\\" src=\\"http://i.idnes.cz/08/092/sph/MND25cf89_screen5_large.jpg\\" border=\\"0\\" alt=\\"Star Wars: The Force Unleashed Xbox360\\" width=\\"244\\" height=\\"183\\" align=\\"right\\" /></a>T&iacute;m hra nejenže vypr&aacute;v&iacute; jak&yacute;si předpř&iacute;běh, kter&yacute; v&aacute;s rychle uvede do zaj&iacute;mav&eacute;ho děje, ale d&aacute; v&aacute;m tak&eacute; ochutnat z mocn&yacute;ch Sil, jež si rovněž postupem času osvoj&iacute;te pot&eacute;, co v dal&scaron;&iacute; misi začnete jako novic.</p>\r\n<p>Darth Vader sv&yacute;m světeln&yacute;m mečem hravě odr&aacute;ž&iacute; přil&eacute;t&aacute;vaj&iacute;c&iacute; střely proti invazi stav&iacute;c&iacute;ch se Wookiů. Meč je pochopitelně předev&scaron;&iacute;m mocnou zbran&iacute; v boji na bl&iacute;zko. Jednou dvěma ranami pokoř&iacute; Vader i ty <a class=\\"vvword\\" href=\\"http://s-property-sro-1.takeit.cz/nove-byty-v-praze-nad-bohemkou-2584740?279935&amp;rtype=V&amp;rmain=64256&amp;ritem=2584740&amp;rclanek=3641073&amp;rslovo=419724&amp;showdirect=1\\" target=\\"_blank\\">největ&scaron;&iacute;</a> z chlupat&yacute;ch dobr&aacute;ků.</p>\r\n<p>Pokud už arcipadouch pod va&scaron;&iacute;m veden&iacute;m utrp&iacute; nějak&yacute; ten &scaron;r&aacute;m na kr&aacute;se, dok&aacute;že se rychle vyl&eacute;čit ze zabit&yacute;ch nepř&aacute;tel. Prvn&iacute; mise nem&aacute; představovat ž&aacute;dnou v&yacute;zvu a funguje tak skutečně jako ochutn&aacute;vka. Ve stylu nože proch&aacute;zej&iacute;c&iacute;ho m&aacute;slem tak běž&iacute;te st&aacute;le vpřed a <a class=\\"vvword\\" href=\\"http://euro-bike-praha-prodej-servis.takeit.cz/trek-4300-wsd-prp-white-2008-633479?5071&amp;rtype=V&amp;rmain=0&amp;ritem=633479&amp;rclanek=3641073&amp;rslovo=466871&amp;showdirect=1\\" target=\\"_blank\\">kolem</a> v&aacute;s se rozs&eacute;v&aacute; ne&scaron;těst&iacute;.</p>\r\n<p><a onclick=\\"return !Win.open(this, \\''foto_fullscreen\\'');\\" href=\\"http://bonusweb.idnes.cz/pc/preview/star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek-1te-/fotka.MND25cf88_screen4_large.jpg.idn\\" target=\\"bonusweb\\"><img class=\\"fl\\" src=\\"http://i.idnes.cz/08/092/sph/MND25cf88_screen4_large.jpg\\" border=\\"0\\" alt=\\"Star Wars: The Force Unleashed Xbox360\\" width=\\"244\\" height=\\"183\\" align=\\"left\\" /></a>Co už ale podle preview verze zase tak hladce neběž&iacute;, to je ovl&aacute;d&aacute;n&iacute; pomoc&iacute; kl&aacute;vesnice a my&scaron;i. Pakliže s t&iacute;m do vyd&aacute;n&iacute; autoři je&scaron;tě něco neudělaj&iacute;, nezbude v recenzi než zkonstatovat, že s gamepadem je to o pozn&aacute;n&iacute; lep&scaron;&iacute; a nevyžaduj&iacute;c&iacute; takov&eacute; zaučen&iacute;.</p>\r\n<p>The Force Unleashed je totiž akc&iacute; z pohledu třet&iacute; osoby, kter&aacute; byla na gamepad zjevně designov&aacute;na. Pozn&aacute;te to zejm&eacute;na při použit&iacute; telekinetick&yacute;ch Sil, kter&yacute;mi Vader a později tak&eacute; jeho učeň disponuj&iacute;. Těmi se d&aacute; v prostřed&iacute; pohybovat rozličn&yacute;mi předměty. S gamepadem je cel&aacute; z&aacute;ležitost velmi intuitivn&iacute; a do hry dobře zabudovan&aacute;, zat&iacute;mco s kombem kl&aacute;vesnice-my&scaron; už to zdaleka tak slavn&eacute; nen&iacute;.</p>\r\n<p>Na Kashyyyku konečně dojde k boji mezi Vaderem a zm&iacute;něn&yacute;m Jedim. Jedn&aacute; se o prvn&iacute; velk&yacute; souboj,<a onclick=\\"return !Win.open(this, \\''foto_fullscreen\\'');\\" href=\\"http://bonusweb.idnes.cz/pc/preview/star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek-1te-/fotka.MND25cf7f_1213323303.jpg.idn\\" target=\\"bonusweb\\"><img class=\\"fr\\" src=\\"http://i.idnes.cz/08/092/sph/MND25cf7f_1213323303.jpg\\" border=\\"0\\" alt=\\"Star Wars: The Force Unleashed Xbox360\\" width=\\"244\\" height=\\"183\\" align=\\"right\\" /></a> kter&yacute; absolvujete. V průběhu hry je jich cel&aacute; řada a patř&iacute; k vrcholům hratelnosti. Je třeba v nich &scaron;ikovně využ&iacute;vat bojov&yacute;ch schopnost&iacute; mečem a doplňovat je Silou. Obvykle se v nich nevyhnete ani reakčn&iacute;m hř&iacute;čk&aacute;m, kdy je zapotřeb&iacute; jen mačkat př&iacute;slu&scaron;n&aacute; tlač&iacute;tka. Souboje pak &uacute;st&iacute; ve velkolep&aacute; fin&aacute;le.</p>\r\n<p>Po prvn&iacute; misi se přesunete do budoucnosti a chop&iacute;te se dal&scaron;&iacute;ch kroků učně, kter&yacute; už si vydobyl jm&eacute;no: Starkiller. Ten nem&aacute; zdaleka tolik schopnost&iacute; jako jeho velk&yacute; učitel, zato je ale obratněj&scaron;&iacute; a kromě toho neust&aacute;le sb&iacute;r&aacute; zku&scaron;enosti, jež pak proměňuje v nov&eacute; sithovsk&eacute; techniky, bojov&eacute; man&eacute;vry a obecn&eacute; charakterov&eacute; vlastnosti.</p>\r\n<p>Střeln&eacute; zbraně Starkiller k dispozici nem&aacute; a je tomu tak dobře. Aspoň si <a class=\\"vvword\\" href=\\"http://rudolf-hubac-nej-motochema.takeit.cz/plneni-do-spreju-5663495?2237&amp;rtype=V&amp;rmain=0&amp;ritem=5663495&amp;rclanek=3641073&amp;rslovo=474588&amp;showdirect=1\\" target=\\"_blank\\">plně</a> vychutn&aacute;te to, co děl&aacute; Sitha Sithem. D&aacute;te-li si z&aacute;ležet, budete časem &uacute;tok řetězit do neuvěřitelně efektn&iacute;ch komb. Alternativně stač&iacute; drtit &uacute;točn&eacute; tlač&iacute;tko, ale pak bojov&eacute; sekvence nevypadaj&iacute; tak cool.</p>\r\n<p><a onclick=\\"return !Win.open(this, \\''foto_fullscreen\\'');\\" href=\\"http://bonusweb.idnes.cz/pc/preview/star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek-1te-/fotka.OSK23f02e_1205_0005.jpg.idn\\" target=\\"bonusweb\\"><img class=\\"fl\\" src=\\"http://i.idnes.cz/08/063/sph/OSK23f02e_1205_0005.jpg\\" border=\\"0\\" alt=\\"Star Wars: The Force Unleashed\\" width=\\"244\\" height=\\"183\\" align=\\"left\\" /></a>Zato ale pravděpodobně investujete do S&iacute;ly, např&iacute;klad bleskov&yacute;ch v&yacute;bojů či zm&iacute;něn&eacute; telekineze, jej&iacute;ž využ&iacute;v&aacute;n&iacute; v&aacute;s nejsp&iacute;&scaron;e až do konce hry neomrz&iacute;.</p>\r\n<p>Ve hře se pod&iacute;v&aacute;te na celou řadu m&iacute;st, kter&aacute; jste ve <a class=\\"vvword\\" href=\\"http://bontonfilm.takeit.cz/film-verejni-nepratele-4316208?31830&amp;rtype=V&amp;rmain=94661&amp;ritem=4316208&amp;rclanek=3641073&amp;rslovo=422254&amp;showdirect=1\\" target=\\"_blank\\">filmech</a> neviděli, př&iacute;padně o nich jen sly&scaron;eli. Jednou budete bojovat na planetě pokryt&eacute; džungl&iacute; proti domorodcům a Rancorům, podruh&eacute; se zase pod&iacute;v&aacute;te na planetu slouž&iacute;c&iacute; jako &scaron;roti&scaron;tě. Tam se v&aacute;m postav&iacute; obskurn&iacute; roboti. Tak&eacute; dal&scaron;&iacute; prostřed&iacute; se nese ve variabiln&iacute;m a <a class=\\"vvword\\" href=\\"http://attractive-sro.takeit.cz/atraktivni-vyrocni-zpravy-2516874-2516874?278266&amp;rtype=V&amp;rmain=46704&amp;ritem=2516874&amp;rclanek=3641073&amp;rslovo=427207&amp;showdirect=1\\" target=\\"_blank\\">atraktivn&iacute;m</a> duchu.</p>\r\n<p>Kromě toho je Starkiller m&oacute;dně založen&yacute;m Sithem, a tak si nevystač&iacute; s jedn&iacute;m oblečkem, naopak je stř&iacute;d&aacute;. Po dohr&aacute;n&iacute; si hru budete moct střihnout i za dal&scaron;&iacute; <a class=\\"vvword\\" href=\\"http://body-factory.takeit.cz/fit-menu-novinka-na-ceskem-trhu-3799539-3799539?195685&amp;rtype=V&amp;rmain=67851&amp;ritem=3799539&amp;rclanek=3641073&amp;rslovo=424867&amp;showdirect=1\\" target=\\"_blank\\">postavy</a>, jako je např&iacute;klad Darth Maul nebo Mace Windu.</p>\r\n<p>Nečekejte přitom, že v&scaron;e půjde podle Vaderova pl&aacute;nu. Ostatně ud&aacute;losti čtvrt&eacute;ho filmu ukazuj&iacute;, že<a onclick=\\"return !Win.open(this, \\''foto_fullscreen\\'');\\" href=\\"http://bonusweb.idnes.cz/pc/preview/star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek-1te-/fotka.MND25cf87_screen2_large.jpg.idn\\" target=\\"bonusweb\\"><img class=\\"fr\\" src=\\"http://i.idnes.cz/08/092/sph/MND25cf87_screen2_large.jpg\\" border=\\"0\\" alt=\\"Star Wars: The Force Unleashed Xbox360\\" width=\\"244\\" height=\\"183\\" align=\\"right\\" /></a> se tak docela nevyvedl. Proto v&aacute;s ve hře ček&aacute; nejedno <a class=\\"vvword\\" href=\\"http://24print-sro.takeit.cz/novinka-pro-letosni-vanoce-originalni-darek-4688747?321698&amp;rtype=V&amp;rmain=126594&amp;ritem=4688747&amp;rclanek=3641073&amp;rslovo=437048&amp;showdirect=1\\" target=\\"_blank\\">překvapen&iacute;</a> či dějov&yacute; zvrat. Prozrad&iacute;me jen tolik, že si na konci budete moct zvolit mezi temn&yacute;m a světl&yacute;m z&aacute;věrem, byť to nen&iacute; zdaleka tak očividn&aacute; volba, jak se může zd&aacute;t.</p>\r\n<p>Trojice bonusov&yacute;ch epizod navazuje pr&aacute;vě na temn&yacute; konec. Starkiller je v nich už dobře vycvičen&yacute;m sithsk&yacute;m zabij&aacute;kem a cestuje po třech m&iacute;stech zn&aacute;m&yacute;ch z filmů. Prvn&iacute;m je chr&aacute;m Jediů, jenž je přev&aacute;lcov&aacute;n klonov&yacute;mi bojovn&iacute;ky, d&aacute;le to je ledov&aacute; planeta Hoth a domov Luka Skywalkera, pou&scaron;tn&iacute; Tatooine.</p>\r\n<p>Bonusov&eacute; mise nab&iacute;zej&iacute; zaj&iacute;mav&yacute; alternativn&iacute; <a class=\\"vvword\\" href=\\"http://datalite-spol-s-ro.takeit.cz/vyvoj-sw-na-miru-3729387-3729387?18529&amp;rtype=V&amp;rmain=65783&amp;ritem=3729387&amp;rclanek=3641073&amp;rslovo=421623&amp;showdirect=1\\" target=\\"_blank\\">v&yacute;voj</a>, kter&yacute; si pohr&aacute;v&aacute; s ot&aacute;zkou \\"coby kdyby?\\" a konfrontuje s n&iacute; i zn&aacute;m&eacute; hrdiny s&aacute;gy. Zb&yacute;v&aacute; jen doufat, že se je&scaron;tě povede vyladit ovl&aacute;d&aacute;n&iacute; na kl&aacute;vesnici a my&scaron;i. Pak se můžeme tě&scaron;it na podařenou a atmosf&eacute;rickou akci.</p>\r\n<p style=\\"text-align: center;\\">&nbsp;</p>', 'star-wars-the-force-unleashed-na-pc-slibuje-poradnou-porci-hvezdnych-valek', '', '', '', NULL, NULL, NULL, 1),
(19, 66, 1, 1261658856, 0, 1, 38, 'UN st"aff" ''killed', '<p>&nbsp;</p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n" lang="cs" class="tinymce ardicle_text_class"&gt;" lang="cs" class="tinymce ardicle_text_class"&gt;', 'un-staff-killed-in-kabul-attack', '', '<p>&nbsp;</p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n" lang="cs" class="tinymce ardicle_text_class"&gt;" lang="cs" class="tinymce ardicle_text_class"&gt;', '', '', '<p>&nbsp;</p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n" lang="cs" class="tinymce ardicle_text_class"&gt;" lang="cs" class="tinymce ardicle_text_class"&gt;', '', 1);
INSERT INTO `vypecky_articles` (`id_article`, `id_cat`, `id_user`, `add_time`, `edit_time`, `is_user_last_edit`, `viewed`, `name_cs`, `text_cs`, `urlkey_cs`, `name_en`, `text_en`, `urlkey_en`, `name_de`, `text_de`, `urlkey_de`, `public`) VALUES
(20, 66, 1, 1111658857, 0, 1, 16, 'Six foreign UN employees have been killed', '<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>', 'six-foreign-un-employees-have-been-killed', '', '<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>', '', '', '<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>\r\n<p class="first"><strong>Six foreign UN employees have been killed and nine wounded in an attack in Kabul, the deadliest on the UN in Afghanistan since the Taliban''s fall.</strong></p>\r\n<p>Three militants attacked a guesthouse used by the UN. They were later shot dead. Two Afghan security personnel and a civilian also died.</p>\r\n<p>The Taliban said they carried out the attack, which comes 10 days before the second round of presidential elections.</p>\r\n<p>Later, rockets were fired at the city''s five-star Serena Hotel.</p>\r\n<!-- E SF -->\r\n<p>One or two rockets were said to have landed in the grounds of the hotel, which is used by diplomats and other foreigners.</p>\r\n<p>No-one has been reported injured there, but about 100 people inside at the time were taken to secure rooms as smoke filled the lobby.</p>\r\n<p><strong>Taliban warning</strong></p>\r\n<p>The attack at the guesthouse happened just before 0600 (0130 GMT), Afghan police and the UN said.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div class="sih">ANALYSIS</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/media/images/46622000/jpg/_46622348_000865616-1.jpg" border="0" alt="Andrew North" hspace="0" vspace="0" width="66" height="66" align="right" /></div>\r\n<div class="mva"><strong>Andrew North, BBC News, Kabul</strong></div>\r\n<div class="mva">\r\n<p>These attacks on two high-profile targets have spread a lot of fear.</p>\r\n<p>Security preparations were getting under way to protect the vote on 7 November. There are thousands of Afghan troops in and around Kabul but determined militants are still able to get through.</p>\r\n<p>The UN guesthouses have to conform to specific rules on security but we don''t know how these militants were able to get through the security.</p>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>The private Bekhtar Guesthouse is used by the UN and other international organisations.</p>\r\n<p>The US embassy confirmed one of the dead was an American.</p>\r\n<p>Taliban spokesman Zabiullah Mujahid claimed responsibility for the attack in a telephone call to the Associated Press.</p>\r\n<p>He said three Taliban militants with suicide vests, grenades and machine guns had carried out the assault.</p>\r\n<p>UN spokesman Aleem Siddique told the BBC there was gunfire and an explosion outside the building as UN employees tried to flee. Streets were cordoned off by police.</p>\r\n<p>The three gunmen were shot dead and the incident ended at about 0830 local time.</p>\r\n<p>It was not immediately known how many people were inside the guesthouse at the time. The building was gutted by fire.</p>\r\n<!-- S IBOX --> \r\n<table style="width: 231px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td width="5"><img src="http://newsimg.bbc.co.uk/shared/img/o.gif" border="0" alt="" hspace="0" vspace="0" width="5" height="1" /></td>\r\n<td class="sibtbg">\r\n<div>\r\n<div class="mva"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/start_quote_rb.gif" border="0" alt="" width="24" height="13" /> <strong>Much of the gunfire was random - security guards shooting at nothing</strong> <img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/end_quote_rb.gif" border="0" alt="" vspace="0" width="23" height="13" align="right" /></div>\r\n</div>\r\n<div class="mva">\r\n<div>Kabul eyewitness</div>\r\n</div>\r\n<div class="o"><img src="http://newsimg.bbc.co.uk/nol/shared/img/v3/inline_dashed_line.gif" border="0" alt="" hspace="0" vspace="2" width="226" height="1" /></div>\r\n<div class="miiib"><!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/middle_east/8329188.stm">In pictures: Kabul UN attacks</a></div>\r\n<!-- E ILIN --> <!-- S ILIN -->\r\n<div class="arr"><a href="http://news.bbc.co.uk/2/hi/south_asia/8329129.stm">US to pay Taliban to switch sides</a></div>\r\n<!-- E ILIN --></div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IBOX -->\r\n<p>One foreign Kabul resident told the BBC the attack involved multiple grenades and automatic weapons.</p>\r\n<p>"I saw people on the roof [security guards] and one woman climbed down from a second storey balcony after she had stood screaming and shouting for about five minutes for someone to come help her. Much of the gunfire was random - security guards shooting at nothing," he said.</p>\r\n<p>The US embassy condemned the attack, saying it was "shocked and saddened".</p>\r\n<p>"Attacking civilian workers will not lessen our determination to support the Afghan people and their election process," the embassy said.</p>', '', 1);
INSERT INTO `vypecky_articles` (`id_article`, `id_cat`, `id_user`, `add_time`, `edit_time`, `is_user_last_edit`, `viewed`, `name_cs`, `text_cs`, `urlkey_cs`, `name_en`, `text_en`, `urlkey_en`, `name_de`, `text_de`, `urlkey_de`, `public`) VALUES
(22, 66, 1, 1261658858, 0, 1, 42, 'Images on a Subdomain (?)', '<p>I can&rsquo;t remember where, but a while ago I read something about using subdomains to serve up a sites resources as a way to potentially speed up loading. The theory was that the protocol that browsers use to communicate with servers only allows some limited number of things to be download concurrently from a single domain (like 2 or 4?). But a site fairly commonly has dozens of resources. So if you were to create a subdomain (e.g. images.css-tricks.com) and use that to serve up images, that would be treated as a different domain and you would double the number of concurrent downloads possible.</p>\r\n<p>&nbsp;</p>\r\n<p>In trying to research it, I haven&rsquo;t been able to turn up a lot of quality information. Some forum threads are condemning it saying that multiple DNS lookups would then be required slowing things down more than speeding things up. Others going to far as to say that Google may penalize for this (which seems relatively absurd).</p>\r\n<p>I&rsquo;m always trying to improve the efficiency and speed of my sites where I can. This past weekend I was trying to improve my CSS Sprites use on this site. This was <a href=\\"http://images.css-tricks.com/theme-4/css-tricks.png\\">the result</a>. It is fairly trivial to create a subdomain, so I tossed it up on there. This is just one image so it doubt it will make any big difference, but I&rsquo;ll definitely look into moving all of the images from the theme onto a subdomain if there is any conclusive evidence this is a smart idea.</p>\r\n<p>Anyone have any good information on this?</p>', 'images-on-a-subdomain-', '', '', '', NULL, NULL, NULL, 0),
(23, 66, 1, 1261658859, 1261660010, 1, 25, 'A jQuery plugin for rendering rich, fast-performing photo galleries', '<p>Galleriffic is ''a jQuery plugin that provides a rich, post-back free experience optimized to handle high volumes of photos while conserving bandwidth. I am not so great at spelling, and it was much later that I realized that the more appropriate spellings would be Galle<em>rif</em>ic or Galle<em>rrif</em>ic, but is too late now for a name change, so Galleriffic remains.</p>\r\n<h3>Features</h3>\r\n<ul>\r\n<li>Smart image preloading <strong>after</strong> the page is loaded</li>\r\n<li>Thumbnail navigation (with pagination)</li>\r\n<li>jQuery.history plugin integration to support bookmark-friendly URLs per-image</li>\r\n<li>Slideshow (with optional auto-updating url bookmarks)</li>\r\n<li>Keyboard navigation</li>\r\n<li>Events that allow for adding your own custom transition effects</li>\r\n<li>API for controlling the gallery with custom controls</li>\r\n<li>Support for image captions</li>\r\n<li>Flexible configuration</li>\r\n<li>Graceful degradation when javascript is not available</li>\r\n<li>Support for multiple galleries per page</li>\r\n</ul>\r\n<h3>Examples</h3>\r\n<ul>\r\n<li><a href="\\">Minimal implementation</a></li>\r\n<li><a href="\\">Thumbnail rollover effects and slideshow crossfades</a></li>\r\n<li><a href="\\">Integration with history plugin</a></li>\r\n<li><a href="\\">Insert and remove images after initialization</a></li>\r\n<li><a href="\\">Alternate layout using custom previous/next page controls</a></li>\r\n</ul>', 'A-jQuery-plugin-for-rendering-rich-fast-performing-photo-galleries', '', '', '', NULL, NULL, NULL, 0),
(27, 81, 1, 1261658860, 0, 1, 30, 'Škoda 105 RS vs. Bagr', '<p>I spent a while replacing all my ereg() calls to preg_match(), since ereg() is now deprecated and will not be supported as of v 6.0.&nbsp;<br /><br />Just a warning regarding the conversion, the two functions behave very similarly, but not exactly alike. Obviously, you will need to delimit your pattern with \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ characters.&nbsp;<br /><br />The difference that stumped me was that preg_replace overwrites the $matches array regardless if a match was found. If no match was found, $matches is simply empty.&nbsp;</p>', 'Skoda-105-RS-vs-Bagr', '', '', '', NULL, NULL, NULL, 1),
(29, 81, 1, 1261658861, 0, 1, 10, 'Povodně', 'If your regular expression does not match with long input text when you think it should, you might have hit the PCRE backtrack default limit of 100000. See&nbsp;<a rel=\\"\\\\&quot;\\\\\\\\&quot;nofollow\\\\\\\\&quot;\\\\&quot;\\" href=\\"\\\\\\" target=\\"\\\\&quot;\\\\\\\\&quot;_blank\\\\\\\\&quot;\\\\&quot;\\">http://php.net/pcre.backtrack-limit.</a>', 'Povodne', '', '', '', NULL, NULL, NULL, 1),
(31, 81, 1, 1261658862, 1261660121, 1, 9, 'škoda bum', 'ef jkwehfkjew fkr ghfker ghkrehs gkersgh ekjrsglhj ghkfhdg kerhjg ehrj e hr ferk', 'skoda-bum', '', '', '', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

DROP TABLE IF EXISTS `vypecky_categories`;
CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL AUTO_INCREMENT,
  `module` varchar(20) DEFAULT NULL,
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
  `params` varchar(200) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `priority` smallint(2) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'je-li kategorie aktivní',
  `individual_panels` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Jesltli jsou panely pro kategorii individuální',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'yearly',
  `sitemap_priority` float NOT NULL DEFAULT '0.1',
  `show_in_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Má li se položka zobrazit v menu',
  `show_when_login_only` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Jstli má bát položka zobrazena po přihlášení',
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `module`, `urlkey_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `label_en`, `alt_en`, `urlkey_de`, `label_de`, `alt_de`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `params`, `protected`, `priority`, `active`, `individual_panels`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`) VALUES
(1, 'text', 'textove/textove-pole', 'Textové pole', '', 'about-as', 'About as', '', '', '', '', '', '', '', '', '', '', '', 0, -10, 1, 0, 'monthly', 0.8, 1, 0),
(14, 'categories', 'administrace/kategorie', 'Kategorie', '', 'administration/categories', 'Categories', '', '', '', '', '', '', '', '', '', '', '', 1, 0, 1, 0, 'never', 0.1, 0, 0),
(50, 'text', 'administrace', 'Administrace', '', 'administration', 'Administration', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, 0, 'yearly', 0, 1, 0),
(51, 'configuration', 'administrace/konf-volby', 'Konfigurační volby', '', 'administration/config-options', 'Configuration options', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, 0, 'never', 0, 1, 0),
(52, 'login', 'ucet', 'Účet', '', 'account', 'Account', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, 0, 'always', 0, 1, 0),
(60, 'panels', 'administrace/panely', 'Panely', 'Nastavení panelů', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, 0, 'never', 0, 0, 0),
(65, 'photogalery', 'galerie/jednoducha-galerie', 'Jednoduchá galerie', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, '', 0, 0, 1, 0, 'always', 0, 0, 0),
(66, 'articles', 'textove/blogy', 'Blogy', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, 'scroll=10', 0, 0, 1, 0, 'always', 0, 0, 0),
(67, 'navigationmenu', 'specialni/navigacni-panel', 'Navigační panel', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, '', 0, 0, 1, 0, 'monthly', 0, 1, 0),
(68, 'users', 'administrace/uzivatele', 'Uživatelé', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, '', 0, 0, 1, 0, 'never', 0, 1, 1),
(69, 'phpinfo', 'administrace/php-info', 'PHP info', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0),
(70, 'text', 'testy', 'Testy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0),
(74, 'cinemaprogram', 'specialni/program-kina', 'Program kina', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, '', 0, 0, 1, 0, 'always', 0, 1, 0),
(75, 'pokus', 'testovaci-kategorie', 'Testovací kategorie', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, '', 0, 0, 1, 0, 'always', 0, 1, 0),
(76, 'text', 'testy/test-08', 'test 08', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, '', 0, 0, 1, 0, 'always', 0, 1, 0),
(77, 'actions', 'specialni/akce', 'Akce', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, '', 0, 0, 1, 0, 'always', 0, 1, 0),
(78, 'text', 'galerie', 'Galerie', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0),
(79, 'text', 'textove', 'Textové', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 1, 0),
(80, 'text', 'specialni', 'Speciální', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0),
(81, 'photogalerymed', 'galerie/stredni-galerie', 'Střední galerie', '', '', '', '', NULL, NULL, NULL, '', '', '', '', NULL, NULL, 'imagesinlist=4', 0, 0, 1, 0, 'always', 0, 1, 0),
(82, 'actionswgal', 'specialni/akce-s-galerii', 'Akce s galerií', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'always', 0, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_cinemaprogram_movies`
--

DROP TABLE IF EXISTS `vypecky_cinemaprogram_movies`;
CREATE TABLE IF NOT EXISTS `vypecky_cinemaprogram_movies` (
  `id_movie` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` smallint(5) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_orig` varchar(50) DEFAULT NULL,
  `label` varchar(1000) DEFAULT NULL,
  `length` int(10) unsigned NOT NULL DEFAULT '0',
  `version` varchar(20) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `imdbid` int(10) unsigned zerofill DEFAULT NULL,
  `csfdid` int(10) unsigned zerofill DEFAULT NULL,
  `price` smallint(5) unsigned zerofill NOT NULL DEFAULT '00000',
  `accessibility` smallint(5) unsigned NOT NULL DEFAULT '0',
  `film_club` tinyint(1) NOT NULL DEFAULT '0',
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Vypisuji data pro tabulku `vypecky_cinemaprogram_movies`
--

INSERT INTO `vypecky_cinemaprogram_movies` (`id_movie`, `id_category`, `name`, `name_orig`, `label`, `length`, `version`, `image`, `imdbid`, `csfdid`, `price`, `accessibility`, `film_club`, `changed`) VALUES
(10, 74, 'Film1', '', 'popis', 123, 'czech', NULL, 0000000000, 0000000000, 00022, 0, 0, '2010-01-03 12:07:35'),
(11, 74, 'Film 2', '', 'popisek', 120, 'czech', NULL, 0000000000, 0000000000, 00100, 0, 0, '2010-01-03 12:08:38'),
(12, 74, 'Film 3', '', 'popisek', 120, 'czech', NULL, 0000000000, 0000000000, 00100, 0, 0, '2010-01-03 12:09:11'),
(13, 74, 'Film 4', '', 'popisek', 120, 'czech', NULL, 0000000000, 0000000000, 00022, 0, 0, '2010-01-03 12:10:51');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_cinemaprogram_time`
--

DROP TABLE IF EXISTS `vypecky_cinemaprogram_time`;
CREATE TABLE IF NOT EXISTS `vypecky_cinemaprogram_time` (
  `id_time` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_movie` smallint(5) unsigned NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `time` time NOT NULL,
  PRIMARY KEY (`id_time`),
  KEY `id_movie` (`id_movie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_cinemaprogram_time`
--

INSERT INTO `vypecky_cinemaprogram_time` (`id_time`, `id_movie`, `date_from`, `date_to`, `time`) VALUES
(3, 10, '2010-01-01', '2010-01-08', '16:00:00'),
(4, 10, '2010-01-09', '2010-01-09', '18:00:00'),
(5, 11, '2010-01-09', '2010-01-16', '16:00:00'),
(6, 11, '2010-01-17', '2010-01-17', '18:00:00'),
(7, 11, '2010-01-18', '2010-01-18', '20:00:00'),
(8, 12, '2010-01-19', '2010-01-27', '16:00:00'),
(9, 13, '2010-01-26', '2010-01-27', '18:00:00'),
(10, 13, '2010-01-28', '2010-01-29', '16:00:00');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

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
(15, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":6:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:7:{i:7;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"78";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"65";s:28:"\0Category_Structure\0idParent";s:2:"78";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"81";s:28:"\0Category_Structure\0idParent";s:2:"78";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:8;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"79";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"66";s:28:"\0Category_Structure\0idParent";s:2:"79";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":6:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";i:1;s:28:"\0Category_Structure\0idParent";s:2:"79";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}s:35:"\0Category_Structure\0currentChildKey";N;}}}i:9;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"80";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:4:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"77";s:28:"\0Category_Structure\0idParent";s:2:"80";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"67";s:28:"\0Category_Structure\0idParent";s:2:"80";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"74";s:28:"\0Category_Structure\0idParent";s:2:"80";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"82";s:28:"\0Category_Structure\0idParent";s:2:"80";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:10;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"70";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:2:{i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"75";s:28:"\0Category_Structure\0idParent";s:2:"70";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"76";s:28:"\0Category_Structure\0idParent";s:2:"70";s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}i:13;O:18:"Category_Structure":6:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";i:50;s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:5:{i:0;O:18:"Category_Structure":6:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";i:14;s:28:"\0Category_Structure\0idParent";i:50;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}s:35:"\0Category_Structure\0currentChildKey";N;}i:2;O:18:"Category_Structure":6:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"60";s:28:"\0Category_Structure\0idParent";i:50;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}s:35:"\0Category_Structure\0currentChildKey";N;}i:3;O:18:"Category_Structure":6:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"51";s:28:"\0Category_Structure\0idParent";i:50;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}s:35:"\0Category_Structure\0currentChildKey";N;}i:4;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"68";s:28:"\0Category_Structure\0idParent";i:50;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:5;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:2;s:22:"\0Category_Structure\0id";s:2:"69";s:28:"\0Category_Structure\0idParent";i:50;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}s:35:"\0Category_Structure\0currentChildKey";N;}i:14;O:18:"Category_Structure":6:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"52";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}s:35:"\0Category_Structure\0currentChildKey";N;}i:15;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:2:"71";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}s:35:"\0Category_Structure\0currentChildKey";N;}', NULL, 1, 'ser_object'),
(21, 'PAGE_TITLE_SEPARATOR', NULL, '|', NULL, 0, 'string'),
(16, 'NAVIGATION_SEPARATOR', NULL, '::', NULL, 0, 'string'),
(17, 'HEADLINE_SEPARATOR', NULL, ' - ', NULL, 0, 'string'),
(19, 'PANEL_TYPES', NULL, 'left;right;bottom', 'left;right;bottom;top', 0, 'listmulti'),
(18, 'USE_IMAGEMAGICK', NULL, 'false', NULL, 0, 'bool'),
(20, 'DATA_DIR', NULL, 'data', NULL, 0, 'string'),
(22, 'USE_GLOBAL_ACCOUNTS', 'Globální systém přihlašování', 'false', NULL, 0, 'bool'),
(23, 'GLOBAL_TABLES_PREFIX', 'Prefix globálních tabulek', 'global_', NULL, 0, 'string'),
(25, 'USE_SUBDOMAIN_HTACCESS_WORKAROUND', NULL, NULL, NULL, 0, 'string');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_contacts`
--

DROP TABLE IF EXISTS `vypecky_contacts`;
CREATE TABLE IF NOT EXISTS `vypecky_contacts` (
  `id_contact` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_city` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) DEFAULT NULL,
  `text_cs` text,
  `name_en` varchar(300) DEFAULT NULL,
  `text_en` text,
  `name_de` varchar(300) DEFAULT NULL,
  `text_de` text,
  `file` varchar(200) DEFAULT NULL,
  `changed_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_contact`),
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `name_cs` (`name_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `name_en` (`name_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `name_de` (`name_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_contacts`
--

INSERT INTO `vypecky_contacts` (`id_contact`, `id_item`, `id_city`, `name_cs`, `text_cs`, `name_en`, `text_en`, `name_de`, `text_de`, `file`, `changed_time`) VALUES
(2, 8, 203, 'Prodejna a sklad, centrála společnosti', '<p>Telefon: 571 611 801, 571 618 970<br />Mobil:739 619 605<br /> Fax: 571 611 801</p>\r\n<p> </p>\r\n<p>E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a></p>\r\n<p> </p>\r\n<p>Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem.jpg', 1239209498),
(4, 8, 21, 'Prodejna', '<p>Telefon: 571 611 801, 571 618 970,Mobil:739 619 605<br /> Fax: 571 611 801<br /> E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a><br /> Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem2.jpg', 1239207032),
(5, 8, 100, 'Prodejna a sklad', '<p>Telefon: 571 611 801, 571 618 970,Mobil:739 619 605<br /> Fax: 571 611 801<br /> E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a><br /> Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem3.jpg', 1239207080);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_contacts_areas`
--

DROP TABLE IF EXISTS `vypecky_contacts_areas`;
CREATE TABLE IF NOT EXISTS `vypecky_contacts_areas` (
  `id_area` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id_area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65536 ;

--
-- Vypisuji data pro tabulku `vypecky_contacts_areas`
--

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

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_contacts_cities`
--

DROP TABLE IF EXISTS `vypecky_contacts_cities`;
CREATE TABLE IF NOT EXISTS `vypecky_contacts_cities` (
  `id_city` int(11) NOT NULL AUTO_INCREMENT,
  `id_area` int(11) NOT NULL,
  `city_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id_city`),
  KEY `id_area` (`id_area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65536 ;

--
-- Vypisuji data pro tabulku `vypecky_contacts_cities`
--

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

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_eplugin_sendmails`
--

DROP TABLE IF EXISTS `vypecky_eplugin_sendmails`;
CREATE TABLE IF NOT EXISTS `vypecky_eplugin_sendmails` (
  `id_mail` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned DEFAULT NULL,
  `mail` varchar(200) NOT NULL,
  PRIMARY KEY (`id_mail`),
  KEY `id_item` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `vypecky_eplugin_sendmails`
--

INSERT INTO `vypecky_eplugin_sendmails` (`id_mail`, `id_item`, `id_article`, `mail`) VALUES
(1, 9, 0, 'jakubmatas@gmail.com'),
(2, 9, 0, 'cuba@vypecky.info');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_eplugin_sendmailstexts`
--

DROP TABLE IF EXISTS `vypecky_eplugin_sendmailstexts`;
CREATE TABLE IF NOT EXISTS `vypecky_eplugin_sendmailstexts` (
  `id_text` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned DEFAULT NULL,
  `subject` varchar(500) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id_text`),
  KEY `id_item` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `vypecky_eplugin_sendmailstexts`
--

INSERT INTO `vypecky_eplugin_sendmailstexts` (`id_text`, `id_item`, `id_article`, `subject`, `text`) VALUES
(1, 9, NULL, 'Předmět emalu', 'Text mailu %pokus%.\r\n\r\npočet znaků je: %pocet%/%sudy[sudý/lichý]%\r\n\r\npočet znaků je: %pocet%/%sudy[sudý/lichý]%');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_galeries`
--

DROP TABLE IF EXISTS `vypecky_galeries`;
CREATE TABLE IF NOT EXISTS `vypecky_galeries` (
  `id_galery` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_category` smallint(5) unsigned NOT NULL,
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) NOT NULL,
  `name_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `text_cs` varchar(1000) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_en` varchar(200) DEFAULT NULL,
  `text_en` varchar(1000) DEFAULT NULL,
  `name_de` varchar(200) DEFAULT NULL,
  `text_de` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_galery`),
  KEY `id_category` (`id_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `vypecky_galeries`
--


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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_groups`
--

INSERT INTO `vypecky_groups` (`id_group`, `name`, `label`, `used`, `default_right`) VALUES
(1, 'admin', 'Administrátor', 1, 'rwc'),
(2, 'guest', 'Host', 1, 'r--'),
(3, 'user', 'Uživatel', 1, 'r--'),
(4, 'poweruser', 'uživatel s většími právy', 1, 'rw-'),
(5, 'test', 'Testovací skupina', 1, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_items`
--

DROP TABLE IF EXISTS `vypecky_items`;
CREATE TABLE IF NOT EXISTS `vypecky_items` (
  `id_item` smallint(6) NOT NULL AUTO_INCREMENT,
  `label_cs` varchar(100) DEFAULT NULL,
  `alt_cs` varchar(500) DEFAULT NULL,
  `label_en` varchar(100) DEFAULT NULL,
  `alt_en` varchar(500) DEFAULT NULL,
  `label_de` varchar(100) DEFAULT NULL,
  `alt_de` varchar(500) DEFAULT NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') DEFAULT 'rwc',
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') DEFAULT 'rw-',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') DEFAULT 'r--',
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') DEFAULT 'rwc',
  `params` varchar(500) DEFAULT NULL COMMENT 'parametry pro daný modul itemu - jsouv popsány v docs',
  `priority` smallint(6) NOT NULL DEFAULT '0',
  `id_category` smallint(6) NOT NULL,
  `id_module` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id_item`),
  KEY `id_category` (`id_category`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `vypecky_items`
--

INSERT INTO `vypecky_items` (`id_item`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `group_admin`, `group_user`, `group_guest`, `group_poweruser`, `params`, `priority`, `id_category`, `id_module`) VALUES
(1, 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=true;theme=advanced', 0, 1, 1),
(2, 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=false;theme=simple', 0, 2, 1),
(3, 'Novinky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10;scrollpanel=2', 0, 3, 2),
(4, 'Login', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 4, 4),
(5, 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 5, 1),
(6, 'Reference', NULL, 'References', NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'width=800;height=600;smallwidth=200;smallheight=150', 0, 6, 19),
(7, 'Články', NULL, 'Articles', NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=2', 0, 7, 20),
(8, 'Kontakty', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 8, 21),
(9, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 9, 7),
(10, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10', 0, 10, 22),
(11, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10;scrollpanel=1', 0, 11, 24);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_modules`
--

DROP TABLE IF EXISTS `vypecky_modules`;
CREATE TABLE IF NOT EXISTS `vypecky_modules` (
  `id_module` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `mparams` varchar(100) DEFAULT NULL,
  `datadir` varchar(100) DEFAULT NULL,
  `dbtable1` varchar(50) DEFAULT NULL,
  `dbtable2` varchar(50) DEFAULT NULL,
  `dbtable3` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Vypisuji data pro tabulku `vypecky_modules`
--

INSERT INTO `vypecky_modules` (`id_module`, `name`, `mparams`, `datadir`, `dbtable1`, `dbtable2`, `dbtable3`) VALUES
(1, 'text', NULL, NULL, 'texts', NULL, NULL),
(2, 'news', NULL, NULL, 'news', NULL, NULL),
(3, 'categories', NULL, NULL, NULL, NULL, NULL),
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
(22, 'products', NULL, 'products', 'products', 'products_documents', 'products_photos'),
(24, 'actions', NULL, 'actions', 'actions', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_navigation_panel`
--

DROP TABLE IF EXISTS `vypecky_navigation_panel`;
CREATE TABLE IF NOT EXISTS `vypecky_navigation_panel` (
  `id_link` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `type` enum('subdomain','project') NOT NULL DEFAULT 'subdomain',
  `indexing` tinyint(1) NOT NULL DEFAULT '1',
  `params` varchar(200) DEFAULT NULL,
  `ord` smallint(3) NOT NULL DEFAULT '100',
  PRIMARY KEY (`id_link`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Vypisuji data pro tabulku `vypecky_navigation_panel`
--

INSERT INTO `vypecky_navigation_panel` (`id_link`, `url`, `name`, `icon`, `type`, `indexing`, `params`, `ord`) VALUES
(12, 'http://localhost/vve6/ucet/', 'Účet', NULL, 'project', 1, '', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_news`
--

DROP TABLE IF EXISTS `vypecky_news`;
CREATE TABLE IF NOT EXISTS `vypecky_news` (
  `id_new` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `label_cs` varchar(50) NOT NULL,
  `text_cs` varchar(500) NOT NULL,
  `label_en` varchar(50) DEFAULT NULL,
  `text_en` varchar(500) DEFAULT NULL,
  `label_de` varchar(50) DEFAULT NULL,
  `text_de` varchar(500) DEFAULT NULL,
  `time` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_new`),
  KEY `id_user` (`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Vypisuji data pro tabulku `vypecky_news`
--

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
(32, 3, 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220169, 0),
(34, 3, 1, 'Zcela nová novinka pro publikum', '&amp;lt;p&amp;gt;&amp;lt;strong&amp;gt;ROME, Italy (CNN) &amp;lt;/strong&amp;gt; -- American student Amanda Knox was on the stand Saturday for a second day, this time facing questions from the public prosecutor in her trial on charges of murdering her housemate about two years ago.&amp;lt;/p&amp;gt;', NULL, NULL, NULL, NULL, 1244917651, 0),
(35, 3, 1, 'Zcela nová novinka pro publikum', '&amp;lt;p&amp;gt;&amp;lt;strong&amp;gt;ROME, Italy (CNN) &amp;lt;/strong&amp;gt; -- American student Amanda Knox was on the stand Saturday for a second day, this time facing questions from the public prosecutor in her trial on charges of murdering her housemate about two years ago.&amp;lt;/p&amp;gt;', NULL, NULL, NULL, NULL, 1244918038, 0),
(36, 3, 1, 'Zcela nová novinka pro publikum', '<p><strong>ROME, Italy (CNN)</strong> -- American student Amanda Knox was on the stand Saturday for a second day, this time facing questions from the public prosecutor in her trial on charges of murdering her housemate about two years ago.</p>', NULL, NULL, NULL, NULL, 1244918107, 0);

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
  PRIMARY KEY (`id_panel`),
  KEY `id_cat` (`id_cat`),
  KEY `id_show_cat` (`id_show_cat`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `id_cat`, `id_show_cat`, `position`, `porder`) VALUES
(5, 61, 0, 'left', 0),
(6, 61, 0, 'bottom', 0),
(7, 1, 0, 'bottom', 0),
(8, 12, 0, 'right', 0),
(9, 61, 0, 'right', 0),
(11, 66, 0, 'left', 0),
(12, 1, 0, 'left', 0),
(13, 52, 0, 'right', 0),
(14, 77, 0, 'right', 0),
(15, 82, 0, 'right', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_galeries`
--

DROP TABLE IF EXISTS `vypecky_photogalery_galeries`;
CREATE TABLE IF NOT EXISTS `vypecky_photogalery_galeries` (
  `id_galery` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_item` smallint(6) NOT NULL,
  `label_cs` varchar(200) DEFAULT NULL,
  `text_cs` varchar(1000) DEFAULT NULL,
  `label_en` varchar(200) DEFAULT NULL,
  `text_en` varchar(1000) DEFAULT NULL,
  `label_de` varchar(200) DEFAULT NULL,
  `text_de` varchar(1000) DEFAULT NULL,
  `time_add` int(11) DEFAULT NULL,
  `time_edit` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_galery`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_galeries`
--


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
  `name_en` varchar(300) DEFAULT NULL,
  `desc_en` varchar(1000) DEFAULT NULL,
  `name_de` varchar(300) DEFAULT NULL,
  `desc_de` varchar(1000) DEFAULT NULL,
  `ord` smallint(5) unsigned NOT NULL DEFAULT '0',
  `edit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_photo`),
  KEY `id_category` (`id_category`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_images`
--

INSERT INTO `vypecky_photogalery_images` (`id_photo`, `id_article`, `id_category`, `file`, `name_cs`, `desc_cs`, `name_en`, `desc_en`, `name_de`, `desc_de`, `ord`, `edit_time`) VALUES
(1, 65, 65, 'Obraz007.jpg', 'Obraz007.jpg', NULL, 'Obraz007.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(2, 65, 65, 'Obraz008.jpg', 'Obraz008.jpg', NULL, 'Obraz008.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(3, 65, 65, 'Obraz009.jpg', 'Obraz009.jpg', NULL, 'Obraz009.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(4, 65, 65, 'Obraz010.jpg', 'Obraz010.jpg', NULL, 'Obraz010.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(5, 65, 65, 'Obraz011.jpg', 'Obraz011.jpg', NULL, 'Obraz011.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(6, 65, 65, 'Obraz012.jpg', 'Obraz012.jpg', NULL, 'Obraz012.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(7, 65, 65, 'Obraz013.jpg', 'Obraz013.jpg', NULL, 'Obraz013.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(8, 65, 65, 'Obraz014.jpg', 'Obraz014.jpg', NULL, 'Obraz014.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(9, 28, 81, 'Obraz016.jpg', 'Obraz016.jpg', NULL, 'Obraz016.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(10, 28, 81, 'Obraz017.jpg', 'Obraz017.jpg', NULL, 'Obraz017.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(11, 28, 81, 'Obraz018.jpg', 'Obraz018.jpg', NULL, 'Obraz018.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(32, 27, 81, 'IMAG0005.JPG', 'IMAG0005.JPG', NULL, 'IMAG0005.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(31, 27, 81, 'IMAG0004.JPG', 'IMAG0004.JPG', NULL, 'IMAG0004.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(30, 27, 81, 'IMAG0003.JPG', 'IMAG0003.JPG', NULL, 'IMAG0003.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(38, 29, 81, 'Obraz008.jpg', 'Obraz008.jpg', NULL, 'Obraz008.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(37, 29, 81, 'Obraz007.jpg', 'Obraz007.jpg', NULL, 'Obraz007.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(29, 27, 81, 'IMAG0002.JPG', 'IMAG0002.JPG', NULL, 'IMAG0002.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(33, 27, 81, 'IMAG0006.JPG', 'IMAG0006.JPG', NULL, 'IMAG0006.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(34, 27, 81, 'IMAG0008.JPG', 'IMAG0008.JPG', NULL, 'IMAG0008.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(35, 27, 81, 'IMAG0009.JPG', 'IMAG0009.JPG', NULL, 'IMAG0009.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(36, 27, 81, 'IMAG0010.JPG', 'IMAG0010.JPG', NULL, 'IMAG0010.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(39, 29, 81, 'Obraz009.jpg', 'Obraz009.jpg', NULL, 'Obraz009.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(40, 29, 81, 'Obraz010.jpg', 'Obraz010.jpg', NULL, 'Obraz010.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(41, 29, 81, 'Obraz011.jpg', 'Obraz011.jpg', NULL, 'Obraz011.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(42, 29, 81, 'Obraz012.jpg', 'Obraz012.jpg', NULL, 'Obraz012.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(43, 29, 81, 'Obraz013.jpg', 'Obraz013.jpg', NULL, 'Obraz013.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(44, 29, 81, 'Obraz014.jpg', 'Obraz014.jpg', NULL, 'Obraz014.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(45, 29, 81, 'Obraz015.jpg', 'Obraz015.jpg', NULL, 'Obraz015.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(46, 29, 81, 'Obraz016.jpg', 'Obraz016.jpg', NULL, 'Obraz016.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(47, 29, 81, 'Obraz017.jpg', 'Obraz017.jpg', NULL, 'Obraz017.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(48, 29, 81, 'Obraz018.jpg', 'Obraz018.jpg', NULL, 'Obraz018.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(49, 29, 81, 'Obraz019.jpg', 'Obraz019.jpg', NULL, 'Obraz019.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(50, 29, 81, 'Obraz020.jpg', 'Obraz020.jpg', NULL, 'Obraz020.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(51, 29, 81, 'Obraz021.jpg', 'Obraz021.jpg', NULL, 'Obraz021.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(52, 29, 81, 'P1020070.JPG', 'P1020070.JPG', NULL, 'P1020070.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(53, 29, 81, 'P1020071.JPG', 'P1020071.JPG', NULL, 'P1020071.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(54, 29, 81, 'P1020072.JPG', 'P1020072.JPG', NULL, 'P1020072.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(55, 29, 81, 'P1020073.JPG', 'P1020073.JPG', NULL, 'P1020073.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(56, 29, 81, 'P1020074.JPG', 'P1020074.JPG', NULL, 'P1020074.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(57, 29, 81, 'P1020075.JPG', 'P1020075.JPG', NULL, 'P1020075.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(58, 29, 81, 'P1020076.JPG', 'P1020076.JPG', NULL, 'P1020076.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(59, 29, 81, 'P1020077.JPG', 'P1020077.JPG', NULL, 'P1020077.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(60, 29, 81, 'P1020078.JPG', 'P1020078.JPG', NULL, 'P1020078.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(61, 29, 81, 'P1020079.JPG', 'P1020079.JPG', NULL, 'P1020079.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(62, 29, 81, 'P1020080.JPG', 'P1020080.JPG', NULL, 'P1020080.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(63, 29, 81, 'P1020081.JPG', 'P1020081.JPG', NULL, 'P1020081.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(64, 29, 81, 'P1020082.JPG', 'P1020082.JPG', NULL, 'P1020082.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(65, 29, 81, 'P1020083.JPG', 'P1020083.JPG', NULL, 'P1020083.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(66, 29, 81, 'P1020084.JPG', 'P1020084.JPG', NULL, 'P1020084.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(67, 29, 81, 'P1020085.JPG', 'P1020085.JPG', NULL, 'P1020085.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(68, 29, 81, 'P1020086.JPG', 'P1020086.JPG', NULL, 'P1020086.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(69, 29, 81, 'P1020087.JPG', 'P1020087.JPG', NULL, 'P1020087.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(70, 29, 81, 'P1020088.JPG', 'P1020088.JPG', NULL, 'P1020088.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(71, 29, 81, 'P1020089.JPG', 'P1020089.JPG', NULL, 'P1020089.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(72, 29, 81, 'P1020090.JPG', 'P1020090.JPG', NULL, 'P1020090.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(73, 29, 81, 'P1020091.JPG', 'P1020091.JPG', NULL, 'P1020091.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(74, 29, 81, 'P1020092.JPG', 'P1020092.JPG', NULL, 'P1020092.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(75, 29, 81, 'P1020093.JPG', 'P1020093.JPG', NULL, 'P1020093.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(76, 29, 81, 'P1020094.JPG', 'P1020094.JPG', NULL, 'P1020094.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(77, 29, 81, 'P1020095.JPG', 'P1020095.JPG', NULL, 'P1020095.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(78, 29, 81, 'P1020096.JPG', 'P1020096.JPG', NULL, 'P1020096.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(79, 29, 81, 'P1020097.JPG', 'P1020097.JPG', NULL, 'P1020097.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(80, 29, 81, 'P1020098.JPG', 'P1020098.JPG', NULL, 'P1020098.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(81, 29, 81, 'P1020099.JPG', 'P1020099.JPG', NULL, 'P1020099.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(82, 29, 81, 'P1020100.JPG', 'P1020100.JPG', NULL, 'P1020100.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(83, 29, 81, 'P1020101.JPG', 'P1020101.JPG', NULL, 'P1020101.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(84, 29, 81, 'P1020102.JPG', 'P1020102.JPG', NULL, 'P1020102.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(85, 29, 81, 'P1020103.JPG', 'P1020103.JPG', NULL, 'P1020103.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(86, 29, 81, 'P1020104.JPG', 'P1020104.JPG', NULL, 'P1020104.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(87, 29, 81, 'P1020105.JPG', 'P1020105.JPG', NULL, 'P1020105.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(88, 29, 81, 'P1020106.JPG', 'P1020106.JPG', NULL, 'P1020106.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(89, 29, 81, 'P1020107.JPG', 'P1020107.JPG', NULL, 'P1020107.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(90, 29, 81, 'P1020108.JPG', 'P1020108.JPG', NULL, 'P1020108.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(91, 29, 81, 'P1020110.JPG', 'P1020110.JPG', NULL, 'P1020110.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(92, 29, 81, 'P1020111.JPG', 'P1020111.JPG', NULL, 'P1020111.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(93, 29, 81, 'P1020112.JPG', 'P1020112.JPG', NULL, 'P1020112.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(94, 29, 81, 'P1020113.JPG', 'P1020113.JPG', NULL, 'P1020113.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(95, 29, 81, 'P1020116.JPG', 'P1020116.JPG', NULL, 'P1020116.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(96, 29, 81, 'P1020119.JPG', 'P1020119.JPG', NULL, 'P1020119.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(97, 29, 81, 'P1020120.JPG', 'P1020120.JPG', NULL, 'P1020120.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(98, 29, 81, 'P1020121.JPG', 'P1020121.JPG', NULL, 'P1020121.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(99, 29, 81, 'P1020122.JPG', 'P1020122.JPG', NULL, 'P1020122.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(100, 29, 81, 'P1020123.JPG', 'P1020123.JPG', NULL, 'P1020123.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(101, 29, 81, 'P1020124.JPG', 'P1020124.JPG', NULL, 'P1020124.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(102, 29, 81, 'P1020125.JPG', 'P1020125.JPG', NULL, 'P1020125.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(103, 29, 81, 'P1020126.JPG', 'P1020126.JPG', NULL, 'P1020126.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(104, 29, 81, 'P1020127.JPG', 'P1020127.JPG', NULL, 'P1020127.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(105, 29, 81, 'P1020128.JPG', 'P1020128.JPG', NULL, 'P1020128.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(106, 29, 81, 'P1020129.JPG', 'P1020129.JPG', NULL, 'P1020129.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(107, 29, 81, 'P1020130.JPG', 'P1020130.JPG', NULL, 'P1020130.JPG', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(108, 29, 81, 'SP_A0995.jpg', 'SP_A0995.jpg', NULL, 'SP_A0995.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(109, 29, 81, 'SP_A0996.jpg', 'SP_A0996.jpg', NULL, 'SP_A0996.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(110, 29, 81, 'SP_A0997.jpg', 'SP_A0997.jpg', NULL, 'SP_A0997.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(111, 29, 81, 'SP_A0998.jpg', 'SP_A0998.jpg', NULL, 'SP_A0998.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(112, 29, 81, 'SP_A1001.jpg', 'SP_A1001.jpg', NULL, 'SP_A1001.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(113, 29, 81, 'SP_A1003.jpg', 'SP_A1003.jpg', NULL, 'SP_A1003.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(114, 29, 81, 'SP_A1004.jpg', 'SP_A1004.jpg', NULL, 'SP_A1004.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(115, 29, 81, 'SP_A1005.jpg', 'SP_A1005.jpg', NULL, 'SP_A1005.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(116, 29, 81, 'SP_A1006.jpg', 'SP_A1006.jpg', NULL, 'SP_A1006.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(117, 29, 81, 'SP_A1007.jpg', 'SP_A1007.jpg', NULL, 'SP_A1007.jpg', NULL, NULL, NULL, 0, '0000-00-00 00:00:00'),
(119, 31, 81, 'IMAG0011.JPG', 'IMAG0011.JPG', '', 'IMAG0011.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(120, 38, 82, 'IMAG0001.JPG', 'IMAG0001.JPG', '', 'IMAG0001.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(121, 38, 82, 'IMAG0002.JPG', 'IMAG0002.JPG', '', 'IMAG0002.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(122, 38, 82, 'IMAG0003.JPG', 'IMAG0003.JPG', '', 'IMAG0003.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(123, 38, 82, 'IMAG0004.JPG', 'IMAG0004.JPG', '', 'IMAG0004.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(124, 38, 82, 'IMAG0005.JPG', 'IMAG0005.JPG', '', 'IMAG0005.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(127, 38, 82, 'IMAG0008.JPG', 'IMAG0008.JPG', '', 'IMAG0008.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(128, 38, 82, 'IMAG0009.JPG', 'IMAG0009.JPG', '', 'IMAG0009.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(129, 38, 82, 'IMAG0010.JPG', 'IMAG0010.JPG', '', 'IMAG0010.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(130, 38, 82, 'IMAG0011.JPG', 'IMAG0011.JPG', '', 'IMAG0011.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00'),
(132, 38, 82, 'IMAG0014.JPG', 'IMAG0014.JPG', '', 'IMAG0014.JPG', '', NULL, NULL, 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_photos`
--

DROP TABLE IF EXISTS `vypecky_photogalery_photos`;
CREATE TABLE IF NOT EXISTS `vypecky_photogalery_photos` (
  `id_photo` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_galery` smallint(5) unsigned NOT NULL,
  `label_cs` varchar(500) DEFAULT NULL,
  `label_en` varchar(500) DEFAULT NULL,
  `label_de` varchar(500) DEFAULT NULL,
  `file` varchar(200) NOT NULL,
  `time_add` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_photo`),
  KEY `id_galery` (`id_galery`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_photos`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_products`
--

DROP TABLE IF EXISTS `vypecky_products`;
CREATE TABLE IF NOT EXISTS `vypecky_products` (
  `id_product` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned DEFAULT '1',
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) DEFAULT NULL,
  `label_cs` varchar(400) DEFAULT NULL,
  `text_cs` text,
  `label_en` varchar(400) DEFAULT NULL,
  `text_en` text,
  `lebal_de` varchar(400) DEFAULT NULL,
  `text_de` text,
  `main_image` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_product`),
  KEY `id_item` (`id_item`,`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `lebal_de` (`lebal_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_products`
--

INSERT INTO `vypecky_products` (`id_product`, `id_item`, `id_user`, `add_time`, `edit_time`, `label_cs`, `text_cs`, `label_en`, `text_en`, `lebal_de`, `text_de`, `main_image`) VALUES
(2, 10, 1, 1239816255, 1239964857, 'pokus', '<p>Pokusný text produktu</p>\r\n<p> </p>\r\n<p class="para">Instead of lots of commands to output HTML (as seen in C or Perl),     PHP pages contain HTML with embedded code that does     "something" (in this case, output "Hi, I\\\\\\\\\\\\\\''m a PHP script!").     The PHP code is enclosed in special <a class="link" href="file:////home/cuba/Docs/PHP/html/language.basic-syntax.phpmode.html">start and end processing     instructions <code class="code"> and <code class="code">?&gt;</code></code></a> that allow you to jump into and out of "PHP mode."</p>\r\n<p class="para">What distinguishes PHP from something like client-side JavaScript     is that the code is executed on the server, generating HTML which     is then sent to the client. The client would receive     the results of running that script, but would not know     what the underlying code was. You can even configure your web server     to process all your HTML files with PHP, and then there\\\\\\\\\\\\\\''s really no     way that users can tell what you have up your sleeve.</p>\r\n<p class="para">The best things in using PHP are that it is extremely simple     for a newcomer, but offers many advanced features for     a professional programmer. Don\\\\\\\\\\\\\\''t be afraid reading the long     list of PHP\\\\\\\\\\\\\\''s features. You can jump in, in a short time, and     start writing simple scripts in a few hours.</p>\r\n<p class="para">Although PHP\\\\\\\\\\\\\\''s development is focused on server-side scripting,     you can do much more with it. Read on, and see more in the     <a class="link" href="file:////home/cuba/Docs/PHP/html/intro-whatcando.html">What can PHP do?</a> section,     or go right to the <a class="link" href="file:////home/cuba/Docs/PHP/html/tutorial.html">introductory     tutorial</a> if you are only interested in web programming.</p>', NULL, NULL, NULL, NULL, 'okno-titul-stitulkem.jpg'),
(3, 10, 1, 1239817269, 1239817269, 'Dveře k oknům', '<p class="para">Instead of lots of commands to output HTML (as seen in C or Perl),     PHP pages contain HTML with embedded code that does     "something" (in this case, output "Hi, I\\''m a PHP script!").     The PHP code is enclosed in special <a class="link" href="file:////home/cuba/Docs/PHP/html/language.basic-syntax.phpmode.html">start and end processing     instructions <code class="code">&lt;?php</code> and <code class="code">?&gt;</code></a> that allow you to jump into and out of "PHP mode."</p>\r\n<p class="para">What distinguishes PHP from something like client-side JavaScript     is that the code is executed on the server, generating HTML which     is then sent to the client. The client would receive     the results of running that script, but would not know     what the underlying code was. You can even configure your web server     to process all your HTML files with PHP, and then there\\''s really no     way that users can tell what you have up your sleeve.</p>\r\n<p class="para">The best things in using PHP are that it is extremely simple     for a newcomer, but offers many advanced features for     a professional programmer. Don\\''t be afraid reading the long     list of PHP\\''s features. You can jump in, in a short time, and     start writing simple scripts in a few hours.</p>\r\n<p class="para">Although PHP\\''s development is focused on server-side scripting,     you can do much more with it. Read on, and see more in the     <a class="link" href="file:////home/cuba/Docs/PHP/html/intro-whatcando.html">What can PHP do?</a> section,     or go right to the <a class="link" href="file:////home/cuba/Docs/PHP/html/tutorial.html">introductory     tutorial</a> if you are only interested in web programming.</p>', NULL, NULL, NULL, NULL, 'madagaskar-2-1226738485.jpg'),
(4, 10, 1, 1239875036, 1239965458, 'Dveře k okýnkům', '<p>Málokterý element má při návrhu fasády takovou důležitost, jako vchodové dveře. Dveře by měly být vizitkou každého domu a zároveň zárukou maximální bezpečnosti a trvanlivosti. <br /> <br />Široká nabídka dveřních systémů TROCAL sahá od balkonových dveří, přes vedlejší vstupní dveře s hliníkovým prahem, až po vchodové dveře tuhostí srovnatelné s hliníkovými. Pro sladění designu s okenními systémy nabízíme samozřejmě i provedení elegance. I vchodové dveře mohou být opatřeny osvědčenou barevnou technologií AcrylProtect, DecoStyle s designem dřeva, nebo TROCAL AluClip, skýtajícím takřka neomezenou barevnou volbu dle vzorníku RAL. Standardem je otvírání dovnitř i ven. Všechny dveřní systémy jsou koncipovány na nejvyšší tuhost. Rohy jsou zesíleny rohovými spojkami.</p>\r\n<p> </p>\r\n<p><a rel="lightbox" href="data/userfiles/budova-milenium-center-s-parkovacim-domem.jpg"><img title="budova-milenium-center-s-parkovacim-domem.jpg" src="data/userfiles/budova-milenium-center-s-parkovacim-domem.jpg" alt="budova-milenium-center-s-parkovacim-domem.jpg" width="300" height="225" /></a></p>\r\n<p><br />Všechny dveře TROCAL odpovídají požadavkům normy DIN 18103. <br /> <br />Skutečný "vzhled" vašim dveřím propůjčí kromě různých kombinací ze sloupků vyráběných přímo v produkci oken i dveřní výplně. Informujte se u svého dodavatele oken TROCAL. <br /> <br />Kombinace tvarů, skel, barev a rozměrů jdou do tisíců. Naši specialisté jsou připraveni Vám zpracovat a prezentovat tu neefektivnější variantu.</p>', NULL, NULL, NULL, NULL, 'okna.jpg'),
(5, 10, 1, 1239963926, 1239963926, 'Plastová okna innoNova', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vulputate nibh. Etiam ut odio. Donec vel mauris. Nullam ut urna. Morbi sapien lectus, rutrum ac, malesuada in, tincidunt at, ligula. Proin non ipsum. Nunc nulla lectus, varius non, facilisis id, eleifend sed, justo. Duis ac nulla non eros rutrum condimentum. Etiam nunc velit, feugiat ac, vestibulum id, blandit quis, orci. Phasellus ultricies, mauris semper fringilla commodo, enim arcu porta orci, et auctor ligula ante non est. In faucibus, libero vitae eleifend porta, purus sem elementum quam, et dapibus mi urna sit amet nisl. Nullam sodales. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nam libero dolor, porta nec, sodales eget, sodales sed, ipsum. Proin a arcu non mi adipiscing ultricies. In gravida, nisi id ornare cursus, eros felis dapibus justo, vitae convallis massa lorem laoreet ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut eu libero quis purus condimentum suscipit. Proin eu nisl quis eros suscipit tempor. Curabitur sed metus vitae metus molestie feugiat.</p>\r\n<p>Nunc tempus. Mauris risus. Praesent porttitor, risus vel euismod feugiat, justo ligula ornare sem, eu ullamcorper ante justo sit amet erat. Etiam bibendum. Donec pellentesque. Pellentesque pellentesque lectus at nibh. Duis sollicitudin, leo non dapibus congue, erat orci placerat ipsum, nec semper lectus mauris ut risus. Nam auctor ullamcorper mauris. Nulla non augue. Suspendisse luctus convallis ipsum. Cras a leo sed felis faucibus auctor. Maecenas nec erat eget elit cursus commodo.</p>\r\n<p>Aliquam ut nulla. Suspendisse sodales libero fringilla odio. Vivamus turpis nisi, aliquam in, vehicula vel, tristique fermentum, nisl. Mauris urna justo, placerat vel, sodales cursus, mollis a, turpis. Quisque metus. Maecenas et magna eget urna ullamcorper fringilla. Vivamus ut nisi. Praesent quis sapien sit amet urna faucibus interdum. Vestibulum posuere, quam eleifend egestas tincidunt, nisi sapien viverra mauris, eget luctus dui nisl eu augue. Sed dictum, eros vitae mattis mattis, nunc augue lacinia eros, vel cursus urna ligula quis leo. Nam tempor. Nulla facilisi. Nunc risus. Pellentesque tortor. Donec purus. Morbi suscipit.</p>', NULL, NULL, NULL, NULL, 'okno-titul.jpg');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_references`
--

DROP TABLE IF EXISTS `vypecky_references`;
CREATE TABLE IF NOT EXISTS `vypecky_references` (
  `id_reference` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) DEFAULT NULL,
  `label_cs` text,
  `name_en` varchar(300) DEFAULT NULL,
  `label_en` text,
  `name_de` varchar(300) DEFAULT NULL,
  `label_de` text,
  `file` varchar(200) DEFAULT NULL,
  `changed_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_reference`),
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `name_cs` (`name_cs`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `name_en` (`name_en`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `name_de` (`name_de`),
  FULLTEXT KEY `label_de` (`label_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `vypecky_references`
--

INSERT INTO `vypecky_references` (`id_reference`, `id_item`, `name_cs`, `label_cs`, `name_en`, `label_en`, `name_de`, `label_de`, `file`, `changed_time`) VALUES
(7, 6, 'Stránky hudba valmez 2009', '<p>tránky k projektu valašského CD, které vychází každých 5 let. obsahují různé kapely od známých, jako mňága a žďorp až po úplné neznámé.</p>', 'english label', NULL, NULL, NULL, 'madagaskar-2-1226738485.jpg', 1238325126);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=144 ;

--
-- Vypisuji data pro tabulku `vypecky_rights`
--

INSERT INTO `vypecky_rights` (`id_right`, `id_category`, `id_group`, `right`) VALUES
(1, 68, 1, 'rwc'),
(2, 14, 1, 'rwc'),
(3, 50, 1, 'rwc'),
(4, 1, 2, 'r--'),
(5, 1, 1, 'rwc'),
(6, 1, 3, 'r--'),
(7, 1, 4, 'rw-'),
(8, 65, 1, 'rwc'),
(9, 65, 2, 'r--'),
(10, 65, 3, 'r--'),
(11, 65, 4, 'rw-'),
(12, 66, 1, 'rwc'),
(13, 66, 2, 'r--'),
(14, 66, 3, 'r--'),
(15, 66, 4, 'rw-'),
(16, 50, 2, '---'),
(17, 50, 3, '---'),
(18, 50, 4, 'r--'),
(19, 14, 2, '---'),
(20, 14, 3, '---'),
(21, 14, 4, '---'),
(22, 60, 1, 'rwc'),
(23, 60, 2, '---'),
(24, 60, 3, '---'),
(25, 60, 4, '---'),
(26, 51, 1, 'rwc'),
(27, 51, 2, '---'),
(28, 51, 3, '---'),
(29, 51, 4, '---'),
(30, 68, 2, '---'),
(31, 68, 3, '---'),
(32, 68, 4, 'r--'),
(33, 52, 1, 'rwc'),
(34, 52, 2, 'r--'),
(35, 52, 3, 'rwc'),
(36, 52, 4, 'rwc'),
(37, 67, 1, 'rwc'),
(38, 67, 2, 'r--'),
(39, 67, 3, 'r--'),
(40, 67, 4, 'rw-'),
(69, 69, 1, 'rwc'),
(70, 69, 2, '---'),
(71, 69, 3, '---'),
(72, 69, 4, 'rw-'),
(73, 69, 5, '---'),
(74, 70, 1, 'rwc'),
(75, 70, 2, 'r--'),
(76, 70, 3, 'r--'),
(77, 70, 4, 'rw-'),
(78, 70, 5, 'r--'),
(94, 74, 1, 'rwc'),
(95, 74, 2, 'r--'),
(96, 74, 3, 'r--'),
(97, 74, 4, 'rw-'),
(98, 74, 5, 'r--'),
(103, 75, 5, 'r--'),
(102, 75, 4, 'rw-'),
(101, 75, 3, 'r--'),
(100, 75, 2, 'r--'),
(99, 75, 1, 'rwc'),
(104, 76, 1, 'rwc'),
(105, 76, 2, 'r--'),
(106, 76, 3, 'r--'),
(107, 76, 4, 'rw-'),
(108, 76, 5, 'r--'),
(109, 77, 1, 'rwc'),
(110, 77, 2, 'r--'),
(111, 77, 3, 'r--'),
(112, 77, 4, 'r--'),
(113, 77, 5, 'r--'),
(114, 78, 1, 'rwc'),
(115, 78, 2, 'r--'),
(116, 78, 3, 'r--'),
(117, 78, 4, 'r--'),
(118, 78, 5, 'r--'),
(119, 79, 1, 'rwc'),
(120, 79, 2, 'r--'),
(121, 79, 3, 'r--'),
(122, 79, 4, 'r--'),
(123, 79, 5, 'r--'),
(124, 80, 1, 'rwc'),
(125, 80, 2, 'r--'),
(126, 80, 3, 'r--'),
(127, 80, 4, 'r--'),
(128, 80, 5, 'r--'),
(129, 66, 5, 'r--'),
(130, 65, 5, 'r--'),
(131, 67, 5, 'r--'),
(132, 1, 5, 'r--'),
(133, 81, 1, 'rwc'),
(134, 81, 2, 'r--'),
(135, 81, 3, 'r--'),
(136, 81, 4, 'rw-'),
(137, 81, 5, 'r--'),
(138, 60, 5, 'r--'),
(139, 82, 1, 'rwc'),
(140, 82, 2, 'r--'),
(141, 82, 3, 'r--'),
(142, 82, 4, 'rw-'),
(143, 82, 5, 'r--');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sections`
--

DROP TABLE IF EXISTS `vypecky_sections`;
CREATE TABLE IF NOT EXISTS `vypecky_sections` (
  `id_section` smallint(3) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL,
  `slabel_cs` varchar(50) DEFAULT NULL,
  `salt_cs` varchar(200) DEFAULT NULL,
  `slabel_en` varchar(50) DEFAULT NULL,
  `salt_en` varchar(200) DEFAULT NULL,
  `slabel_de` varchar(50) DEFAULT NULL,
  `salt_de` varchar(200) DEFAULT NULL,
  `priority` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_sections`
--

INSERT INTO `vypecky_sections` (`id_section`, `id_parent`, `slabel_cs`, `salt_cs`, `slabel_en`, `salt_en`, `slabel_de`, `salt_de`, `priority`) VALUES
(1, 0, 'section 1', NULL, NULL, NULL, NULL, NULL, 0),
(6, 0, 'sekce 2', 'druhá sekce s dalšími nástroji', NULL, NULL, NULL, NULL, 5),
(7, 1, 'pod sekce 1', NULL, NULL, NULL, NULL, NULL, 0),
(9, 1, 'podsekce 2', NULL, NULL, NULL, NULL, NULL, 0),
(10, 7, 'podpodsekce 1', NULL, NULL, NULL, NULL, NULL, 0);

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
  `subkey` varchar(30) NOT NULL DEFAULT 'NULL',
  `label_cs` varchar(200) DEFAULT NULL,
  `text_cs` mediumtext,
  `text_panel_cs` varchar(1000) DEFAULT NULL,
  `changed_time` int(11) DEFAULT NULL,
  `label_en` varchar(200) DEFAULT NULL,
  `text_en` mediumtext,
  `text_panel_en` varchar(1000) DEFAULT NULL,
  `label_de` varchar(200) DEFAULT NULL,
  `text_de` mediumtext,
  `text_panel_de` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--

INSERT INTO `vypecky_texts` (`id_text`, `id_item`, `subkey`, `label_cs`, `text_cs`, `text_panel_cs`, `changed_time`, `label_en`, `text_en`, `text_panel_en`, `label_de`, `text_de`, `text_panel_de`) VALUES
(9, 2, 'NULL', NULL, '<div id="lipsum">\r\n<p><strong>Lorem ipsum dolor sit </strong>amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p><img title="tango-feet.png" src="data/userfiles/tango-feet.png" alt="tango-feet.png" width="556" height="593" /></p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p> </p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>', NULL, 1244631636, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 1, 'NULL', '', '<p><br /><a rel="lightbox" href="data/images/resize.jpg"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/images/resize.jpg" alt="imag0001.jpg" width="267" height="200" /></a><strong><span style="font-size: xx-large;">Od 29.3. <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p><a rel="lightbox" href="data/photogalery/medium/DSC_8614.JPG"><img style="float: left;" src="data/photogalery/small/DSC_8614.JPG" alt="" width="75" height="75" /></a>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/images/okna-plastova.jpg" alt="00703.jpg" width="200" height="117" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p style="text-align: center;">\r\n<object width="425" height="344" data="http://www.youtube.com/v/1hrWjkn_DHs&amp;hl=cs_CZ&amp;fs=1&amp;rel=0&amp;color1=0x2b405b&amp;color2=0x6b8ab6" type="application/x-shockwave-flash">\r\n<param name="data" value="http://www.youtube.com/v/1hrWjkn_DHs&amp;hl=cs_CZ&amp;fs=1&amp;rel=0&amp;color1=0x2b405b&amp;color2=0x6b8ab6" />\r\n<param name="allowFullScreen" value="true" />\r\n<param name="allowscriptaccess" value="always" />\r\n<param name="src" value="http://www.youtube.com/v/1hrWjkn_DHs&amp;hl=cs_CZ&amp;fs=1&amp;rel=0&amp;color1=0x2b405b&amp;color2=0x6b8ab6" />\r\n<param name="allowfullscreen" value="true" />\r\n</object>\r\n</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. &nbsp;Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p>&nbsp;</p>', '<a rel="lightbox" href="data/photogalery/medium/DSC_8614.JPG"><img style="float: left; border: 0px initial initial;" src="data/photogalery/small/DSC_8614.JPG" alt="" width="75" height="75" /></a>česky - Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo.\r\n<div></div>\r\n<div></div>', 1260823798, '', '<h2><strong><span style="font-size: xx-large;">Od 29.3. a&nbsp;skladě nov&eacute; druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></h2>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/images/okna-plastova.jpg" alt="00703.jpg" width="200" height="117" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.             \r\n<object width="200" height="100" data="data/flash/menu_galerie_465x376.swf" type="application/x-shockwave-flash">\r\n<param name="data" value="data/flash/menu_galerie_465x376.swf" />\r\n<param name="src" value="data/flash/menu_galerie_465x376.swf" />\r\n</object>\r\nDuis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>', 'English - f(!empty($this-&gt;panels[''left''])) echo "col1pad_plusleft";', '', '<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/images/resize.jpg" alt="imag0001.jpg" width="267" height="200" /></a><strong><span style="font-size: xx-large;">Od 29.3. a&nbsp;skladě nov&eacute; druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/images/okna-plastova.jpg" alt="00703.jpg" width="200" height="117" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.      \r\n<object width="200" height="100" data="data/flash/menu_galerie_465x376.swf" type="application/x-shockwave-flash">\r\n<param name="data" value="data/flash/menu_galerie_465x376.swf" />\r\n<param name="src" value="data/flash/menu_galerie_465x376.swf" />\r\n</object>\r\nDuis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p>&nbsp;</p>\r\n<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/userimages/imag0001.jpg" alt="imag0001.jpg" width="201" height="149" /></a><strong><span style="font-size: xx-large;">Od 29.3. a&nbsp;skladě nov&eacute; druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/userfiles/00703.jpg" alt="00703.jpg" width="200" height="137" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p>&nbsp;</p>\r\n<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/userimages/imag0001.jpg" alt="imag0001.jpg" width="201" height="149" /></a><strong><span style="font-size: xx-large;">Od 29.3. m&eacute;me na&nbsp;skladě nov&eacute; druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/userfiles/00703.jpg" alt="00703.jpg" width="200" height="137" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p>&nbsp;</p>\r\n<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/userimages/imag0001.jpg" alt="imag0001.jpg" width="201" height="149" /></a><strong><span style="font-size: xx-large;">Od 29.3. a&nbsp;skladě nov&eacute; druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/userfiles/00703.jpg" alt="00703.jpg" width="200" height="137" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p>&nbsp;</p>\r\n<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/userimages/imag0001.jpg" alt="imag0001.jpg" width="201" height="149" /></a><strong><span style="font-size: xx-large;">Od 29.3. m&eacute;me na&nbsp;skladě nov&eacute; druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/userfiles/00703.jpg" alt="00703.jpg" width="200" height="137" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p>&nbsp;</p>\r\n<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/userimages/imag0001.jpg" alt="imag0001.jpg" width="201" height="149" /></a><strong><span style="font-size: xx-large;">Od 29.3. m&eacute;me na&nbsp;skladě nov&eacute; druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v&nbsp;barv&aacute;ch duhy</span></strong></p>\r\n<p>&nbsp;</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p>&nbsp;</p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a&nbsp;leo. Sed vehicula.</p>\r\n<p>&nbsp;</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/userfiles/00703.jpg" alt="00703.jpg" width="200" height="137" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a&nbsp;orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>&nbsp;</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p>&nbsp;</p>', ''),
(10, 5, 'NULL', NULL, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', NULL, 1237145467, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 6, 'NULL', NULL, '<ul>\r\n<li>Další reference</li>\r\n<li>dalfvdv</li>\r\n<li>fvfvfdvdf</li>\r\n<li>gvfdfvfdvfdv</li>\r\n<li>fdvfdvdfvdfv</li>\r\n<li>fdvdfvdfvdfvfdv</li>\r\n<li>dfvdfvdfvdfvf</li>\r\n<li>vdfvdfvdvfd</li>\r\n</ul>', NULL, 1238325822, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 0, 'NULL', NULL, '<div class="mxb">\r\n<h1>Ex-Nazi goes on trial in Germany</h1>\r\n</div>\r\n<!-- S BO --> <!-- S IIMA --> \r\n<table style="width: 226px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<div><img src="http://newsimg.bbc.co.uk/media/images/46623000/jpg/_46623941_008187545-1.jpg" border="0" alt="Heinrich Boere in court in Aachen 28.10.09 " hspace="0" vspace="0" width="226" height="170" />\r\n<div class="cap">Heinrich Boere was 18 when he joined the notorious Waffen SS</div>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IIMA --> <!-- S SF -->\r\n<p class="first"><strong>A former member of the Nazi SS has gone on trial in Germany charged with the wartime murder of three civilians in the Netherlands.</strong></p>\r\n<p>Heinrich Boere, 88, has previously acknowledged shooting dead three people in 1944, as reprisals for attacks by the Dutch resistance.</p>\r\n<p>The trial went ahead after an appeal court ruled he was fit to be tried.</p>\r\n<p>However, the hearing was adjourned when the five-judge panel said it needed time to consider more legal argument.</p>\r\n<!-- E SF -->\r\n<p>The trial is due to resume on Monday, court officials said.</p>\r\n<p>Anti-Nazi protesters had gathered outside the court in Aachen as the trial opened.</p>\r\n<p>Relatives of some of the victims were also in court.</p>\r\n<p>Correspondents said Heinrich Boere entered the courtroom in a wheelchair with a doctor by his side, but appeared alert and attentive as he answered questions. The hearing was adjourned shortly afterwards.</p>\r\n<p>The defendent is charged with killing three men: Fritz Bicknese, a chemist and father of 12; bicycle seller Teun de Groot, who helped Jews go into hiding; and resistance member Frans Kusters.</p>\r\n<p>He admitted the killings to Dutch authorities while in captivity after the war, but escaped before he could be brought to trial. He later fled to Germany.</p>\r\n<p><strong>''Killing terrorists''</strong></p>\r\n<p>He has also confessed to his role in interviews with the media.</p>\r\n<p>"Yes, I got rid of them," he told Focus magazine. "It was not difficult. You just had to bend a finger."</p>\r\n<p>He told Spiegel magazine that he and his accomplices thought they were killing "terrorists", adding: "We thought we were doing the right thing."</p>\r\n<!-- S IIMA --> \r\n<table style="width: 226px;" border="0" cellspacing="0" cellpadding="0" align="right">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<div><img src="http://newsimg.bbc.co.uk/media/images/46627000/jpg/_46627835_008187803-2.jpg" border="0" alt="Banner ''No peace for NS delinquents'' outside Aachen court" hspace="0" vspace="0" width="226" height="170" />\r\n<div class="cap">Anti-Nazi protesters displayed banners outside the court</div>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<!-- E IIMA -->\r\n<p>A tribunal in Amsterdam sentenced him to death in absentia in 1949, a sentence later reduced to life in prison.</p>\r\n<p>A Dutch extradition request was turned down by Germany in the early 1980s.</p>\r\n<p>He was eventually indicted in Germany last year, but a court in Aachen then said he was unfit to stand trial due to health problems.</p>\r\n<p>That ruling was reversed in July by an appeals court in Cologne.</p>\r\n<p>Boere, who is of Dutch-German origin, was 18 when he joined the SS in 1940, shortly after the Germans overran his hometown of Maastricht.</p>\r\n<p>After fighting on the Russian front, he went to Holland as part of an SS death squad codenamed Silbertanne (Silver Pine).</p>\r\n<p>His statements to Dutch authorities are expected to form the basis for the prosecution''s case, the Associated Press news agency reports.</p>\r\n<p>Defence lawyers have declined to say how they will try to counter the confession.</p>\r\n<p>But even if he is convicted there remains some doubt over whether he will actually go to jail.</p>\r\n<p>A 90-year-old former German infantry commander, Josef Scheungraber, was given a life sentence by a German court in August, but remains free while his appeal is heard.</p>', NULL, 1256758151, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `vypecky_texts` (`id_text`, `id_item`, `subkey`, `label_cs`, `text_cs`, `text_panel_cs`, `changed_time`, `label_en`, `text_en`, `text_panel_en`, `label_de`, `text_de`, `text_panel_de`) VALUES
(14, 12, 'NULL', 'revizionismus', '<p><strong>Revizionismus</strong> (z <a title="Latina" href="http://cs.wikipedia.org/wiki/Latina">lat.</a> <em>re-videre</em>, <em>re-visus</em>, znovu prohl&eacute;dnout, ověřit) je soustavn&eacute; &uacute;sil&iacute; o přehodnocen&iacute;, změny př&iacute;padně &uacute;pravy dan&eacute;ho (st&aacute;vaj&iacute;c&iacute;ho) stavu, <a title="Ideologie" href="http://cs.wikipedia.org/wiki/Ideologie">ideologie</a> nebo vět&scaron;inov&eacute;ho n&aacute;zoru, a to ve společensky v&yacute;znamn&eacute; věci. Proto mohl b&yacute;t předmětem pron&aacute;sledov&aacute;n&iacute; i v&aacute;&scaron;niv&yacute;ch diskus&iacute;.</p>\r\n<p>Pojem &bdquo;<em>revizionismus</em>&ldquo; m&aacute; několik v&yacute;znamů:</p>\r\n<ul>\r\n<li>pejorativn&iacute; označen&iacute; "od&scaron;těpenců", kteř&iacute; chtěli revidovat z&aacute;klady leninsk&eacute; ideologie</li>\r\n<li>společensky v&yacute;znamn&yacute; pokus o nov&yacute; pohled na (ned&aacute;vn&eacute;) dějiny, někdy i s politick&yacute;m z&aacute;měrem</li>\r\n<li>soustavnou snahu o změnu vět&scaron;inov&eacute;ho n&aacute;zoru, "hlavn&iacute;ho proudu" i v jin&yacute;ch věd&aacute;ch.</li>\r\n</ul>\r\n<table id="toc" class="toc">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<div id="toctitle">\r\n<h2>Obsah</h2>\r\n</div>\r\n<ul>\r\n<li class="tocsection-1 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#P.C5.AFvod_slova"><span class="tocnumber">1</span> <span class="toctext">Původ slova</span></a></li>\r\n<li class="tocsection-2 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Oportunistick.C3.BD_sm.C4.9Br_v_revolu.C4.8Dn.C3.ADm_d.C4.9Blnick.C3.A9m_hnut.C3.AD"><span class="tocnumber">2</span> <span class="toctext">Oportunistick&yacute; směr v revolučn&iacute;m dělnick&eacute;m hnut&iacute;</span></a></li>\r\n<li class="tocsection-3 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Historick.C3.BD_revizionismus"><span class="tocnumber">3</span> <span class="toctext">Historick&yacute; revizionismus</span></a> \r\n<ul>\r\n<li class="tocsection-4 toclevel-2"><a href="http://cs.wikipedia.org/wiki/Revizionismus#SSSR_a_druh.C3.A1_sv.C4.9Btov.C3.A1_v.C3.A1lka"><span class="tocnumber">3.1</span> <span class="toctext">SSSR a druh&aacute; světov&aacute; v&aacute;lka</span></a></li>\r\n<li class="tocsection-5 toclevel-2"><a href="http://cs.wikipedia.org/wiki/Revizionismus#.C4.8Cesko.2C_Sudety.2C_Mnichov.2C_nacionalismus"><span class="tocnumber">3.2</span> <span class="toctext">Česko, Sudety, Mnichov, nacionalismus</span></a></li>\r\n</ul>\r\n</li>\r\n<li class="tocsection-6 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#N.C4.9Bkte.C5.99.C3.AD_historiografov.C3.A9_-_revizionist.C3.A9"><span class="tocnumber">4</span> <span class="toctext">Někteř&iacute; historiografov&eacute; - revizionist&eacute;</span></a></li>\r\n<li class="tocsection-7 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Revizionismus.2C_fal.C5.A1ov.C3.A1n.C3.AD_historie_a_propaganda"><span class="tocnumber">5</span> <span class="toctext">Revizionismus, fal&scaron;ov&aacute;n&iacute; historie a propaganda</span></a> \r\n<ul>\r\n<li class="tocsection-8 toclevel-2"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Sudeton.C4.9Bmeck.C3.BD_revizionismus_a_fal.C5.A1ov.C3.A1n.C3.AD_historie"><span class="tocnumber">5.1</span> <span class="toctext">Sudetoněmeck&yacute; revizionismus a fal&scaron;ov&aacute;n&iacute; historie</span></a></li>\r\n<li class="tocsection-9 toclevel-2"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Komunistick.C3.A1_le.C5.BE"><span class="tocnumber">5.2</span> <span class="toctext">Komunistick&aacute; lež</span></a></li>\r\n</ul>\r\n</li>\r\n<li class="tocsection-10 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Reference"><span class="tocnumber">6</span> <span class="toctext">Reference</span></a></li>\r\n<li class="tocsection-11 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Souvisej.C3.ADc.C3.AD_.C4.8Dl.C3.A1nky"><span class="tocnumber">7</span> <span class="toctext">Souvisej&iacute;c&iacute; čl&aacute;nky</span></a></li>\r\n<li class="tocsection-12 toclevel-1"><a href="http://cs.wikipedia.org/wiki/Revizionismus#Extern.C3.AD_odkazy"><span class="tocnumber">8</span> <span class="toctext">Extern&iacute; odkazy</span></a></li>\r\n</ul>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<script type="text/javascript">// <![CDATA[\r\n// <![CDATA[\r\nif (window.showTocToggle) { var tocShowText = "zobrazit"; var tocHideText = "skrýt"; showTocToggle(); }\r\n// ]]></script>\r\n<h2><span id="P.C5.AFvod_slova" class="mw-headline">Původ slova</span></h2>\r\n<p>Podle anglick&eacute;ho etymologick&eacute;ho slovn&iacute;ku je slovo poprv&eacute; doloženo k roku 1903 jako pejorativn&iacute; označen&iacute; <a title="Soci&aacute;ln&iacute; demokracie" href="http://cs.wikipedia.org/wiki/Soci%C3%A1ln%C3%AD_demokracie">soci&aacute;lně demokratick&yacute;ch</a> snah v <a title="Marxismus" href="http://cs.wikipedia.org/wiki/Marxismus">marxismu</a> ("<a title="Oportunismus" href="http://cs.wikipedia.org/wiki/Oportunismus">oportunismus</a>"), zejm&eacute;na pro opu&scaron;těn&iacute; tez&iacute; o <a class="new" title="Tř&iacute;dn&iacute; boj (str&aacute;nka neexistuje)" href="http://cs.wikipedia.org/w/index.php?title=T%C5%99%C3%ADdn%C3%AD_boj&amp;action=edit&amp;redlink=1">tř&iacute;dn&iacute;m boji</a> a <a class="new" title="Prolet&aacute;řsk&aacute; revoluce (str&aacute;nka neexistuje)" href="http://cs.wikipedia.org/w/index.php?title=Prolet%C3%A1%C5%99sk%C3%A1_revoluce&amp;action=edit&amp;redlink=1">prolet&aacute;řsk&eacute; revoluci</a>. Ve smyslu snah o revizi historie se poprv&eacute; vyskytuje 1934 v souvislosti s př&iacute;činami <a title="Prvn&iacute; světov&aacute; v&aacute;lka" href="http://cs.wikipedia.org/wiki/Prvn%C3%AD_sv%C4%9Btov%C3%A1_v%C3%A1lka">Prvn&iacute; světov&eacute; v&aacute;lky</a>.<sup id="cite_ref-0" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-0">[1]</a></sup></p>\r\n<h2><span id="Oportunistick.C3.BD_sm.C4.9Br_v_revolu.C4.8Dn.C3.ADm_d.C4.9Blnick.C3.A9m_hnut.C3.AD" class="mw-headline">Oportunistick&yacute; směr v revolučn&iacute;m dělnick&eacute;m hnut&iacute;</span></h2>\r\n<p>V encyklopedick&yacute;ch <a title="Slovn&iacute;k" href="http://cs.wikipedia.org/wiki/Slovn%C3%ADk">slovn&iacute;c&iacute;ch</a> se často setk&aacute;v&aacute;me s t&iacute;mto pojmem v souvislosti <a title="Komunismus" href="http://cs.wikipedia.org/wiki/Komunismus">komunistickou ideologi&iacute;</a>. V tomto pojet&iacute; je člověk hl&aacute;saj&iacute;c&iacute; revizionismus naz&yacute;v&aacute;n jako &bdquo;<em>revizionista</em>&ldquo;, což je hanliv&yacute; v&yacute;raz použ&iacute;van&yacute; pro ty kteř&iacute; maj&iacute; snahu revidovat my&scaron;lenky <a class="mw-redirect" title="Marxismus-leninismus" href="http://cs.wikipedia.org/wiki/Marxismus-leninismus">marxismu-leninismu</a>, či pro ty, kteř&iacute; usiluj&iacute; o dosažen&iacute; změn vedouc&iacute;ch k <a title="Socialismus" href="http://cs.wikipedia.org/wiki/Socialismus">socialismu</a> pozvolnou cestou respektov&aacute;n&iacute; z&aacute;konů, což je v rozporu s revolučn&iacute;m pojet&iacute;m těchto změn, hl&aacute;san&yacute;ch ofici&aacute;ln&iacute; komunistickou ideologi&iacute;.<sup id="cite_ref-1" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-1">[2]</a></sup></p>\r\n<h2><span id="Historick.C3.BD_revizionismus" class="mw-headline">Historick&yacute; revizionismus</span></h2>\r\n<table style="margin: 1em auto; background: transparent none repeat scroll 0% 0%;">\r\n<tbody>\r\n<tr style="font-family: ''Times New Roman CE'',''Times New CE'',''Times CE'',''Times New Roman'',times,serif;">\r\n<td style="color: #aaaaaa; font-size: 400%; font-weight: bold; vertical-align: bottom; padding-bottom: 0.7ex;">&bdquo;</td>\r\n<td style="font-size: 110%; font-style: italic; text-align: justify;">Evoluce života a evolučn&iacute; biologie jsou v podobn&eacute;m vztahu jako historie a historiografie.<sup id="cite_ref-2" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-2">[3]</a></sup></td>\r\n<td style="color: #aaaaaa; font-size: 400%; font-weight: bold; vertical-align: top; padding-top: 0.3ex;">&ldquo;</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2" style="text-align: right;"><cite style="font-style: normal;">&mdash; <a title="Anton Marko&scaron;" href="http://cs.wikipedia.org/wiki/Anton_Marko%C5%A1">Anton Marko&scaron;</a></cite></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><em>Historick&yacute; revizionismus</em> je soustavn&aacute; snaha <a title="Historiografie" href="http://cs.wikipedia.org/wiki/Historiografie">historiografů</a> o nov&eacute; hodnocen&iacute; (zpravidla ned&aacute;vn&yacute;ch) dějin, a to s politick&yacute;m v&yacute;znamem. Může b&yacute;t založena na n&aacute;lezu nov&yacute;ch, dosud nezn&aacute;m&yacute;ch pramenů, anebo na nov&eacute; interpretaci pramenů již zn&aacute;m&yacute;ch. Pot&eacute; je revidov&aacute;na <a title="Historie" href="http://cs.wikipedia.org/wiki/Historie">historie</a>. V jin&yacute;ch věd&aacute;ch se někdy hovoř&iacute; o <em>spekulativn&iacute; rekonstrukci</em>.<sup id="cite_ref-3" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-3">[4]</a></sup>.</p>\r\n<h3><span id="SSSR_a_druh.C3.A1_sv.C4.9Btov.C3.A1_v.C3.A1lka" class="mw-headline">SSSR a druh&aacute; světov&aacute; v&aacute;lka</span></h3>\r\n<p>Mezi historick&eacute; revizionisty patř&iacute; např&iacute;klad vojensk&yacute; historik <a title="Viktor Suvorov" href="http://cs.wikipedia.org/wiki/Viktor_Suvorov">Viktor Suvorov</a>, jenž usvědčuje <a title="Sovětsk&yacute; svaz" href="http://cs.wikipedia.org/wiki/Sov%C4%9Btsk%C3%BD_svaz">Sovětsk&yacute; svaz</a> z př&iacute;pravy <a title="Druh&aacute; světov&aacute; v&aacute;lka" href="http://cs.wikipedia.org/wiki/Druh%C3%A1_sv%C4%9Btov%C3%A1_v%C3%A1lka">druh&eacute; světov&eacute; v&aacute;lky</a>.<sup id="cite_ref-4" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-4">[5]</a></sup>,<sup id="cite_ref-5" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-5">[6]</a></sup>,<sup id="cite_ref-6" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-6">[7]</a></sup>,<sup id="cite_ref-7" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-7">[8]</a></sup> Odpůrci V. Suvorova se ho snaž&iacute; různě zpochybňovat a obviňovat z přeh&aacute;něn&iacute;.<sup id="cite_ref-8" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-8">[9]</a></sup> Sovětskou &eacute;ru nyn&iacute; popisuj&iacute; jako okupaci b&yacute;val&eacute; sovětsk&eacute; republiky.<sup id="cite_ref-9" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-9">[10]</a></sup> V Brně prob&iacute;hal spor o um&iacute;stěn&iacute; symbolu srpu a kladiva na pomn&iacute;k padl&yacute;ch rudoarmějců.<sup id="cite_ref-10" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-10">[11]</a></sup></p>\r\n<p>Na tyto tendence reagovalo Rusko. <a title="Rusko" href="http://cs.wikipedia.org/wiki/Rusko">Rusko</a> chce trestat veřejně zpochybňov&aacute;n&iacute; v&iacute;tězstv&iacute; Sovětsk&eacute;ho svazu ve Velk&eacute; vlasteneck&eacute; v&aacute;lce a v&yacute;roky znevažuj&iacute;c&iacute; rozhoduj&iacute;c&iacute; &uacute;lohu SSSR ve 2. světov&eacute; v&aacute;lce.<sup id="cite_ref-11" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-11">[12]</a></sup> Kreml z&aacute;roveň ustanovil komisi, kter&aacute; m&aacute; "br&aacute;nit zkreslov&aacute;n&iacute; dějin".<sup id="cite_ref-12" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-12">[13]</a></sup>,<sup id="cite_ref-13" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-13">[14]</a></sup>,<sup id="cite_ref-14" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-14">[15]</a></sup> Ministerstvo obrany Rusk&eacute; federace tvrd&iacute;, že 2. světovou v&aacute;lku rozpoutalo Polsko, a to odm&iacute;tnut&iacute;m německ&yacute;ch požadavků začlenit město Gdaňsk do Německa a vybudovat exteritori&aacute;ln&iacute; d&aacute;lnici a železnici jako koridor spojuj&iacute;c&iacute; V&yacute;chodn&iacute; Prusko s Německem.<sup id="cite_ref-15" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-15">[16]</a></sup>,<sup id="cite_ref-16" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-16">[17]</a></sup>,<sup id="cite_ref-17" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-17">[18]</a></sup>,<sup id="cite_ref-18" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-18">[19]</a></sup>,<sup id="cite_ref-19" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-19">[20]</a></sup>,<sup id="cite_ref-20" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-20">[21]</a></sup> V roce 2009 Rusko tak&eacute; obvinilo Ukrajinu z fal&scaron;ov&aacute;n&iacute; historie. Podle Ruska jsou na někter&yacute;ch fotografi&iacute;ch vystaven&yacute;ch v Sevastopolu v muzeu hladomoru nejsou hladověj&iacute;c&iacute; Ukrajinci, n&yacute;brž chud&iacute; Američan&eacute;.<sup id="cite_ref-21" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-21">[22]</a></sup> Ministerstvo vnitra RF zase ve sv&eacute; dějepisn&eacute; př&iacute;ručce mimo jin&eacute; tvrd&iacute;, že "sionist&eacute; fyzicky odstranili Stalina" nebo to, že sovětsk&yacute; prezident Michail Gorbačov byl ve skutečnosti sionistick&yacute;m agentem jm&eacute;nem Garber a SSSR pr&yacute; z&aacute;měrně zničil "ve jm&eacute;nu boha Mojž&iacute;&scaron;e".<sup id="cite_ref-22" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-22">[23]</a></sup></p>\r\n<h3><span id=".C4.8Cesko.2C_Sudety.2C_Mnichov.2C_nacionalismus" class="mw-headline">Česko, Sudety, Mnichov, nacionalismus</span></h3>\r\n<p>V Česku publikoval s&eacute;rii čl&aacute;nků o česk&yacute;ch dějin&aacute;ch spisovatel <a title="Tom&aacute;&scaron; Krystl&iacute;k" href="http://cs.wikipedia.org/wiki/Tom%C3%A1%C5%A1_Krystl%C3%ADk">Tom&aacute;&scaron; Krystl&iacute;k</a><sup id="cite_ref-23" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-23">[24]</a></sup>,<sup id="cite_ref-24" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-24">[25]</a></sup>, kter&yacute; pod&aacute;v&aacute; nov&yacute; pohled např&iacute;klad na "n&aacute;rodn&iacute; m&yacute;ty, podvrhy a lži"<sup id="cite_ref-25" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-25">[26]</a></sup>,<sup id="cite_ref-26" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-26">[27]</a></sup>, nebo na mnichovsk&eacute; ud&aacute;losti<sup id="cite_ref-27" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-27">[28]</a></sup>. Oponenti T. Krystl&iacute;ka ho obvinili z "fal&scaron;ov&aacute;n&iacute; dějin"<sup id="cite_ref-28" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-28">[29]</a></sup>,<sup id="cite_ref-29" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-29">[30]</a></sup>,<sup id="cite_ref-30" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-30">[31]</a></sup>,<sup id="cite_ref-31" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-31">[32]</a></sup>.</p>\r\n<p>Blogger Finrod Felagund<sup id="cite_ref-32" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-32">[33]</a></sup> na sv&eacute;m blogu Nekorektně.com ře&scaron;&iacute; ot&aacute;zku, zda je odpor k <a title="Sudet&scaron;t&iacute; Němci" href="http://cs.wikipedia.org/wiki/Sudet%C5%A1t%C3%AD_N%C4%9Bmci">sudetsk&yacute;m Němcům</a> leg&aacute;ln&iacute; obdobou antisemitismu<sup id="cite_ref-33" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-33">[34]</a></sup>.</p>\r\n<p>V srpnu 2009 odm&iacute;tlo Ministerstvo vnitra ČR registraci <a title="Sudetoněmeck&eacute; krajansk&eacute; sdružen&iacute;" href="http://cs.wikipedia.org/wiki/Sudeton%C4%9Bmeck%C3%A9_krajansk%C3%A9_sdru%C5%BEen%C3%AD">Sudetoněmeck&eacute;ho krajansk&eacute;ho sdružen&iacute;</a> v Čech&aacute;ch, na Moravě a ve Slezsku.<sup id="cite_ref-34" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-34">[35]</a></sup>,<sup id="cite_ref-35" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-35">[36]</a></sup></p>\r\n<h2><span id="N.C4.9Bkte.C5.99.C3.AD_historiografov.C3.A9_-_revizionist.C3.A9" class="mw-headline">Někteř&iacute; historiografov&eacute; - revizionist&eacute;</span></h2>\r\n<ul>\r\n<li><a title="Franz Chocholat&yacute;-Gr&ouml;ger" href="http://cs.wikipedia.org/wiki/Franz_Chocholat%C3%BD-Gr%C3%B6ger">Franz Chocholat&yacute;-Gr&ouml;ger</a></li>\r\n<li><a title="Tom&aacute;&scaron; Krystl&iacute;k" href="http://cs.wikipedia.org/wiki/Tom%C3%A1%C5%A1_Krystl%C3%ADk">Tom&aacute;&scaron; Krystl&iacute;k</a></li>\r\n<li><a title="Viktor Suvorov" href="http://cs.wikipedia.org/wiki/Viktor_Suvorov">Viktor Suvorov</a></li>\r\n</ul>\r\n<h2><span id="Revizionismus.2C_fal.C5.A1ov.C3.A1n.C3.AD_historie_a_propaganda" class="mw-headline">Revizionismus, fal&scaron;ov&aacute;n&iacute; historie a propaganda</span></h2>\r\n<p>K &bdquo;<em>revizionismus</em>&ldquo; se hl&aacute;s&iacute; tak&eacute; propagandist&eacute;, jejichž c&iacute;lem je &uacute;sil&iacute; o změnu a revizi dějin, a to tak z&aacute;sadn&iacute;m způsobem, že doch&aacute;z&iacute; nejen k překrucov&aacute;n&iacute; dějin, ale i k pop&iacute;r&aacute;n&iacute; historicky doložiteln&yacute;ch ud&aacute;lost&iacute;.<sup id="cite_ref-36" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-36">[37]</a></sup> Typickou uk&aacute;zkou je revizionismus <a title="Druh&aacute; světov&aacute; v&aacute;lka" href="http://cs.wikipedia.org/wiki/Druh%C3%A1_sv%C4%9Btov%C3%A1_v%C3%A1lka">2. světov&eacute; v&aacute;lky</a>, či revizionismus <a title="Holocaust" href="http://cs.wikipedia.org/wiki/Holocaust">holocaustu</a>, spoč&iacute;vaj&iacute;c&iacute; např. v:</p>\r\n<ul>\r\n<li>zamlžov&aacute;n&iacute; nacistick&eacute; politiky a nacistick&eacute;ho režimu v hitlerovsk&eacute;m <a title="Třet&iacute; ř&iacute;&scaron;e" href="http://cs.wikipedia.org/wiki/T%C5%99et%C3%AD_%C5%99%C3%AD%C5%A1e">Německu</a>, bagatelizaci jeho totalitn&iacute;ho syst&eacute;mu</li>\r\n<li>bagatelizaci německ&eacute; viny za rozpout&aacute;n&iacute; <a title="Druh&aacute; světov&aacute; v&aacute;lka" href="http://cs.wikipedia.org/wiki/Druh%C3%A1_sv%C4%9Btov%C3%A1_v%C3%A1lka">druh&eacute; světov&eacute; v&aacute;lky</a></li>\r\n<li>zlehčov&aacute;n&iacute; <a title="Německ&eacute; v&aacute;lečn&eacute; zločiny ve druh&eacute; světov&eacute; v&aacute;lce" href="http://cs.wikipedia.org/wiki/N%C4%9Bmeck%C3%A9_v%C3%A1le%C4%8Dn%C3%A9_zlo%C4%8Diny_ve_druh%C3%A9_sv%C4%9Btov%C3%A9_v%C3%A1lce">německ&yacute;ch v&aacute;lečn&yacute;ch zločinů</a></li>\r\n<li>kladen&iacute; rovn&iacute;tka mezi př&iacute;slu&scaron;n&iacute;ky n&aacute;rodů a občany st&aacute;tů, kteř&iacute; se stali obět&iacute; v&aacute;lky, a těmi, kteř&iacute; byli jako agresoři a vin&iacute;ci v&aacute;lky nějak&yacute;m způsobem postiženi</li>\r\n<li><strong><a class="mw-redirect" title="Pop&iacute;r&aacute;n&iacute; holocaustu" href="http://cs.wikipedia.org/wiki/Pop%C3%ADr%C3%A1n%C3%AD_holocaustu">pop&iacute;r&aacute;n&iacute; holocaustu</a></strong> (tzv. <em>Osvětimsk&aacute; lež</em>)</li>\r\n</ul>\r\n<p>Tento &bdquo;<em>historick&yacute; revizionismus</em>&ldquo; je využ&iacute;v&aacute;n např&iacute;klad neonacistick&yacute;mi a extr&eacute;mistick&yacute;mi hnut&iacute;mi, použ&iacute;vaj&iacute; ho i někteř&iacute; lid&eacute; a skupiny, jenž se odm&iacute;tli sm&iacute;řit s por&aacute;žkou nacistick&eacute;ho <a title="Německo" href="http://cs.wikipedia.org/wiki/N%C4%9Bmecko">Německa</a> ve <a title="Druh&aacute; světov&aacute; v&aacute;lka" href="http://cs.wikipedia.org/wiki/Druh%C3%A1_sv%C4%9Btov%C3%A1_v%C3%A1lka">2. světov&eacute; v&aacute;lce</a>. Např&iacute;klad na <a title="Ukrajina" href="http://cs.wikipedia.org/wiki/Ukrajina">Ukrajině</a> se za Ju&scaron;čenkovy vl&aacute;dy &bdquo;přejmenov&aacute;vaj&iacute; ulice a n&aacute;měst&iacute;, odstraňuj&iacute; pomn&iacute;ky Rud&eacute; arm&aacute;dy a hrdinů Velk&eacute; vlasteneck&eacute; v&aacute;lky, přepisuj&iacute; &scaron;koln&iacute; učebnice a oslavuje se režim velitele Ukrajinsk&eacute; povstaleck&eacute; arm&aacute;dy (UPA) &Scaron;ucheviče a jeho banditů, Bandera a jednotky SS-Galizien.&ldquo;<sup id="cite_ref-37" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-37">[38]</a></sup>,<sup id="cite_ref-38" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-38">[39]</a></sup></p>\r\n<h3><span id="Sudeton.C4.9Bmeck.C3.BD_revizionismus_a_fal.C5.A1ov.C3.A1n.C3.AD_historie" class="mw-headline">Sudetoněmeck&yacute; revizionismus a fal&scaron;ov&aacute;n&iacute; historie</span></h3>\r\n<p>V česk&yacute;ch zem&iacute;ch je t&eacute;ž použ&iacute;v&aacute;n term&iacute;n &bdquo;<em>sudetoněmeck&yacute; revizionismus</em> (tzv. <em>sudetsk&aacute; lež</em><sup id="cite_ref-39" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-39">[40]</a></sup>),&ldquo; což je určit&aacute; obdoba historick&eacute;ho revizionismu<sup id="cite_ref-40" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-40">[41]</a></sup>,<sup id="cite_ref-41" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-41">[42]</a></sup>. Spoč&iacute;v&aacute; zejm&eacute;na v:</p>\r\n<ul>\r\n<li>odm&iacute;t&aacute;n&iacute; jak&eacute;koliv odpovědnosti <a title="Sudet&scaron;t&iacute; Němci" href="http://cs.wikipedia.org/wiki/Sudet%C5%A1t%C3%AD_N%C4%9Bmci">sudetsk&yacute;ch Němců</a> za rozpout&aacute;n&iacute; otevřen&eacute;ho nepř&aacute;telstv&iacute; vůči <a title="Če&scaron;i" href="http://cs.wikipedia.org/wiki/%C4%8Ce%C5%A1i">Čechům</a> a <a title="Žid&eacute;" href="http://cs.wikipedia.org/wiki/%C5%BDid%C3%A9">Židům</a> ve 30. letech 20. stolet&iacute;, kter&eacute; bylo realizov&aacute;no <a title="Sudetoněmeck&aacute; strana" href="http://cs.wikipedia.org/wiki/Sudeton%C4%9Bmeck%C3%A1_strana">Sudetoněmeckou stranou</a>, podporovanou <a title="Adolf Hitler" href="http://cs.wikipedia.org/wiki/Adolf_Hitler">Adolfem Hitlerem</a></li>\r\n<li>pop&iacute;r&aacute;n&iacute; toho, že <a title="Vyhn&aacute;n&iacute; Čechů ze Sudet v roce 1938" href="http://cs.wikipedia.org/wiki/Vyhn%C3%A1n%C3%AD_%C4%8Cech%C5%AF_ze_Sudet_v_roce_1938">vyhn&aacute;n&iacute; Čechů z pohranič&iacute;</a> v roce <a title="1938" href="http://cs.wikipedia.org/wiki/1938">1938</a> bylo velk&yacute;m d&iacute;lem realizov&aacute;no formou n&aacute;tlaku a v&yacute;hrůžek <a title="Sudet&scaron;t&iacute; Němci" href="http://cs.wikipedia.org/wiki/Sudet%C5%A1t%C3%AD_N%C4%9Bmci">sudetsk&yacute;ch Němců</a></li>\r\n<li>bagatelizov&aacute;n&iacute; &uacute;tlaku a str&aacute;d&aacute;n&iacute; česk&eacute;ho obyvatelstva za <a title="Protektor&aacute;t Čechy a Morava" href="http://cs.wikipedia.org/wiki/Protektor%C3%A1t_%C4%8Cechy_a_Morava">Protektor&aacute;tu</a>, zlehčov&aacute;n&iacute; důsledků rozbit&iacute; <a title="Československo" href="http://cs.wikipedia.org/wiki/%C4%8Ceskoslovensko">Československa</a> a n&aacute;sledn&eacute; německ&eacute; okupace <a title="Čechy" href="http://cs.wikipedia.org/wiki/%C4%8Cechy">Čech</a> a <a title="Morava" href="http://cs.wikipedia.org/wiki/Morava">Moravy</a></li>\r\n<li>zpochybňov&aacute;n&iacute; <a title="Oběti nacismu z Československ&eacute; republiky" href="http://cs.wikipedia.org/wiki/Ob%C4%9Bti_nacismu_z_%C4%8Ceskoslovensk%C3%A9_republiky">počtu československ&yacute;ch obět&iacute; nacismu</a></li>\r\n<li>zpochybňov&aacute;n&iacute; <a title="Konečn&eacute; ře&scaron;en&iacute; česk&eacute; ot&aacute;zky" href="http://cs.wikipedia.org/wiki/Kone%C4%8Dn%C3%A9_%C5%99e%C5%A1en%C3%AD_%C4%8Desk%C3%A9_ot%C3%A1zky">německ&yacute;ch pl&aacute;nů na germanizaci česk&eacute;ho prostoru</a> a pov&aacute;lečn&eacute; vys&iacute;dlen&iacute; <a title="Če&scaron;i" href="http://cs.wikipedia.org/wiki/%C4%8Ce%C5%A1i">Čechů</a> ze sv&eacute; vlasti</li>\r\n<li>bagatelizov&aacute;n&iacute; teroru <a title="Němci" href="http://cs.wikipedia.org/wiki/N%C4%9Bmci">Němců</a> vůči <a title="Če&scaron;i" href="http://cs.wikipedia.org/wiki/%C4%8Ce%C5%A1i">Čechům</a>, kter&yacute; byl př&iacute;činou mnoh&yacute;ch pov&aacute;lečn&yacute;ch excesů Čechů vůči Němcům</li>\r\n</ul>\r\n<p>Sudetoněmeck&yacute; revizionismus doch&aacute;z&iacute; i do takov&eacute;ho krajn&iacute;ho ztv&aacute;rněn&iacute; historie, v n&iacute;ž probl&eacute;m Čechů a česk&yacute;ch Němců zač&iacute;n&aacute; <a title="9. květen" href="http://cs.wikipedia.org/wiki/9._kv%C4%9Bten">9. května</a> <a title="1945" href="http://cs.wikipedia.org/wiki/1945">1945</a>, bez ohledu na mnoh&eacute; v&yacute;znamn&eacute; ud&aacute;losti tomu předch&aacute;zej&iacute;c&iacute;. Sudetoněmeck&yacute; revizionismus zneuž&iacute;v&aacute; i skutečnosti, že někter&eacute; ud&aacute;losti československ&yacute;ch dějin byly v pov&aacute;lečn&yacute;ch 40 letech <a title="Komunistick&yacute; režim" href="http://cs.wikipedia.org/wiki/Komunistick%C3%BD_re%C5%BEim">komunistick&yacute;m režimem</a> tabuizov&aacute;ny a nejsou dodnes historicky prozkoum&aacute;ny. Tytu ud&aacute;losti b&yacute;vaj&iacute; z německ&eacute; strany ne&uacute;měrně zveličov&aacute;ny a překrucov&aacute;ny<sup id="cite_ref-42" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-42">[43]</a></sup>, doch&aacute;z&iacute; k manipulaci s č&iacute;sly, přičemž tyto &uacute;daje přeb&iacute;raj&iacute; i česk&aacute; m&eacute;dia, nemaj&iacute;c&iacute; oporu v seri&oacute;zn&iacute;m historick&eacute;m b&aacute;d&aacute;n&iacute;. Jako zcela jednoznačně prokazateln&yacute; př&iacute;klad sudetoněmeck&eacute;ho revizionismu je uv&aacute;děn&iacute; počtu obět&iacute; <a title="Vys&iacute;dlen&iacute; Němců z Československa" href="http://cs.wikipedia.org/wiki/Vys%C3%ADdlen%C3%AD_N%C4%9Bmc%C5%AF_z_%C4%8Ceskoslovenska">odsunu sudetsk&yacute;ch Němců</a>, kde se i společn&aacute; česko-německ&aacute; komise shodla na č&iacute;slu, pohybuj&iacute;c&iacute; se kolem 20 tis&iacute;c osob. <a title="Sudetoněmeck&eacute; krajansk&eacute; sdružen&iacute;" href="http://cs.wikipedia.org/wiki/Sudeton%C4%9Bmeck%C3%A9_krajansk%C3%A9_sdru%C5%BEen%C3%AD">Sudetoněmeck&eacute; krajansk&eacute; sdružen&iacute;</a> v&scaron;ak st&aacute;le nehor&aacute;zně uv&aacute;d&iacute; č&iacute;sla mezi 200 až 300 tis&iacute;c obět&iacute;.<sup id="cite_ref-43" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-43">[44]</a></sup>,<sup id="cite_ref-44" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-44">[45]</a></sup></p>\r\n<p>Se "sudetoněmeck&yacute;m revizionismem" souvis&iacute; <em>Odborn&eacute; vyj&aacute;dřen&iacute; z oboru politologie, historie, sociologie a lingvistiky</em><sup id="cite_ref-45" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-45">[46]</a></sup>,<sup id="cite_ref-46" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-46">[47]</a></sup> na publikaci <em>G. Kleining, J. Weikert: Sudet&scaron;t&iacute; Němci, etnick&aacute; čistka, vyhn&aacute;n&iacute;</em>, v němž autor soudn&iacute;ho posudku politolog <a class="new" title="Zdeněk Zbořil (str&aacute;nka neexistuje)" href="http://cs.wikipedia.org/w/index.php?title=Zden%C4%9Bk_Zbo%C5%99il&amp;action=edit&amp;redlink=1">Zdeněk Zbořil</a> zcela nepochopitelně sm&iacute;chal dohromady posudek na texty o sudetsk&yacute;ch Němc&iacute;ch s antisemitskou literaturou <em>Rudolf Seidl: Osvětim, fakta versus fikce, nov&eacute; a utajovan&eacute; poznatky o holocaustu</em>.</p>\r\n<h3><span id="Komunistick.C3.A1_le.C5.BE" class="mw-headline">Komunistick&aacute; lež</span></h3>\r\n<p>V lednu 2009 vydala o. p. s. <a title="Člověk v t&iacute;sni" href="http://cs.wikipedia.org/wiki/%C4%8Clov%C4%9Bk_v_t%C3%ADsni">Člověk v t&iacute;sni</a> prohl&aacute;&scaron;en&iacute;, že v&yacute;uka dějin o obdob&iacute; komunismu na &scaron;kol&aacute;ch je nedostatečn&aacute;<sup id="cite_ref-47" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-47">[48]</a></sup>, uspoř&aacute;dala tiskovou konferenci Jak učit o obdob&iacute; komunismu? <sup id="cite_ref-48" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-48">[49]</a></sup> a informačn&iacute; a vzděl&aacute;vac&iacute; projekt Př&iacute;běhy bezpr&aacute;v&iacute; komunistick&eacute;ho Československa<sup id="cite_ref-49" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-49">[50]</a></sup> Podobnou osvětovou činnost (např. <em>Letn&iacute; &scaron;kolu modern&iacute;ch dějin</em><sup id="cite_ref-50" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-50">[51]</a></sup>) vyv&iacute;j&iacute; i <a title="&Uacute;stav pro studium totalitn&iacute;ch režimů" href="http://cs.wikipedia.org/wiki/%C3%9Astav_pro_studium_totalitn%C3%ADch_re%C5%BEim%C5%AF">&Uacute;stav pro studium totalitn&iacute;ch režimů</a><sup id="cite_ref-51" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-51">[52]</a></sup>, <a title="Konfederace politick&yacute;ch vězňů Česk&eacute; republiky" href="http://cs.wikipedia.org/wiki/Konfederace_politick%C3%BDch_v%C4%9Bz%C5%88%C5%AF_%C4%8Cesk%C3%A9_republiky">Konfederace politick&yacute;ch vězňů ČR</a><sup id="cite_ref-52" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-52">[53]</a></sup>,<sup id="cite_ref-53" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-53">[54]</a></sup> nebo m&eacute;dia<sup id="cite_ref-54" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-54">[55]</a></sup>,<sup id="cite_ref-55" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-55">[56]</a></sup>, <sup id="cite_ref-56" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-56">[57]</a></sup>,<sup id="cite_ref-57" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-57">[58]</a></sup>,<sup id="cite_ref-58" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-58">[59]</a></sup>. T&eacute;ž někteř&iacute; učitel&eacute; dějepisu vyzvali sv&eacute; kolegy, aby učili o zločinech komunismu<sup id="cite_ref-59" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-59">[60]</a></sup>. V červnu 2009 vydala vzděl&aacute;vac&iacute; agentura Služba &scaron;kole MB z Mlad&eacute; Boleslavi s podporou nadace Open Society Fund z Prahy metodickou př&iacute;ručku pro v&yacute;uku modern&iacute;ch československ&yacute;ch a česk&yacute;ch dějin po 2. světov&eacute; v&aacute;lce pro obdob&iacute; let 1945 &ndash; 1954 <strong>Přes pr&aacute;h totality</strong>.<sup id="cite_ref-60" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-60">[61]</a></sup>,<sup id="cite_ref-61" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-61">[62]</a></sup>,<sup id="cite_ref-62" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-62">[63]</a></sup>,<sup id="cite_ref-63" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-63">[64]</a></sup>. Publikaci pro učitele dějepisu <em>Nacistick&aacute; perzekuce obyvatel česk&yacute;ch zem&iacute;</em> o průběhu a v&yacute;sledc&iacute;ch nacistick&eacute; perzekuce na &uacute;zem&iacute; Česka<sup id="cite_ref-64" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-64">[65]</a></sup>. vydala tak&eacute; o. p. s. <em>Živ&aacute; paměť</em><sup id="cite_ref-65" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-65">[66]</a></sup>.</p>\r\n<p>V r&aacute;mci stategie boje proti extremismu označili <a class="mw-redirect" title="Vl&aacute;da ČR" href="http://cs.wikipedia.org/wiki/Vl%C3%A1da_%C4%8CR">vl&aacute;dn&iacute;</a> experti osvětu - vzděl&aacute;v&aacute;n&iacute; jak dět&iacute; ve &scaron;kol&aacute;ch, tak samotn&yacute;ch pedagogů, ale i policistů a dal&scaron;&iacute;ch, kteř&iacute; jsou souč&aacute;st&iacute; bezpečnostn&iacute;ch složek, za nejv&iacute;ce podstatnou obranu proti extremistům. Do konce roku 2009 m&aacute; ministerstvo &scaron;kolstv&iacute; vypracovat jednotliv&eacute; metodick&eacute; př&iacute;ručky a pokyny jak pro nakladatele, autory učebnic, učitele, ale i &scaron;irokou veřejnost <sup id="cite_ref-66" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-66">[67]</a></sup>.</p>\r\n<p>Komunistick&aacute; strana okamžitě protestovala proti "propagandistick&eacute;mu působen&iacute; organizac&iacute; typu Člověk v t&iacute;sni" na &scaron;kol&aacute;ch<sup id="cite_ref-67" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-67">[68]</a></sup>. Učitelka Marta Semelov&aacute;, předsedkyně Odborn&eacute; sekce &scaron;kolstv&iacute; &Uacute;V <a class="mw-redirect" title="KSČM" href="http://cs.wikipedia.org/wiki/KS%C4%8CM">KSČM</a>, vyzvala v dubnu 2009 k <em>aktivn&iacute;mu postoji veřejnosti proti antikomunismu, neonacismu a rasismu</em><sup id="cite_ref-68" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-68">[69]</a></sup>, ve kter&eacute; mj. &bdquo;protestuje proti &uacute;čelov&yacute;m z&aacute;sahům politick&yacute;ch subjektů do vzděl&aacute;v&aacute;n&iacute; na&scaron;ich dět&iacute; a ml&aacute;deže, kter&eacute; se na z&aacute;kladě polopravd a lž&iacute; snaž&iacute; ovlivnit novodob&yacute; v&yacute;klad historie&ldquo;. KSČM odm&iacute;t&aacute; "jednostrannost česk&yacute;ch učebnic dějepisu a občansk&eacute; nauky v ot&aacute;zce hodnocen&iacute; let 1948-89". Podle Semelov&eacute; by "bylo objektivn&iacute;, když s ž&aacute;ky mluv&iacute; reprezentanti Konfederace politick&yacute;ch vězňů, aby si ž&aacute;ci vyslechli i exponenty komunistick&eacute;ho režimu"<sup id="cite_ref-69" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-69">[70]</a></sup>. Komunist&eacute; si stěžuj&iacute;, že antikomunistick&aacute; "lživ&aacute; propaganda" ve &scaron;kol&aacute;ch je financov&aacute;na z grantů EU<sup id="cite_ref-70" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-70">[71]</a></sup> a že tyto aktivity propaguj&iacute; antikomunist&eacute; <a title="Jan &Scaron;in&aacute;gl" href="http://cs.wikipedia.org/wiki/Jan_%C5%A0in%C3%A1gl">Jan &Scaron;in&aacute;gl</a> a <a title="Jarom&iacute;r &Scaron;tětina" href="http://cs.wikipedia.org/wiki/Jarom%C3%ADr_%C5%A0t%C4%9Btina">Jarom&iacute;r &Scaron;tětina</a><sup id="cite_ref-71" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-71">[72]</a></sup>.</p>\r\n<p>Proti komunistick&eacute; v&yacute;zvě se postavila řada koment&aacute;torů<sup id="cite_ref-72" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-72">[73]</a></sup>,<sup id="cite_ref-73" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-73">[74]</a></sup>,<sup id="cite_ref-74" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-74">[75]</a></sup>,<sup id="cite_ref-75" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-75">[76]</a></sup>,<sup id="cite_ref-76" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-76">[77]</a></sup>, kteř&iacute; komunistickou v&yacute;yvu označili za <em>komunistickou lež</em>. Na prob&iacute;haj&iacute;c&iacute; spor reagovali učitel&eacute; dějepisu<sup id="cite_ref-77" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-77">[78]</a></sup> i historiografov&eacute;<sup id="cite_ref-78" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-78">[79]</a></sup>.</p>\r\n<p>Veden&iacute; KSČM v červenci 2009 sv&yacute;m kandid&aacute;tům do voleb do Poslaneck&eacute; sněmovny Parlamentu ČR v ř&iacute;jnu 2009 napsalo jakousi "kuchařku", jak maj&iacute; při m&iacute;tinc&iacute;ch s voliči odpov&iacute;dat na nejrůzněj&scaron;&iacute; politick&eacute; ot&aacute;zky, mimo jin&eacute; i na ty spjat&eacute; s komunistickou minulost&iacute;. KSČM rad&iacute; sv&yacute;m kandid&aacute;tům, jak maj&iacute; odpov&iacute;dat voličům na ot&aacute;zky spjat&eacute; s komunistickou minulost&iacute;, aby z toho nebyl skand&aacute;l. Např. poprava Milady Hor&aacute;kov&eacute; byla "selh&aacute;n&iacute;m moci" a <a title="Diktatura proletari&aacute;tu" href="http://cs.wikipedia.org/wiki/Diktatura_proletari%C3%A1tu">diktatura proletari&aacute;tu</a> "chybou, protože spr&aacute;vn&yacute; komunista n&aacute;sil&iacute; rozhodně odm&iacute;t&aacute;"<sup id="cite_ref-79" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-79">[80]</a></sup>,<sup id="cite_ref-80" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-80">[81]</a></sup>.</p>\r\n<p>Uk&aacute;zkou metod komunistick&eacute; revize dějin může b&yacute;t vymaz&aacute;v&aacute;n&iacute;m nepohodln&yacute;ch osob z fotek <a title="Fotomanipulace" href="http://cs.wikipedia.org/wiki/Fotomanipulace">retu&scaron;ov&aacute;n&iacute;m</a><sup id="cite_ref-81" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-81">[82]</a></sup> nebo nahrazov&aacute;n&iacute;m nepohodln&yacute;ch osob v encyklopedi&iacute;ch jin&yacute;mi hesly<sup id="cite_ref-82" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-82">[83]</a></sup>.</p>\r\n<p>V červenci 2009 přimělo b&yacute;val&eacute;ho disidenta <a title="Stanislav Penc" href="http://cs.wikipedia.org/wiki/Stanislav_Penc">Stanislava Pence</a> k vyvě&scaron;en&iacute; poč&iacute;tačov&yacute;ch registrů EZO (evidence z&aacute;jmov&yacute;ch osob) a SEZO (sjednocen&aacute; evidence z&aacute;jmov&yacute;ch osob)<sup id="cite_ref-83" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-83">[84]</a></sup> někdej&scaron;&iacute; <a title="St&aacute;tn&iacute; bezpečnost" href="http://cs.wikipedia.org/wiki/St%C3%A1tn%C3%AD_bezpe%C4%8Dnost">St&aacute;tn&iacute; bezpečnosti</a> (StB) na internet chov&aacute;n&iacute; <a title="&Uacute;stav pro studium totalitn&iacute;ch režimů" href="http://cs.wikipedia.org/wiki/%C3%9Astav_pro_studium_totalitn%C3%ADch_re%C5%BEim%C5%AF">&Uacute;stavu pro studium totalitn&iacute;ch režimů</a> (&Uacute;STR) a jemu podř&iacute;zen&eacute;ho Archivu bezpečnostn&iacute;ch složek (ABS). Penc obviňuje &Uacute;STR a ABS z toho, že si monopolizuj&iacute; pohled na historii, zatajuj&iacute; informace a ke zveřejněn&iacute; vyb&iacute;raj&iacute; medi&aacute;lně vděčn&eacute; kauzy<sup id="cite_ref-84" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-84">[85]</a></sup>, např. spisovatele <a title="Milan Kundera" href="http://cs.wikipedia.org/wiki/Milan_Kundera">Milana Kunderu</a><sup id="cite_ref-85" class="reference"><a href="http://cs.wikipedia.org/wiki/Revizionismus#cite_note-85">[86]</a></sup>.</p>', '<strong>Revizionismus</strong> (z <a title="Latina" href="http://cs.wikipedia.org/wiki/Latina">lat.</a> <em>re-videre</em>, <em>re-visus</em>, znovu prohl&eacute;dnout, ověřit) je soustavn&eacute; &uacute;sil&iacute; o přehodnocen&iacute;, změny př&iacute;padně &uacute;pravy dan&eacute;ho (st&aacute;vaj&iacute;c&iacute;ho) stavu', 1257412973, '', '', 'eng - <strong>Revizionismus</strong> (z <a title="Latina" href="http://cs.wikipedia.org/wiki/Latina">lat.</a> <em>re-videre</em>, <em>re-visus</em>, znovu prohl&eacute;dnout, ověřit) je soustavn&eacute; &uacute;sil&iacute; o přehodnocen&iacute;, změny př&iacute;padně &uacute;pravy dan&eacute;ho (st&aacute;vaj&iacute;c&iacute;ho) stavu', '', '', ''),
(16, 63, 'NULL', 'Česky', 'It is important to specify the ''thumb'' class for the link that should serve as the thumbnail and the ''caption'' class for the element that should serve as the caption. When an image is selected for display in the slideshow, any elements with the ''caption'' class will be rendered within the specified caption container element above.', 'Česky', 1257412965, 'Anglicky', '', 'Anglicky', 'Německy', '', 'Německy'),
(17, 65, 'NULL', NULL, 'A generic iterator function, which can be used to seamlessly iterate over both objects and arrays. Arrays and array-like objects with a length property (such as a function''s arguments object) are iterated by numeric index, from 0 to length-1. Other objects are iterated via their named properties.', NULL, 1261156950, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_userfiles`
--

DROP TABLE IF EXISTS `vypecky_userfiles`;
CREATE TABLE IF NOT EXISTS `vypecky_userfiles` (
  `id_file` smallint(6) NOT NULL AUTO_INCREMENT,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL DEFAULT '1',
  `file` varchar(50) NOT NULL,
  `type` enum('file','image','flash') NOT NULL DEFAULT 'file',
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_file`),
  KEY `id_category` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

--
-- Vypisuji data pro tabulku `vypecky_userfiles`
--

INSERT INTO `vypecky_userfiles` (`id_file`, `id_item`, `id_article`, `id_user`, `file`, `type`, `width`, `height`, `size`, `time`) VALUES
(65, 1, 1, 1, '00703.jpg', 'image', 700, 480, 68306, 1244911573),
(46, 1, 1, 1, 'recoverdatareiserfstrial.tar.gz', 'file', NULL, NULL, 4346556, 1238771976),
(48, 7, 1, 1, 'anree1.jpg', 'image', 1200, 1600, 674756, 1239023394),
(49, 7, 1, 1, 'anree2.jpg', 'image', 1200, 1600, 674756, 1239023578),
(50, 7, 1, 1, 'teo.jpg', 'image', 46, 58, 1320, 1239023663),
(51, 7, 1, 1, 'buttony.swf', 'flash', 120, 120, 2981, 1239029679),
(53, 10, 4, 1, 'budova-milenium-center-s-parkovacim-domem.jpg', 'image', 2048, 1536, 1038239, 1239874962),
(55, 2, 2, 1, 'icon-naming-utils-0.8.90.tar.gz', 'file', 0, 0, 70321, 1244630753),
(56, 2, 2, 1, 'tango-feet.png', 'image', 729, 783, 256459, 1244631590),
(58, 7, 7, 1, '0070.jpg', 'image', 700, 480, 68306, 1244644445),
(59, 7, 7, 1, '1560.jpg', 'image', 414, 414, 74995, 1244645022),
(61, 7, 8, 1, '00701.jpg', 'image', 700, 480, 68306, 1244645753),
(64, 7, 9, 1, 'fotka-novinky.jpg_[eh7874].jpeg', 'image', 241, 58, 13181, 1244795678),
(66, 7, 10, 1, 'art.protest.afp.gi.jpg', 'image', 292, 219, 23057, 1244915033);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Vypisuji data pro tabulku `vypecky_users`
--

INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', '', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0),
(26, 'test', '6e017b5464f820a6c1bb5e9f6d711a667a80d8ea', 4, 'Testovací', 'Uživatel', 'jakubmatas@vypecky.info', 'Only for testing !!!', 0, NULL, 0);

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `vypecky_cinemaprogram_time`
--
ALTER TABLE `vypecky_cinemaprogram_time`
  ADD CONSTRAINT `vypecky_cinemaprogram_time_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `vypecky_cinemaprogram_movies` (`id_movie`);
