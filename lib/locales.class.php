<?php

/**
 * Třída pro práci s locale (místním nastavením).
 * Třída slouží pro práci s jazykovým nastavením aplikace. Je určena k volbě
 * výchozího a zvoleného jazyka aplikace. Lze s ní ískat i kompletní výpis všech
 * jazyků, a všech použiých jazyků v aplikaci.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author			$Author$ $Date$
 * 						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu jazykového nastavení
 * @internal      Last ErrorCode 2
 */
class Locales extends TrObject {

   /**
    * Oddělovač jazyků v konfiguračním souboru
    * @var string
    */
   const LANG_SEPARATOR = ';';

   /**
    * Název Session s jazykem
    * @var string
    */
   const SESSION_LANG = 'lang';

   /**
    * Název adresáře s locales
    * @var string
    */
   const LOCALES_DIR = 'locale';

   /**
    * výchozí doména pro gettext
    * @var string
    */
   const GETTEXT_DEFAULT_DOMAIN = 'messages';

   /**
    * Gettext engine locales dir
    * @var string
    */
   const GETTEXT_DEFAULT_LOCALES_DIR = 'locale';

   /**
    * Doména pro gettext a moduly
    * @var string
    */
   const GETTEXT_MDOMAIN = 'module_messages';

   /**
    * Pole se všemi locales
    * @var array
    * @todo připojit krátké názvy
    */
   protected static $locales = array(
       'aa_DJ' =>
       array(
           'name' => 'Afar (Djibouti)',
           'locale' => 'aa_DJ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'aa_ER' =>
       array(
           'name' => 'Afar (Eritrea)',
           'locale' => 'aa_ER.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'aa_ET' =>
       array(
           'name' => 'Afar (Ethiopia)',
           'locale' => 'aa_ET.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'af_ZA' =>
       array(
           'name' => 'Afrikaans (South Africa)',
           'locale' => 'af_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'am_ET' =>
       array(
           'name' => 'Amharic (Ethiopia)',
           'locale' => 'am_ET.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'an_ES' =>
       array(
           'name' => 'Aragonese (Spain)',
           'locale' => 'an_ES.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_AE' =>
       array(
           'name' => 'Arabic (United Arab Emirates)',
           'locale' => 'ar_AE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_BH' =>
       array(
           'name' => 'Arabic (Bahrain)',
           'locale' => 'ar_BH.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_DZ' =>
       array(
           'name' => 'Arabic (Algeria)',
           'locale' => 'ar_DZ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_EG' =>
       array(
           'name' => 'Arabic (Egypt)',
           'locale' => 'ar_EG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_IN' =>
       array(
           'name' => 'Arabic (India)',
           'locale' => 'ar_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_IQ' =>
       array(
           'name' => 'Arabic (Iraq)',
           'locale' => 'ar_IQ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_JO' =>
       array(
           'name' => 'Arabic (Jordan)',
           'locale' => 'ar_JO.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_KW' =>
       array(
           'name' => 'Arabic (Kuwait)',
           'locale' => 'ar_KW.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_LB' =>
       array(
           'name' => 'Arabic (Lebanon)',
           'locale' => 'ar_LB.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_LY' =>
       array(
           'name' => 'Arabic (Libya)',
           'locale' => 'ar_LY.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_MA' =>
       array(
           'name' => 'Arabic (Morocco)',
           'locale' => 'ar_MA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_OM' =>
       array(
           'name' => 'Arabic (Oman)',
           'locale' => 'ar_OM.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_QA' =>
       array(
           'name' => 'Arabic (Qatar)',
           'locale' => 'ar_QA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_SA' =>
       array(
           'name' => 'Arabic (Saudi Arabia)',
           'locale' => 'ar_SA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_SD' =>
       array(
           'name' => 'Arabic (Sudan)',
           'locale' => 'ar_SD.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_SY' =>
       array(
           'name' => 'Arabic (Syria)',
           'locale' => 'ar_SY.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_TN' =>
       array(
           'name' => 'Arabic (Tunisia)',
           'locale' => 'ar_TN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ar_YE' =>
       array(
           'name' => 'Arabic (Yemen)',
           'locale' => 'ar_YE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'as' =>
       array(
           'name' => 'Assamese (India)',
           'locale' => 'as_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
//       'ast_ES' =>
//       array(
//           'name' => 'Asturian (Spain)',
//           'locale' => 'ast_ES.UTF-8',
//       ),
       'az_AZ' =>
       array(
           'name' => 'Azerbaijani (Azerbaijan)',
           'locale' => 'az_AZ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'az_TR' =>
       array(
           'name' => 'Azerbaijani (Turkey)',
           'locale' => 'az_TR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'be' =>
       array(
           'name' => 'Belarusian (Belarus)',
           'locale' => 'be_BY.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'bg' =>
       array(
           'name' => 'Bulgarian (Bulgaria)',
           'locale' => 'bg_BG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'bn' =>
       array(
           'name' => 'Bengali (Bangladesh) - default',
           'locale' => 'bn_BD.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'bn_IN' =>
       array(
           'name' => 'Bengali (India)',
           'locale' => 'bn_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'bo' =>
       array(
           'name' => 'Tibetan (China) - default',
           'locale' => 'bo_CN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'bo_IN' =>
       array(
           'name' => 'Tibetan (India)',
           'locale' => 'bo_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'br' =>
       array(
           'name' => 'Breton (France)',
           'locale' => 'br_FR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'bs' =>
       array(
           'name' => 'Bosnian (Bosnia and Herzegovina)',
           'locale' => 'bs_BA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ca_AD' =>
       array(
           'name' => 'Catalan (Andorra)',
           'locale' => 'ca_AD.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ca' =>
       array(
           'name' => 'Catalan (Spain) - default',
           'locale' => 'ca_ES.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ca_FR' =>
       array(
           'name' => 'Catalan (France)',
           'locale' => 'ca_FR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ca_IT' =>
       array(
           'name' => 'Catalan (Italy)',
           'locale' => 'ca_IT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
//       'crh_UA' =>
//       array(
//           'name' => 'Crimean Turkish (Ukraine)',
//           'locale' => 'crh_UA.UTF-8',
//       ),
       'cs' =>
       array(
           'name' => 'Czech (Czech Republic)',
           'label' => 'Česky',
           'locale' => 'cs_CZ.UTF-8',
           'collation' => array('mysql' => 'utf8_czech_ci'),
       ),
//       'csb_PL' =>
//       array(
//           'name' => 'Kashubian (Poland)',
//           'locale' => 'csb_PL.UTF-8',
//       ),
       'cv' =>
       array(
           'name' => 'Chuvash (Russia)',
           'locale' => 'cv_RU.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'cy' =>
       array(
           'name' => 'Welsh (United Kingdom)',
           'locale' => 'cy_GB.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'da' =>
       array(
           'name' => 'Danish (Denmark)',
           'locale' => 'da_DK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'de_AT' =>
       array(
           'name' => 'German (Austria)',
           'locale' => 'de_AT.UTF-8',
           'collation' => array('mysql' => 'utf8_german2_ci'),
       ),
       'de_BE' =>
       array(
           'name' => 'German (Belgium)',
           'locale' => 'de_BE.UTF-8',
           'collation' => array('mysql' => 'utf8_german2_ci'),
       ),
       'de_CH' =>
       array(
           'name' => 'German (Switzerland)',
           'locale' => 'de_CH.UTF-8',
           'collation' => array('mysql' => 'utf8_german2_ci'),
       ),
       'de' =>
       array(
           'name' => 'German (Germany) - default',
           'locale' => 'de_DE.UTF-8',
           'collation' => array('mysql' => 'utf8_german2_ci'),
       ),
       'de_LI' =>
       array(
           'name' => 'German (Liechtenstein)',
           'locale' => 'de_LI.UTF-8',
           'collation' => array('mysql' => 'utf8_german2_ci'),
       ),
       'de_LU' =>
       array(
           'name' => 'German (Luxembourg)',
           'locale' => 'de_LU.UTF-8',
           'collation' => array('mysql' => 'utf8_german2_ci'),
       ),
       'dv' =>
       array(
           'name' => 'Divehi (Maldives)',
           'locale' => 'dv_MV.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'dz' =>
       array(
           'name' => 'Dzongkha (Bhutan)',
           'locale' => 'dz_BT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'el_CY' =>
       array(
           'name' => 'Greek (Cyprus)',
           'locale' => 'el_CY.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'el' =>
       array(
           'name' => 'Greek (Greece) - default',
           'locale' => 'el_GR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_AG' =>
       array(
           'name' => 'English (Antigua and Barbuda)',
           'locale' => 'en_AG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_AU' =>
       array(
           'name' => 'English (Australia)',
           'locale' => 'en_AU.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_BW' =>
       array(
           'name' => 'English (Botswana)',
           'locale' => 'en_BW.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_CA' =>
       array(
           'name' => 'English (Canada)',
           'locale' => 'en_CA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_DK' =>
       array(
           'name' => 'English (Denmark)',
           'locale' => 'en_DK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en' =>
       array(
           'name' => 'English',
           'label' => 'English - default',
           'locale' => 'en_GB.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_HK' =>
       array(
           'name' => 'English (Hong Kong SAR China)',
           'locale' => 'en_HK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_IE' =>
       array(
           'name' => 'English (Ireland)',
           'locale' => 'en_IE.UTF-8',
       ),
       'en_IN' =>
       array(
           'name' => 'English (India)',
           'locale' => 'en_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_NG' =>
       array(
           'name' => 'English (Nigeria)',
           'locale' => 'en_NG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_NZ' =>
       array(
           'name' => 'English (New Zealand)',
           'locale' => 'en_NZ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_PH' =>
       array(
           'name' => 'English (Philippines)',
           'locale' => 'en_PH.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_SG' =>
       array(
           'name' => 'English (Singapore)',
           'locale' => 'en_SG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_US' =>
       array(
           'name' => 'English (United States)',
           'locale' => 'en_US.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_ZA' =>
       array(
           'name' => 'English (South Africa)',
           'locale' => 'en_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_ZM' =>
       array(
           'name' => 'English (Zambia)',
           'locale' => 'en_ZM.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'en_ZW' =>
       array(
           'name' => 'English (Zimbabwe)',
           'locale' => 'en_ZW.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'es' =>
       array(
           'name' => 'Spanish (Spain) - default',
           'locale' => 'es_ES.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_AR' =>
       array(
           'name' => 'Spanish (Argentina)',
           'locale' => 'es_AR.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_BO' =>
       array(
           'name' => 'Spanish (Bolivia)',
           'locale' => 'es_BO.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_CL' =>
       array(
           'name' => 'Spanish (Chile)',
           'locale' => 'es_CL.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_CO' =>
       array(
           'name' => 'Spanish (Colombia)',
           'locale' => 'es_CO.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_CR' =>
       array(
           'name' => 'Spanish (Costa Rica)',
           'locale' => 'es_CR.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_DO' =>
       array(
           'name' => 'Spanish (Dominican Republic)',
           'locale' => 'es_DO.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_EC' =>
       array(
           'name' => 'Spanish (Ecuador)',
           'locale' => 'es_EC.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_GT' =>
       array(
           'name' => 'Spanish (Guatemala)',
           'locale' => 'es_GT.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_HN' =>
       array(
           'name' => 'Spanish (Honduras)',
           'locale' => 'es_HN.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_MX' =>
       array(
           'name' => 'Spanish (Mexico)',
           'locale' => 'es_MX.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_NI' =>
       array(
           'name' => 'Spanish (Nicaragua)',
           'locale' => 'es_NI.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_PA' =>
       array(
           'name' => 'Spanish (Panama)',
           'locale' => 'es_PA.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_PE' =>
       array(
           'name' => 'Spanish (Peru)',
           'locale' => 'es_PE.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_PY' =>
       array(
           'name' => 'Spanish (Paraguay)',
           'locale' => 'es_PY.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'es_SV' =>
       array(
           'name' => 'Spanish (El Salvador)',
           'locale' => 'es_SV.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_US' =>
       array(
           'name' => 'Spanish (United States)',
           'locale' => 'es_US.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_UY' =>
       array(
           'name' => 'Spanish (Uruguay)',
           'locale' => 'es_UY.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'es_VE' =>
       array(
           'name' => 'Spanish (Venezuela)',
           'locale' => 'es_VE.UTF-8',
           'collation' => array('mysql' => 'utf8_spanish_ci'),
       ),
       'et_EE' =>
       array(
           'name' => 'Estonian (Estonia)',
           'locale' => 'et_EE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'eu_ES' =>
       array(
           'name' => 'Basque (Spain)',
           'locale' => 'eu_ES.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'eu_FR' =>
       array(
           'name' => 'Basque (France)',
           'locale' => 'eu_FR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fa' =>
       array(
           'name' => 'Persian (Iran)',
           'locale' => 'fa_IR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ff' =>
       array(
           'name' => 'Fulah (Senegal)',
           'locale' => 'ff_SN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fi' =>
       array(
           'name' => 'Finnish (Finland)',
           'locale' => 'fi_FI.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fo' =>
       array(
           'name' => 'Faroese (Faroe Islands)',
           'locale' => 'fo_FO.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fr_BE' =>
       array(
           'name' => 'French (Belgium)',
           'locale' => 'fr_BE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fr_CA' =>
       array(
           'name' => 'French (Canada)',
           'locale' => 'fr_CA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fr_CH' =>
       array(
           'name' => 'French (Switzerland)',
           'locale' => 'fr_CH.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fr' =>
       array(
           'name' => 'French (France) - default',
           'locale' => 'fr_FR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fr_LU' =>
       array(
           'name' => 'French (Luxembourg)',
           'locale' => 'fr_LU.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
//       'fur_IT' =>
//       array(
//           'name' => 'Friulian (Italy)',
//           'locale' => 'fur_IT.UTF-8',
//       ),
       'fy_DE' =>
       array(
           'name' => 'Western Frisian (Germany)',
           'locale' => 'fy_DE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'fy_NL' =>
       array(
           'name' => 'Western Frisian (Netherlands)',
           'locale' => 'fy_NL.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ga' =>
       array(
           'name' => 'Irish (Ireland)',
           'locale' => 'ga_IE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'gd' =>
       array(
           'name' => 'Scottish Gaelic (United Kingdom)',
           'locale' => 'gd_GB.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'gl' =>
       array(
           'name' => 'Galician (Spain)',
           'locale' => 'gl_ES.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'gu' =>
       array(
           'name' => 'Gujarati (India)',
           'locale' => 'gu_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'gv' =>
       array(
           'name' => 'Manx (United Kingdom)',
           'locale' => 'gv_GB.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ha' =>
       array(
           'name' => 'Hausa (Nigeria)',
           'locale' => 'ha_NG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'he' =>
       array(
           'name' => 'Hebrew (Israel)',
           'locale' => 'he_IL.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'hi' =>
       array(
           'name' => 'Hindi (India)',
           'locale' => 'hi_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'hr' =>
       array(
           'name' => 'Croatian (Croatia)',
           'locale' => 'hr_HR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ht' =>
       array(
           'name' => 'Haitian (Haiti)',
           'locale' => 'ht_HT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'hu' =>
       array(
           'name' => 'Hungarian (Hungary)',
           'locale' => 'hu_HU.UTF-8',
           'collation' => array('mysql' => 'utf8_hungarian_ci'),
       ),
       'hy' =>
       array(
           'name' => 'Armenian (Armenia)',
           'locale' => 'hy_AM.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'id' =>
       array(
           'name' => 'Indonesian (Indonesia)',
           'locale' => 'id_ID.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ig' =>
       array(
           'name' => 'Igbo (Nigeria)',
           'locale' => 'ig_NG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ik' =>
       array(
           'name' => 'Inupiaq (Canada)',
           'locale' => 'ik_CA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'is' =>
       array(
           'name' => 'Icelandic (Iceland)',
           'locale' => 'is_IS.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'it_CH' =>
       array(
           'name' => 'Italian (Switzerland)',
           'locale' => 'it_CH.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'it' =>
       array(
           'name' => 'Italian (Italy)',
           'locale' => 'it_IT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'iu' =>
       array(
           'name' => 'Inuktitut (Canada)',
           'locale' => 'iu_CA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'iw' =>
       array(
           'name' => 'Hebrew (Israel)',
           'locale' => 'iw_IL.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ja' =>
       array(
           'name' => 'Japanese (Japan)',
           'locale' => 'ja_JP.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ka' =>
       array(
           'name' => 'Georgian (Georgia)',
           'locale' => 'ka_GE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'kk' =>
       array(
           'name' => 'Kazakh (Kazakhstan)',
           'locale' => 'kk_KZ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'kl' =>
       array(
           'name' => 'Kalaallisut (Greenland)',
           'locale' => 'kl_GL.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'km' =>
       array(
           'name' => 'Khmer (Cambodia)',
           'locale' => 'km_KH.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'kn' =>
       array(
           'name' => 'Kannada (India)',
           'locale' => 'kn_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ko' =>
       array(
           'name' => 'Korean (South Korea)',
           'locale' => 'ko_KR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ks' =>
       array(
           'name' => 'Kashmiri (India)',
           'locale' => 'ks_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ku' =>
       array(
           'name' => 'Kurdish (Turkey)',
           'locale' => 'ku_TR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'kw' =>
       array(
           'name' => 'Cornish (United Kingdom)',
           'locale' => 'kw_GB.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ky' =>
       array(
           'name' => 'Kirghiz (Kyrgyzstan)',
           'locale' => 'ky_KG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'lg' =>
       array(
           'name' => 'Ganda (Uganda)',
           'locale' => 'lg_UG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'li_BE' =>
       array(
           'name' => 'Limburgish (Belgium)',
           'locale' => 'li_BE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'li_NL' =>
       array(
           'name' => 'Limburgish (Netherlands)',
           'locale' => 'li_NL.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'lo' =>
       array(
           'name' => 'Lao (Laos)',
           'locale' => 'lo_LA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'lt' =>
       array(
           'name' => 'Lithuanian (Lithuania)',
           'locale' => 'lt_LT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'lv' =>
       array(
           'name' => 'Latvian (Latvia)',
           'locale' => 'lv_LV.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'mg' =>
       array(
           'name' => 'Malagasy (Madagascar)',
           'locale' => 'mg_MG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'mi' =>
       array(
           'name' => 'Maori (New Zealand)',
           'locale' => 'mi_NZ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'mk' =>
       array(
           'name' => 'Macedonian (Macedonia)',
           'locale' => 'mk_MK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ml' =>
       array(
           'name' => 'Malayalam (India)',
           'locale' => 'ml_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'mn' =>
       array(
           'name' => 'Mongolian (Mongolia)',
           'locale' => 'mn_MN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'mr' =>
       array(
           'name' => 'Marathi (India)',
           'locale' => 'mr_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ms' =>
       array(
           'name' => 'Malay (Malaysia)',
           'locale' => 'ms_MY.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'mt' =>
       array(
           'name' => 'Maltese (Malta)',
           'locale' => 'mt_MT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'my' =>
       array(
           'name' => 'Burmese (Myanmar [Burma])',
           'locale' => 'my_MM.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'nb' =>
       array(
           'name' => 'Norwegian Bokmål (Norway)',
           'locale' => 'nb_NO.UTF-8',
           'collation' => array('mysql' => 'utf8_danich_ci'),
       ),
       'ne' =>
       array(
           'name' => 'Nepali (Nepal)',
           'locale' => 'ne_NP.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'nl_AW' =>
       array(
           'name' => 'Dutch (Aruba)',
           'locale' => 'nl_AW.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'nl_BE' =>
       array(
           'name' => 'Dutch (Belgium)',
           'locale' => 'nl_BE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'nl' =>
       array(
           'name' => 'Dutch (Netherlands)',
           'locale' => 'nl_NL.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'nn' =>
       array(
           'name' => 'Norwegian Nynorsk (Norway)',
           'locale' => 'nn_NO.UTF-8',
           'collation' => array('mysql' => 'utf8_danich_ci'),
       ),
       'nr' =>
       array(
           'name' => 'South Ndebele (South Africa)',
           'locale' => 'nr_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'oc' =>
       array(
           'name' => 'Occitan (France)',
           'locale' => 'oc_FR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'om_ET' =>
       array(
           'name' => 'Oromo (Ethiopia)',
           'locale' => 'om_ET.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'om_KE' =>
       array(
           'name' => 'Oromo (Kenya)',
           'locale' => 'om_KE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'or' =>
       array(
           'name' => 'Oriya (India)',
           'locale' => 'or_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'os' =>
       array(
           'name' => 'Ossetic (Russia)',
           'locale' => 'os_RU.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'pa' =>
       array(
           'name' => 'Punjabi (India)',
           'locale' => 'pa_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'pa_PK' =>
       array(
           'name' => 'Punjabi (Pakistan)',
           'locale' => 'pa_PK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'pl' =>
       array(
           'name' => 'Polish (Poland)',
           'locale' => 'pl_PL.UTF-8',
           'collation' => array('mysql' => 'utf8_polish_ci'),
       ),
       'ps' =>
       array(
           'name' => 'Pashto (Afghanistan)',
           'locale' => 'ps_AF.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'pt_BR' =>
       array(
           'name' => 'Portuguese (Brazil)',
           'locale' => 'pt_BR.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'pt' =>
       array(
           'name' => 'Portuguese (Portugal)',
           'locale' => 'pt_PT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ro' =>
       array(
           'name' => 'Romanian (Romania)',
           'locale' => 'ro_RO.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ru' =>
       array(
           'name' => 'Russian (Russia)',
           'locale' => 'ru_RU.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ru_UA' =>
       array(
           'name' => 'Russian (Ukraine)',
           'locale' => 'ru_UA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'rw' =>
       array(
           'name' => 'Kinyarwanda (Rwanda)',
           'locale' => 'rw_RW.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sa' =>
       array(
           'name' => 'Sanskrit (India)',
           'locale' => 'sa_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sc' =>
       array(
           'name' => 'Sardinian (Italy)',
           'locale' => 'sc_IT.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sd' =>
       array(
           'name' => 'Sindhi (India)',
           'locale' => 'sd_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'se' =>
       array(
           'name' => 'Northern Sami (Norway)',
           'locale' => 'se_NO.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'si' =>
       array(
           'name' => 'Sinhala (Sri Lanka)',
           'locale' => 'si_LK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
//       'sid_ET' =>
//       array(
//           'name' => 'Sidamo (Ethiopia)',
//           'locale' => 'sid_ET.UTF-8',
//       ),
       'sk' =>
       array(
           'name' => 'Slovak (Slovakia)',
           'label' => 'Slovensky',
           'locale' => 'sk_SK.UTF-8',
           'collation' => array('mysql' => 'utf8_slovak_ci'),
       ),
       'sl' =>
       array(
           'name' => 'Slovenian (Slovenia)',
           'locale' => 'sl_SI.UTF-8',
           'collation' => array('mysql' => 'utf8_slovenian_ci'),
       ),
       'so_DJ' =>
       array(
           'name' => 'Somali (Djibouti)',
           'locale' => 'so_DJ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'so_ET' =>
       array(
           'name' => 'Somali (Ethiopia)',
           'locale' => 'so_ET.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'so_KE' =>
       array(
           'name' => 'Somali (Kenya)',
           'locale' => 'so_KE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'so' =>
       array(
           'name' => 'Somali (Somalia)',
           'locale' => 'so_SO.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sq' =>
       array(
           'name' => 'Albanian (Albania)',
           'locale' => 'sq_AL.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sq_MK' =>
       array(
           'name' => 'Albanian (Macedonia)',
           'locale' => 'sq_MK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sr_ME' =>
       array(
           'name' => 'Serbian (Montenegro)',
           'locale' => 'sr_ME.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sr' =>
       array(
           'name' => 'Serbian (Serbia)',
           'locale' => 'sr_RS.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ss' =>
       array(
           'name' => 'Swati (South Africa)',
           'locale' => 'ss_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'st' =>
       array(
           'name' => 'Southern Sotho (South Africa)',
           'locale' => 'st_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sv_FI' =>
       array(
           'name' => 'Swedish (Finland)',
           'locale' => 'sv_FI.UTF-8',
           'collation' => array('mysql' => 'utf8_swedish_ci'),
       ),
       'sv' =>
       array(
           'name' => 'Swedish (Sweden)',
           'locale' => 'sv_SE.UTF-8',
           'collation' => array('mysql' => 'utf8_swedish_ci'),
       ),
       'sw' =>
       array(
           'name' => 'Swahili (Kenya)',
           'locale' => 'sw_KE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'sw_TZ' =>
       array(
           'name' => 'Swahili (Tanzania)',
           'locale' => 'sw_TZ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ta' =>
       array(
           'name' => 'Tamil (India)',
           'locale' => 'ta_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'te' =>
       array(
           'name' => 'Telugu (India)',
           'locale' => 'te_IN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'tg' =>
       array(
           'name' => 'Tajik (Tajikistan)',
           'locale' => 'tg_TJ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'th' =>
       array(
           'name' => 'Thai (Thailand)',
           'locale' => 'th_TH.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ti_ER' =>
       array(
           'name' => 'Tigrinya (Eritrea)',
           'locale' => 'ti_ER.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ti' =>
       array(
           'name' => 'Tigrinya (Ethiopia)',
           'locale' => 'ti_ET.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'tk' =>
       array(
           'name' => 'Turkmen (Turkmenistan)',
           'locale' => 'tk_TM.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'tl' =>
       array(
           'name' => 'Tagalog (Philippines)',
           'locale' => 'tl_PH.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'tn' =>
       array(
           'name' => 'Tswana (South Africa)',
           'locale' => 'tn_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'tr_CY' =>
       array(
           'name' => 'Turkish (Cyprus)',
           'locale' => 'tr_CY.UTF-8',
           'collation' => array('mysql' => 'utf8_turkish_ci'),
       ),
       'tr' =>
       array(
           'name' => 'Turkish (Turkey)',
           'locale' => 'tr_TR.UTF-8',
           'collation' => array('mysql' => 'utf8_turkish_ci'),
       ),
       'ts' =>
       array(
           'name' => 'Tsonga (South Africa)',
           'locale' => 'ts_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'tt' =>
       array(
           'name' => 'Tatar (Russia)',
           'locale' => 'tt_RU.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ug' =>
       array(
           'name' => 'Uighur (China)',
           'locale' => 'ug_CN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'uk' =>
       array(
           'name' => 'Ukrainian (Ukraine)',
           'locale' => 'uk_UA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'ur_PK' =>
       array(
           'name' => 'Urdu (Pakistan)',
           'locale' => 'ur_PK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'uz' =>
       array(
           'name' => 'Uzbek (Uzbekistan)',
           'locale' => 'uz_UZ.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       've' =>
       array(
           'name' => 'Venda (South Africa)',
           'locale' => 've_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'vi' =>
       array(
           'name' => 'Vietnamese (Vietnam)',
           'locale' => 'vi_VN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'wa' =>
       array(
           'name' => 'Walloon (Belgium)',
           'locale' => 'wa_BE.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'wo' =>
       array(
           'name' => 'Wolof (Senegal)',
           'locale' => 'wo_SN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'xh' =>
       array(
           'name' => 'Xhosa (South Africa)',
           'locale' => 'xh_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'yi' =>
       array(
           'name' => 'Yiddish (United States)',
           'locale' => 'yi_US.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'yo' =>
       array(
           'name' => 'Yoruba (Nigeria)',
           'locale' => 'yo_NG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'zh' =>
       array(
           'name' => 'Chinese (China)',
           'locale' => 'zh_CN.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'zh_HK' =>
       array(
           'name' => 'Chinese (Hong Kong SAR China)',
           'locale' => 'zh_HK.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'zh_SG' =>
       array(
           'name' => 'Chinese (Singapore)',
           'locale' => 'zh_SG.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'zh_TW' =>
       array(
           'name' => 'Chinese (Taiwan)',
           'locale' => 'zh_TW.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
       'zu_ZA' =>
       array(
           'name' => 'Zulu (South Africa)',
           'locale' => 'zu_ZA.UTF-8',
           'collation' => array('mysql' => 'utf8_unicode_ci'),
       ),
   );

   /**
    * Pole s podobnými jazyky (je použito při výchozím nasatvení jazyku)
    * @var <type>
    */
   private static $similaryLangs = array('cs' => 'sk', 'sk' => 'cs');

   /**
    * Pole se všemi názvy jazyků
    *
    * @var array
    */
   private static $localesNames = false;

   /**
    * Výchozí jazyk aplikace
    * @var string
    */
   private static $defaultLang = null;

   /**
    * Vybraný jazyk aplikace
    * @var string
    */
   private static $selectLang = null;

   /**
    * Jazyky aplikace
    * @var array
    */
   private static $appLangs = array();

   /**
    * Jestli je aplikace vícejazyčná
    * @var boolean
    */
   private static $isMultilang = false;

   /**
    * Doména modulu
    * @var string
    * @deprecated
    */
   private $moduleDomain = null;

   /**
    * Pole s importovanými doménami překladu
    * @var array
    * @deprecated
    */
   private static $bindedDomains = array();

   /**
    * Metoda pro vytvoření prostředí třídy locales
    */
   public static function factory()
   {
// vybere jazyky aplikace
      self::parseLangs();
      if (count(self::$appLangs) > 1) {
         self::$isMultilang = true;
      }
// nastaví výchozí jazyk
      self::$defaultLang = VVE_DEFAULT_APP_LANG;
   }

   /**
    * Metoda nastaví aplikaci pro zvolený jazyk
    */
   public static function selectLang()
   {
      if (self::$selectLang == null) {
// pokud nebyl jazyk nastaven při prohlížení
         if (!isset($_SESSION[self::SESSION_LANG])) {
// načteme jazyk klienta a zjistíme, jestli existuje mutace aplikace
            self::$selectLang = self::getLangsByClient();
            $_SESSION[self::SESSION_LANG] = self::$selectLang;
            if (self::$selectLang != self::$defaultLang) {
               $link = new Url_Link();
               $link->lang(self::$selectLang)->reload();
            }
         }
// jazyk klienta byl zjištěn a nastaven
         else {
            self::$selectLang = self::$defaultLang;
            $_SESSION[self::SESSION_LANG] = self::$selectLang;
         }
      } else {
         if (!self::isAppLang(self::$selectLang)) {
            self::$selectLang = self::$defaultLang;
            $tr = new Translator();
            new CoreErrors(new UnexpectedValueException($tr->tr('Zvolený jazyk není v aplikaci implementován'), 1));
         }

         if (!isset($_SESSION[self::SESSION_LANG]) OR self::$selectLang != $_SESSION[self::SESSION_LANG]) {
            $_SESSION[self::SESSION_LANG] = self::$selectLang;
         } else {
            self::$selectLang = $_SESSION[self::SESSION_LANG];
         }
      }
// Doplnění jazyků
      self::_setLangTranslations();
// nastaví Locales
      self::setLocalesEnv();
   }

   /**
    * Metoda zjistí jestli se jedná o jazyk aplikace
    * @param string $lang -- jazyk (cs, en, de)
    * @return boolean -- true pokud se jedná o jazyk aplikace
    */
   private static function isAppLang($lang)
   {
      if (in_array($lang, self::$appLangs)) {
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací pole s názvy jazyků
    *
    * @return array -- pole s názvy jazyků
    */
   public static function getAppLangsNames($returnLabels = true)
   {
      $returnArray = array();
      foreach (self::getAppLangs() as $langKey => $lang) {
         if($returnLabels){
            $returnArray[$lang] = isset(self::$locales[$lang]['label']) ? self::$locales[$lang]['label'] : self::$locales[$lang]['name'];
         } else {
            $returnArray[$lang] = self::$locales[$lang]['name'];
         }
      }
      return $returnArray;
   }

   /**
    * Metoda načte a vrátí podporované jazyky klienta
    * @return array -- pole jazyků klienta
    * @todo -- optimalizovat
    */
   public static function getLangsByClient()
   {
      $retLang = self::getDefaultLang();
      if (VVE_ENABLE_LANG_AUTODETECTION && isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
         $clientString = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
         // odstraníme mezery KHTML, webkit
         $clientString = str_replace(" ", "", $clientString);
         // rozdělit na jazyky
         $clientLangs = Explode(",", $clientString);

         // zkrácení jazyků
         function langs_strings_repair(&$lang, $key)
         {
            $match = array();
            preg_match('/([a-z]{2,3})/', $lang, $match);
            $lang = $match[1];
         }

         array_walk($clientLangs, 'langs_strings_repair');
         // test existence primárního jazyka
         if ($clientLangs[0] == self::getDefaultLang()) {
            return self::getDefaultLang();
         }
         // zkoušení dalších jazyků, které klient podporuje
         foreach ($clientLangs as $lang) {
            if(self::isAppLang($lang)){
               return $lang;
            }
         }
         // test podobnoti
         if (isset(self::$similaryLangs[$clientLangs[0]])
             AND in_array(self::$similaryLangs[$clientLangs[0]], self::getAppLangs())) {
            return self::$similaryLangs[$clientLangs[0]];
         }
         // volba podle klienta
         $match = array();
         foreach ($clientLangs as $lang) {
            if (in_array($lang, self::getAppLangs()))
               return $lang;
         }
      }
      return $retLang;
   }

   /**
    * Metoda nastaví názvy jazyků jayzyky
    * //TODO dořešit přidávání více jazyků
    */
   private static function _setLangTranslations()
   {
      /**
       * Tady asi doplnit nějak překladovou funkci
       */
      foreach (self::$locales as $code => $loc) {
         self::$localesNames[$code] = $loc['name'];
      }
//      $tr = new Translator();
//      self::$localesNames = array("cs" => $tr->tr('Česky'),
//          "en" => $tr->tr('English'),
//          "au" => $tr->tr('English (AUS)'),
//          "us" => $tr->tr('English (USA)'),
//          "de" => $tr->tr('Deutsch'),
//          "ru" => $tr->tr('Pусский'),
//          "sk" => $tr->tr('Slovensky'),
//          "da" => $tr->tr('Danish'),
//          "es" => $tr->tr('Spanish'),
//          "pl" => $tr->tr('Polski'),
//          "lv" => $tr->tr('Latvian'),
//          "is" => $tr->tr('Icelandic'),
//          "sl" => $tr->tr('Slovenian'),
//          "et" => $tr->tr('Estonian'),
//          "lt" => $tr->tr('Lithuanian'),
//          "hu" => $tr->tr('Hungarian'),
//          "sv" => $tr->tr('Swedish'),
//          "fr" => $tr->tr('Français'),
//          "no" => $tr->tr('Norsk'),
//      );
   }

   public static function getSupportedLangs()
   {
      if (!self::$localesNames) {
         self::_setLangTranslations();
      }
      return self::$localesNames;
   }

   /**
    * Metoda nastaví locales na daný jazyk
    */
   private static function setLocalesEnv()
   {
//	nastavení gettext a locales
      $locale = self::getLocale(self::getLang());
      if (SERVER_PLATFORM == 'WIN') {
         $locale = 'czech'; // Windows potřebují jiný druh
      }
      if (setlocale(LC_ALL, $locale) == false) {
         $tr = new Translator();
//         throw new DomainException(sprintf($tr->tr('Nepodporované Locales %s.'), self::getLocale(self::getLang())));
         trigger_error(sprintf($tr->tr('Nepodporované Locales %s.'), self::getLocale(self::getLang())));
      }
      /* DEPRECATED */
      bindtextdomain(self::GETTEXT_DEFAULT_DOMAIN, AppCore::getAppLibDir() . self::GETTEXT_DEFAULT_LOCALES_DIR);
      textdomain(self::GETTEXT_DEFAULT_DOMAIN);
   }

   /**
    * Metoda vrací zvolené locales pro zadaný jazyk
    * @param string -- jazyk (cs, en, de, ...)
    */
   private static function getLocale($lang = null)
   {
      if ($lang == null) {
         return self::$locales[self::$selectLang]['locale'];
      } else {
         if (key_exists($lang, self::$locales)) {
            return self::$locales[$lang]['locale'];
         } else {
//            reset(self::$superLocale);
//            return current(self::$superLocale);
            return self::$locales['en']['locale'];
         }
      }
   }

   /**
    * Metoda vrací zvolené locales pro zadaný jazyk
    * @param string -- jazyk (cs, en, de, ...)
    * @todo doladit aby tam nebylo UTF-8
    */
   public static function getLangLocale($lang = null)
   {
      $l = self::getDefaultLang();
      if ($lang != null) {
         $l = $lang;
      } else if (self::$selectLang != null) {
         $l = self::$selectLang;
      }
      if (isset(self::$locales[$l])) {
         $locale = self::$locales[$l]['locale'];
      } else {
         $locale = self::$locales['en']['locale'];
      }
// odstranění za tečkou
      $locale = preg_replace("/\.[\w-]+/i", '', $locale);
      return $locale;
   }

   /**
    * Metoda vrací výchozí jazyk aplikace (první, uvedený v configu)
    * @return string/array -- výchozí jazyk (cs, en, ..)
    */
   public static function getDefaultLang($returnArray = false)
   {
      if ($returnArray) {
         return self::getLangLabel(self::$defaultLang);
      } else {
         return self::$defaultLang;
      }
   }

   /**
    * Metoda rozparsuje hodnoty jazyku uvedených v configu
    */
   private static function parseLangs()
   {
      $langs = CUBE_CMS_APP_LANGS;
      if ($langs != null) {
         self::$appLangs = explode(self::LANG_SEPARATOR, $langs);
         self::$defaultLang = self::$appLangs[0];
      }
   }

   /**
    * Metoda vrací pole jazyků aplikace
    * @return array -- pole jazyků aplikace např. array(0 => 'cs', 1 => 'en', ...)
    */
   public static function getAppLangs()
   {
      return self::$appLangs;
   }

   /**
    * Metoda vrací true pokud jazyk existuje //TODO private funkce
    *
    * @param string -- název jazyku (cs, en, ...)
    * @return boolean -- true pro existenci jazyku
    */
   public static function langExist($lang)
   {
      if (in_array($lang, self::$appLangs)) {
         return true;
      }
      return false;
   }

   /**
    * Metoda nastaví vybraný jazyk
    * @param string -- název jazyku (cs, en, de, ...)
    */
   public static function setLang($lang)
   {
      if (self::langExist($lang)) {
         self::$selectLang = $lang;
      } else {
         self::selectLang();
      }
//self::setLocalesEnv(); // kvůli změně
   }

   /**
    * Metoda vrací vybraný jazyk aplikace
    * @return string -- vybraný jazyk aplikace
    */
   public static function getLang()
   {
      return self::$selectLang;
   }

   /**
    * Metoda vrací pole s vypraným jazykem
    * @param string $langShor -- zkratka jazyka
    */
   public static function getLangLabel($langShor)
   {
      if (in_array($langShor, self::$appLangs)) {
         $lang = Locales::getAppLangsNames();
         return array($langShor => $lang[$langShor]);
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací jazyk uživatele
    * @return string (cs, en, de, ...)
    */
   public static function getUserLang()
   {
      return Model_UsersSettings::getSettings('userlang', Locales::getLang());
   }

   /**
    * Konstruktor vytvoří objekt pro přístup k locales
    * @param string $moduleDomain
    */
   public function __construct($moduleDomain)
   {
      $this->moduleDomain = $moduleDomain;
      $this->bindTextDomain($moduleDomain);
   }

   /**
    * 
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function setDomain($domain)
   {
      $this->bindTextDomain($domain);
      $this->moduleDomain = $domain;
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function _($message, $domain = null)
   {
      return $this->gettext($message, $domain);
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function gettext($message, $domain = null)
   {
      if ($domain === null) {
         return dgettext($this->moduleDomain, $message);
      } else {
         return dgettext($domain, $message);
      }
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function ngettext($message1, $message2, $int, $domain = null)
   {
      if ($domain === null) {
         return dngettext($this->moduleDomain, $message1, $message2, $int);
      } else {
         return dngettext($domain, $message1, $message2, $int);
      }
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   private function bindTextDomain($moduleDomain)
   {
      if ($moduleDomain != null && !in_array($moduleDomain, self::$bindedDomains)) {
         bindtextdomain($moduleDomain, AppCore::getAppLibDir() . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR
             . DIRECTORY_SEPARATOR . $moduleDomain . DIRECTORY_SEPARATOR . self::LOCALES_DIR);
         array_push(self::$bindedDomains, $moduleDomain);
      }
   }

   /**
    * Metoda vrací true pokud se jedná o vícejazyčnou aplikaci
    * @return boolean
    */
   public static function isMultilang()
   {
      return self::$isMultilang;
   }

   /**
    * Metoda zkišťuje jestli je zadaný jazyk jazykem aplikace
    * @param string $lang -- jazyková zkratka (cs, en, ...)
    * @return bool
    */
   public static function isLang($lang)
   {
      return in_array($lang, self::$appLangs);
   }

   
   public static function getCollation($lang = 'cs', $driver = 'mysql')
   {
      if(isset(self::$locales[$lang]) && isset(self::$locales[$lang]['collation'])  && isset(self::$locales[$lang]['collation'][$driver])){
         return self::$locales[$lang]['collation'][$driver];
      }
      return 'utf8_unicode_ci';
   }
   
   public static function getSupportedLocale($lang = 'cs')
   {
      if(isset(self::$locales[$lang])){
         return self::$locales[$lang];
      }
      return self::$locales['en'];
   }
}
