-- LABEL: Základní data s páru kategoriemi V1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}autorun`
--

TRUNCATE TABLE `{PREFIX}autorun`;
--
-- Vypisuji data pro tabulku `{PREFIX}autorun`
--

INSERT INTO `{PREFIX}autorun` (`id_autorun`, `autorun_module_name`, `autorun_period`, `autorun_url`) VALUES
(1, 'services', 'weekly', NULL),
(2, 'mailsnewsletters', 'hourly', NULL);

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}categories`
--

TRUNCATE TABLE `{PREFIX}categories`;
--
-- Vypisuji data pro tabulku `{PREFIX}categories`
--

INSERT INTO `{PREFIX}categories` (`id_category`, `module`, `data_dir`, `urlkey_cs`, `disable_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `disable_en`, `label_en`, `alt_en`, `urlkey_de`, `disable_de`, `label_de`, `alt_de`, `urlkey_sk`, `disable_sk`, `label_sk`, `alt_sk`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `keywords_sk`, `description_sk`, `ser_params`, `params`, `protected`, `priority`, `active`, `individual_panels`, `sitemap_changefreq`, `sitemap_priority`, `visibility`, `changed`, `default_right`, `feeds`, `icon`, `background`, `id_owner_user`, `allow_handle_access`) VALUES
(1, 'login', 'ucet', 'ucet', 0, 'účet', NULL, 'account', 0, 'account', NULL, NULL, 0, NULL, NULL, 'ucet', 0, 'účet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'never', 0, 5, '2014-12-10 09:50:04', 'r--', 0, NULL, NULL, 0, 0),
(2, 'text', 'o-nas', 'o-nas', 0, 'O nás', '', NULL, 0, '', '', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'yearly', 0, 1, '2014-12-09 19:08:17', 'r--', 0, NULL, NULL, 0, 0),
(3, 'photogalery', 'fotografie', 'fotografie', 0, 'Fotografie', '', NULL, 0, '', '', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 0, 'yearly', 0, 1, '2014-12-09 19:08:32', 'r--', 0, NULL, NULL, 0, 0),
(4, 'contact', 'kontakt', 'kontakt', 0, 'Kontakt', '', NULL, 0, '', '', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '', '', '', '', NULL, NULL, NULL, NULL, 'a:4:{s:3:"map";b:0;s:7:"maptype";s:5:"image";s:4:"form";b:0;s:10:"shareTools";b:1;}', NULL, 0, 0, 1, 0, 'yearly', 0, 1, '2014-12-09 19:08:48', 'r--', 0, NULL, NULL, 0, 0);

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}config`
--

TRUNCATE TABLE `{PREFIX}config`;
--
-- Vypisuji data pro tabulku `{PREFIX}config`
--

INSERT INTO `{PREFIX}config` (`id_config`, `key`, `label`, `value`, `values`, `protected`, `type`, `id_group`, `callback_func`, `hidden_value`) VALUES
(1, 'CATEGORIES_STRUCTURE', NULL, 'O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:0;s:22:"\0Category_Structure\0id";i:0;s:28:"\0Category_Structure\0idParent";N;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:4:{i:0;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"1";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:1;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"2";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:2;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"3";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}i:3;O:18:"Category_Structure":5:{s:25:"\0Category_Structure\0level";i:1;s:22:"\0Category_Structure\0id";s:1:"4";s:28:"\0Category_Structure\0idParent";i:0;s:26:"\0Category_Structure\0catObj";N;s:29:"\0Category_Structure\0childrens";a:0:{}}}}', NULL, 1, 'ser_object', 1, NULL, 0),
(3, 'VERSION', 'Verze jádra', '8.2.2', NULL, 1, 'string', 1, NULL, 0),
(4, 'WEB_NAME', 'Název stránek', 'pokusv2', NULL, 0, 'string', 2, NULL, 0),
(6, 'MAIN_PAGE_TITLE', 'Nadpis hlavní stránky', 'pokusv2', NULL, 0, 'string', 2, NULL, 0),
(7, 'TEMPLATE_FACE', 'Název vzhledu stránek', 'bootstrap', NULL, 0, 'string', 4, NULL, 0),
(5, 'FCB_ACCESS_TOKEN', 'Access token pro přístup k Facebooku', NULL, NULL, 0, 'string', 11, NULL, 1);

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}groups`
--

TRUNCATE TABLE `{PREFIX}groups`;
--
-- Vypisuji data pro tabulku `{PREFIX}groups`
--

INSERT INTO `{PREFIX}groups` (`id_group`, `name`, `label`, `used`, `default_right`, `admin`) VALUES
(1, 'admin', 'Administrátor', 1, 'rwc', 1),
(2, 'guest', 'Host', 1, 'r--', 0);

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}modules_instaled`
--

TRUNCATE TABLE `{PREFIX}modules_instaled`;
--
-- Vypisuji data pro tabulku `{PREFIX}modules_instaled`
--

