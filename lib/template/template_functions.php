<?php
/**
 * Soubor se specielními funkcemi, určenými do šablon
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.16 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate:  $
 * @abstract 		Funkce
 */

define('VVE_TPL_FILE_IMAGE', 'image');

/**
 * 
 * @param type $file
 * @param type $type
 * @return type
 * @deprecated since version 8.0.0 NEPOUŽÍVAT!!!!!
 */
function vve_get_tpl_file($file, $type) {
   switch ($type) {
      case 'image':
      default:
         if(file_exists(AppCore::getAppWebDir().Template::FACES_DIR.DIRECTORY_SEPARATOR
         .Template::face().DIRECTORY_SEPARATOR.Template::IMAGES_DIR.DIRECTORY_SEPARATOR.$file)) {
            return '/'.Template::FACES_DIR.URL_SEPARATOR.Template::face().URL_SEPARATOR
                    .Template::IMAGES_DIR.URL_SEPARATOR.$file;
         } else {
            return '/'.Template::IMAGES_DIR.URL_SEPARATOR.$file;
         }
         break;
   }
}

/**
 * Zkrácení textu s XHTML značkami
 * @param string $s -- zkracovaný řetězec bez komentářů a bloků skriptu
 * @param int $limit -- požadovaný počet vrácených znaků
 * @param string $delimiter -- (option) oddělovač na konci (např. "...")
 * @param string $link -- (option) odkaz na více za textem
 * @param string $linkText -- (option) text textu pro více
 * @return string -- zkrácený řetězec se správně uzavřenými značkami
 * @author Gabi Solomon
 * @link http://www.gsdesign.ro/blog/cut-html-string-without-breaking-the-tags/
 * @deprecated since version 8.0 Uset Utils_Html::truncate()
 */
