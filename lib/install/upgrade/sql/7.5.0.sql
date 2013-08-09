/* UPDATE_MAIN_SITE */
--
-- Struktura tabulky `vypecky_config_groups`
--

CREATE TABLE IF NOT EXISTS `cubecms_global_config_groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name_cs` varchar(45) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `name_sk` varchar(45) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `name_en` varchar(45) DEFAULT NULL,
  `name_de` varchar(45) DEFAULT NULL,
  `desc_cs` varchar(200) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `desc_sk` varchar(200) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT NULL,
  `desc_en` varchar(200) DEFAULT NULL,
  `desc_de` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_config_groups`
--

INSERT INTO `cubecms_global_config_groups` (`id_group`, `name_cs`, `name_sk`, `name_en`, `name_de`, `desc_cs`, `desc_sk`, `desc_en`, `desc_de`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Základní nastavení', 'Základné nastavenie', 'Basic settings', 'Grundeinstellungen', 'Základní nastavení aplikace', 'Základné nastavenia aplikácie', 'Basic settings', NULL),
(3, 'Pokročilá nastavení', 'Rozšírené nastavenia', 'Advanced settings', 'Erweiterte Einstellungen', 'Nastavení chování jádra (přihlášení, subdomény, atd.)', 'Nastavenie správania jadra (prihlásení, subdomény, atď)', 'Adjustment of the Kernel (login, subdomains, etc.)', NULL),
(4, 'Vzhled', 'Vzhľad', 'Appearance', 'Aussehen', 'Nastavení vzhledu stránek', 'Nastavenie vzhľadu stránok', 'Setting up of site', NULL),
(6, 'E-maily', 'E-maily', 'E-mails', 'E-Mails', 'Nastavení e-mailových služeb', 'Nastavenie e-mailových služieb', 'Setting up e-mail service', NULL),
(7, 'Obrázky', 'Obrázky', 'Images', 'Bilder', 'Nastavení obrázků (velikost)', 'Nastavenie obrázkov (veľkosť)', 'Picture settings (size)', NULL),
(8, 'Lokalizace a jazyky', 'Lokalizácia a jazyky', 'Localization and languages', 'Ortsbestimmung und Sprachen', 'Nastavení jazyků prostředí a lokalizace aplikace', 'Nastavenie jazykov prostredia a lokalizácia aplikácie', 'The language environment and positioning applications', NULL),
(9, 'Hledání', 'Hľadanie', 'Search', 'Suche', 'Nastavení výsledků hledání', 'Nastavenie výsledkov hľadania', 'Search Settings', NULL),
(5, 'Exporty', 'Exporty', 'Exports', 'Exporte', 'Nastavení exportů (rss, pdf, ...)', 'Nastavenie exportov (rss, pdf, ...)', 'Export Settings (RSS, PDF, ...)', NULL),
(10, 'E-Shop nastavení', NULL, NULL, NULL, 'Nastavení elektronického obchodu. Toto nastavení je lépe upravovat přímo v nastavení obchodu.', NULL, NULL, NULL);


INSERT INTO `cubecms_global_config` (`key`, `label`, `value`, `protected`, `type`, `id_group`) VALUES
('ARTICLE_TITLE_IMG_W', 'Titulní obrázek článku - šířka', 100, false, 'number', 7),
('ARTICLE_TITLE_IMG_H', 'Titulní obrázek článku - výška', 100, false, 'number', 7),
('ARTICLE_TITLE_IMG_DIR', 'Titulní obrázek článku - adresář', 'title-images', false, 'text', 7);
/* END_UPDATE */