INSERT INTO `{PREFIX}modules_instaled` (`id_module`, `name`, `version_major`, `version_minor`, `version`) VALUES
(1, 'text', 1, 4, '1.4.0'),
(2, 'upgrade', 1, 1, '1.1.0'),
(3, 'mails', 4, 0, '4.0.0'),
(4, 'search', 1, 0, '1.0.0'),
(5, 'users', 2, 0, '2.0.0'),
(6, 'panels', 1, 1, '1.1.0'),
(7, 'empty', 1, 0, '1.0.0'),
(8, 'services', 2, 0, '2.0.0'),
(9, 'configuration', 3, 0, '3.0.0'),
(10, 'templates', 1, 1, '1.1.0'),
(11, 'phpinfo', 1, 0, '1.0.0'),
(12, 'categories', 2, 0, '2.0.0'),
(13, 'login', 1, 0, '1.0.0'),
(14, 'quicktools', 1, 0, '1.0.0'),
(15, 'forms', 1, 0, '1.0.0'),
(16, 'banners', 1, 0, '1.0.0'),
(17, 'custommenu', 1, 0, '1.0.0'),
(18, 'mailsaddressbook', 1, 0, '1.0.0'),
(19, 'mailsnewsletters', 1, 1, '1.1.0'),
(20, 'hpslideshow', 1, 0, '1.1.0'),
(21, 'catsbulkedit', 1, 0, '1.0.0'),
(22, 'crontab', 1, 0, '1.0.0'),
(23, 'trstaticstexts', 1, 0, '1.0.0'),
(24, 'redirect', 1, 0, '1.0.0'),
(25, 'adminsites', 1, 0, '1.0.0'),
(26, 'photogalery', 1, 0, '1.2.0'),
(27, 'contact', 1, 0, '1.1.0');

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}photogalery_images`
--

TRUNCATE TABLE `{PREFIX}photogalery_images`;
--
-- Vypisuji data pro tabulku `{PREFIX}photogalery_images`
--

INSERT INTO `{PREFIX}photogalery_images` (`id_photo`, `id_article`, `id_category`, `file`, `name_cs`, `desc_cs`, `name_sk`, `desc_sk`, `name_en`, `desc_en`, `name_de`, `desc_de`, `ord`, `edit_time`) VALUES
(2, 3, 3, 'BrychtaJan_CCT2012_120630_122324_DSC_7185.jpg', 'BrychtaJan_CCT2012_120630_122324_DSC_7185.jpg', NULL, NULL, NULL, 'BrychtaJan_CCT2012_120630_122324_DSC_7185.jpg', NULL, NULL, NULL, 1, '2014-12-10 09:51:58'),
(3, 3, 3, 'BrychtaJan_CCT2012_120630_122229_DSC_8046.jpg', 'BrychtaJan_CCT2012_120630_122229_DSC_8046.jpg', NULL, NULL, NULL, 'BrychtaJan_CCT2012_120630_122229_DSC_8046.jpg', NULL, NULL, NULL, 2, '2014-12-10 09:51:58'),
(4, 3, 3, 'BrychtaJan_CCT2012_120630_122527_DSC_8057.jpg', 'BrychtaJan_CCT2012_120630_122527_DSC_8057.jpg', NULL, NULL, NULL, 'BrychtaJan_CCT2012_120630_122527_DSC_8057.jpg', NULL, NULL, NULL, 3, '2014-12-10 09:51:58');

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}rights`
--

TRUNCATE TABLE `{PREFIX}rights`;
--
-- Vypisuji data pro tabulku `{PREFIX}rights`
--

INSERT INTO `{PREFIX}rights` (`id_right`, `id_category`, `id_group`, `right`) VALUES
(1, 1, 1, 'rwc'),
(2, 1, 2, 'r--');

--
-- Vyprázdnit tabulku před vkládáním `{PREFIX}texts`
--

TRUNCATE TABLE `{PREFIX}texts`;
--
-- Vypisuji data pro tabulku `{PREFIX}texts`
--

INSERT INTO `{PREFIX}texts` (`id_text`, `id_item`, `id_user`, `subkey`, `changed`, `label_cs`, `text_cs`, `text_clear_cs`, `label_en`, `text_en`, `text_clear_en`, `label_de`, `text_de`, `text_clear_de`, `label_sk`, `text_sk`, `text_clear_sk`, `data`) VALUES
(1, 2, 1, 'main', '2014-12-10 09:51:31', '', '<p>The UN and human rights groups have called for the prosecution of US officials involved in what a Senate report called the "brutal" CIA interrogation of al-Qaeda suspects.</p>\r\n<p>A top UN human rights envoy said there had been a "clear policy orchestrated at a high level".</p>\r\n<p>The CIA has defended its actions in the years after the 9/11 attacks on the US, saying they saved lives.</p>\r\n<p>President Barack Obama said it was now time to move on.</p>', 'The UN and human rights groups have called for the prosecution of US officials involved in what a Senate report called the "brutal" CIA interrogation of al-Qaeda suspects.\r\nA top UN human rights envoy said there had been a "clear policy orchestrated at a high level".\r\nThe CIA has defended its actions in the years after the 9/11 attacks on the US, saying they saved lives.\r\nPresident Barack Obama said it was now time to move on.', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 4, 0, 'main', '2014-12-10 09:52:37', NULL, '<p>Za potůčky 25<br />Valašské Meziříčí 757 01</p>\r\n<p>Tel.: 123 456 789</p>\r\n<p>Email: pokus@hemtam.cz</p>', 'Za potůčky 25Valašské Meziříčí 757 01\r\nTel.: 123 456 789\r\nEmail: pokus@hemtam.cz', NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