function vve_tpl_xhtml_cut($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
{
   return Utils_Html::truncate($text, $length);
}

/**
 * Funkce odstraní požadovaný (nepárový) tag
 * @param string $tagName -- název tagu
 * @param string $string -- řetězec
 * @return string -- upravený řetězec
 * @deprecated since version 8.0.0 use Utils_Html::removeTag()
 */
function vve_tpl_remove_tag($tagName, $string) {
   return Utils_Html::removeTag($tagName, $string);
}


/**
 * Metoda vytvoří tag obrázku s maximální velikostí obrázku
 * @param string $imagePath -- secta k obrázku
 * @param string $alt -- titulek obrázku
 * @param int $maxWidth -- maximální šířka
 * @param int $maxHeight -- maximální výška
 * @return string -- tag obrázku
 * @todo dodělat pokud je s obrázkem předán parametr, ten se musí zkopírovat také na výstup
 */
function vve_tpl_image_tag($imagePath, $alt = null, $mw = false, $mh = false, $clases = null, $other = null, $realpath = false) {
   // jetli se jedná o soubor na tomto webu
   if(preg_match('/^'.addcslashes(Url_Request::getBaseWebDir(),'/').'(.*)/',$imagePath) == 1) {
      // převede se web adresa
      $imagePath = str_replace(Url_Request::getBaseWebDir(),AppCore::getAppWebDir(), $imagePath);
      // pokud neexistuje zkusíme přejít k hlavnímu webu
   }

   $matches = array();
   preg_match('/^([^?]+)(\??.*)/', $imagePath,$matches);
   $imagePath = $matches[1];
   $class = null;
   if($clases != null) {
      if(!is_array($clases)) {
         $clases = array($clases);
      }
      foreach ($clases as $cl) {
         $class .= $cl." ";
      }
      substr($class, 0, strlen($class)-1);
      $class = "class=\"".$class."\" ";
   }

   $others = null;
   if($other != null) {
      foreach ($other as $key => $val) {
         $others .= $key."=\"{$val}\" ";
      }
   }
   if(file_exists($imagePath)) {
      $imageSizes = getimagesize($imagePath);
      if($imageSizes) {
         list($w,$h) = $imageSizes;
         foreach(array('w','h') as $v) {
            $m = "m{$v}";
            if(${$v} > ${$m} && ${$m}) {
               $o = ($v == 'w') ? 'h' : 'w';
               $r = ${$m} / ${$v};
               ${$v} = ${$m};
               ${$o} = ceil(${$o} * $r);
            }
         }
         // převod zpět na adresu serveru
         $imagePath = str_replace(array(AppCore::getAppWebDir(), AppCore::getAppLibDir()),
                 Url_Request::getBaseWebDir(), $imagePath);
//         $imagePath = str_replace(AppCore::getAppLibDir(), Url_Request::getBaseWebDir(), $imagePath);
         return("<img src=\"".$imagePath.$matches[2]."\" alt=\"{$alt}\" width=\"{$w}\" height=\"{$h}\" {$class}{$others}/>");
      }
   }
   return ("<img src=\"".$imagePath.$matches[2]."\" alt=\"{$alt}\" width=\"{$mw}\" height=\"{$mh}\" {$class}{$others}/>");
}

/**
 * Funkce ořeže řetězec na zadanou délku
 * @param string $text -- řetězec
 * @param int $numb -- počet znaků
 * @param string $etc -- ukončovací znaky
 * @return string -- ořezaný řetězec
 * @author alishahnovin@hotmail.com 2007
 * @link http://php.net/manual/en/function.substr-replace.php
 * @deprecated since version 8.0 - use Utils_String::truncate();
 */
function vve_tpl_truncate($text,$numb,$etc = "...") {
    return Utils_String::truncate($text, $numb, $etc);
}

function vve_tpl_date($format, $timestamp = null) {
   return vve_date($format, $timestamp);
}

/**
 * Funkce vrátí řetězec flash objektem (validovaným)
 * @param string $flashUrl -- adresa k flashi
 * @param int $w -- šířka
 * @param int $h -- výška
 * @param string $alternateStr -- alternativní string
 * @param array $params -- pole s dalšími parametry předanými parametry vkládanými do objektu (parametry param)
 * @return string
 */
function vve_tpl_flash($flashUrl, $w, $h, $alternateStr = '<p>This is <b>alternative</b> content.</p>', $params = array()) {
   $flashStr = null;
   $defParams = array(
      'menu' => 'false',
      'loop' => 'true'
   );
   $params = array_merge($defParams, $params);

   $flashStr .= '<!--[if !IE]> --><object type="application/x-shockwave-flash" data="'.$flashUrl.'" width="'.$w.'" height="'.$h.'"><!-- <![endif]--><!--[if IE]><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"  width="'.$w.'" height="'.$h.'"><param name="movie" value="'.$flashUrl.'" /><!--><!--dgx-->';
   foreach ($params as $key => $value) {
      $flashStr .= '<param name="'.$key.'" value="'.(string)$value.'" />';
   }
   $flashStr .= $alternateStr;
   $flashStr .= '</object><!-- <![endif]-->';
   return $flashStr;
}

/**
 * Funkce vykreslí obrázky jazyků, které jsou vyplněny v textu (pokud web není vícejazyčný, nevykreslí se nic)
 * @param Model_ORM_LangCell $text -- objekt jazyka
 * @param string $imagesPath -- (option) cesta k obrázkům
 * @return string
 * @deprecated use Utils_CMS::getLangsImages();
 */
function langsImages($text, $path = 'images/langs/small/') {
   return vve_tpl_show_text_langs($text, $path);
}

/**
 * Funkce vykreslí obrázky jazyků, které jsou vyplněny v textu (pokud web není vícejazyčný, nevykreslí se nic)
 * @param Model_ORM_LangCell $text -- objekt jazyka
 * @param string $imagesPath -- (option) cesta k obrázkům
 * @return string
 * @deprecated since version 8.0.0 use Utils_CMS::getLangsImages()
 */
function vve_tpl_show_text_langs($text, $path = null)
{
   return Utils_CMS::getLangsImages($text, $path);
}

/**
 * Funkce vrací obrázek pro daný jazyk
 * @param string $lang -- jazyková zkratka
 * @param string $path -- cesta k obrázkům
 * @return string tag img
 * @deprecated since version 8.0.0 use Utils_CMS::getLangImage()
 */
function vve_tpl_lang_image($lang = null, $path = null){
   return Utils_CMS::getLangImage($lang, $path);
}

/**
 * Funkce odstraní prázdné tagy kromě nepárových
 * @param string $text -- text
 * @return string
 * @deprecated since version 8.0.0 use Utils_Html::stripEmptyTags();
 */
function vve_remove_empty_tags($text)
{
   return Utils_Html::stripEmptyTags($text);
}

/**
 * Metoda pro výpis řetězce se zakódovanými speciálními html znaky
 * @param string $string 
 * @param mixed - (optional) argumenty, jako u funkce sprintf
 */
function ps($string)
{
   if(func_num_args() == 1){
      echo htmlspecialchars($string);
   } else {
      $args = func_get_args();
      array_shift($args);
      echo htmlspecialchars(vsprintf($string, $args));
   }
}

/**
 * Metoda pro výpis řetězce se zakódovanými speciálními html znaky pro atributy
 * @param string $string 
 * @param mixed - (optional) argumenty, jako u funkce sprintf
 */
function psa($string)
{
   if(func_num_args() == 1){
      echo addslashes(htmlspecialchars($string));
   } else {
      $args = func_get_args();
      array_shift($args);
      echo addslashes(htmlspecialchars(vsprintf($string, $args)));
   }
}

/**
 * Metoda pro výpis řetězce se zakódovanými speciálními html znaky a v jazykové mutaci. 
 * Pokud je povolena suptituce a daný jazyk je prázdný, vrátí se výchozí jazky položky 
 * @param string $string 
 * @param bool - (optional) jesli vypsat výchozí jazyk při neexistenci
 */
function ps_lang($string, $allowDefault = VVE_DEFAULT_LANG_SUBSTITUTION) 
{
   if(isset($string[Locales::getLang()]) && $string[Locales::getLang()] != null){
      ps($string[Locales::getLang()]);return;
   } else if(isset($string[Locales::getDefaultLang()]) && $string[Locales::getDefaultLang()] != null ){
      ps($string[Locales::getDefaultLang()]);return;
   }
   ps($string);
}

/**
 * Metoda pro výpis řetězce v jazykové mutaci. 
 * Pokud je povolena suptituce a daný jazyk je prázdný, vrátí se výchozí jazky položky 
 * @param string $string 
 * @param bool - (optional) jesli vypsat výchozí jazyk při neexistenci
 */
function p_lang($string, $allowDefault = VVE_DEFAULT_LANG_SUBSTITUTION) 
{
   if(isset($string[Locales::getLang()]) && $string[Locales::getLang()] != null){
      echo ($string[Locales::getLang()]);return;
   } else if(isset($string[Locales::getDefaultLang()]) && $string[Locales::getDefaultLang()] != null ){
      echo ($string[Locales::getDefaultLang()]);return;
   }
   echo ($string);
}

/**
 * Metoda vrátí adresu adresáře ze šablony
 * @param string $string -- adresa uvnitř adresáře šablony se souborem
 * @return string
 */
function tp($path = null)
{
   return Template::face(false).$path;
}

/**
 * Funkce vrátí adresu titulního obrázku např. článku 
 * @param string/Model_ORM_Record $img -- obrázek
 * @return string -- adresa obrázku 
 * @deprecated since version 8.0.0 use Utils_CMS::getArticleTitleImage()
 */
function vve_tpl_art_title_image($img, $prop = Articles_Model::COLUMN_TITLE_IMAGE)
{
   return Utils_CMS::getArticleTitleImage($img, $prop);
}

/**
 * Funkce odstraní prázdné tagy z řetězce
 * @param string $string -- řetězec, ze kterého se tagy odstraňují
 * @param string $tags -- regex pro tagy
 * @deprecated since version 8.0.0 use Utils_Html::stripEmptyTags();
 */
function vve_strip_empty_tags($string, $tags = 'p|a|span')
{
   return Utils_Html::stripEmptyTags($string, $tags);
}

/** 
 * html_convert_entities($string) -- convert named HTML entities to
 * XML-compatible numeric entities.
 * @deprecated since version 8.0.0. use Utils_Html::convertNamedEntities()
 */
function vve_html_convert_entities($string) 
{
  return Utils_Html::convertNamedEntities($string);
}


/**
 * Funkce rozdělí řetězce na poloviny a jednu vrátí (první polovina je vždy větší)
 * @param string $string -- řetězec
 * @param bool $firstHalf -- jestli se má vrátit první půlka nebo druhá
 * @deprecated since version 8.0.0 use Utils_String::splitToHalf()
 */
function vve_split_to_half($text, $firstHalf = true)
{
   return Utils_String::splitToHalf($text, $firstHalf);
}

/**
 * Převede emaily v textu na klikací linky
 * @param string $text
 * @return string
 * @deprecated since version 8.0.0 use Utils_Html::emailsToLinks()
 */
function vve_convert_emails_to_links($text)
{
   return Utils_Html::emailsToLinks($text);
}
?>
