<?php
/**
 * Soubor se specielními funkcemi, určenými do šablon
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate:  $
 * @abstract 		Funkce
 */

define('VVE_TPL_FILE_IMAGE', 'image');

function vve_get_tpl_file($file, $type) {
   switch ($type) {
      case 'image':
      default:
         if(file_exists(AppCore::getAppWebDir().Template::FACES_DIR.DIRECTORY_SEPARATOR
         .Template::face().DIRECTORY_SEPARATOR.Template::IMAGES_DIR.DIRECTORY_SEPARATOR.$file)) {
            return Template::FACES_DIR.URL_SEPARATOR.Template::face().URL_SEPARATOR
                    .Template::IMAGES_DIR.URL_SEPARATOR.$file;
         } else {
            return Template::IMAGES_DIR.URL_SEPARATOR.$file;
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
 */
function vve_tpl_xhtml_cut($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
{
   // only execute if text is longer than desired length
   if (strlen(strip_tags($text)) > $length) {
      if ($considerHtml) {
         // if the plain text is shorter than the maximum length, return the whole text
         if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
         }
         // splits all html-tags to scanable lines
         preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

         $total_length = strlen($ending);
         $open_tags = array();
         $truncate = '';

         foreach ($lines as $line_matchings) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($line_matchings[1])) {
               // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
               if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                  // do nothing
                  // if tag is a closing tag (f.e. </b>)
               } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                  // delete tag from $open_tags list
                  $pos = array_search($tag_matchings[1], $open_tags);
                  if ($pos !== false) {
                     unset($open_tags[$pos]);
                  }
                  // if tag is an opening tag (f.e. <b>)
               } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                  // add tag to the beginning of $open_tags list
                  array_unshift($open_tags, strtolower($tag_matchings[1]));
               }
               // add html-tag to $truncate'd text
               $truncate .= $line_matchings[1];
            }
            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
            if ($total_length + $content_length > $length) {
               // the number of characters which are left
               $left = $length - $total_length;
               $entities_length = 0;
               // search for html entities
               if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                  // calculate the real length of all entities in the legal range
                  foreach ($entities[0] as $entity) {
                     if ($entity[1] + 1 - $entities_length <= $left) {
                        $left--;
                        $entities_length += strlen($entity[0]);
                     } else {
                        // no more characters left
                        break;
                     }
                  }
               }
               $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
               // maximum lenght is reached, so get off the loop
               break;
            } else {
               $truncate .= $line_matchings[2];
               $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if ($total_length >= $length) {
               break;
            }
         }
      } else {
         if (strlen($text) <= $length) {
            return $text;
         } else {
            $truncate = substr($text, 0, $length - strlen($ending));
         }
      }
      // if the words shouldn't be cut in the middle...
      if ($exact) {
         // ...search the last occurance of a space...
         /* THIS NOT WORK CORECTLY. REmove ending tags and not add to opened
          
         $spacepos = strrpos($truncate, ' ');
         if (isset($spacepos)) {
            // ...and cut the text in this position
            $truncate = substr($truncate, 0, $spacepos);
         }*/
      }
      // add the defined ending to the text
      $truncate .= $ending;

      if ($considerHtml) {
         // close all unclosed html-tags
         foreach ($open_tags as $tag) {
            $truncate .= '</' . $tag . '>';
         }
      }
      return $truncate;
   } else {
      return ( $text );
   }
}

/**
 * Funkce odstraní požadovaný (nepárový) tag
 * @param string $tagName -- název tagu
 * @param string $string -- řetězec
 * @return string -- upravený řetězec
 */
function vve_tpl_remove_tag($tagName, $string) {
   if(!is_array($tagName)) {
      $tagName = array($tagName);
   }
   foreach ($tagName as $tag) {
      $string = preg_replace('/<'.$tag.' [^<>]*>/i', '', $string);
   }
   return $string;
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
 */
function vve_tpl_truncate($text,$numb,$etc = "...") {
//      $text = html_entity_decode($text, ENT_QUOTES);
   if (strlen($text) > $numb) {
      $text = substr($text, 0, $numb);
      $text = substr($text,0,strrpos($text," "));

      $punctuation = ".!?:;,-"; //punctuation you want removed

      $text = (strspn(strrev($text),  $punctuation)!=0)
              ?
              substr($text, 0, -strspn(strrev($text),  $punctuation))
              :
              $text;

      $text = $text.$etc;
   }
//      $text = htmlentities($text, ENT_QUOTES);
   return $text;
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
 * @deprecated use vve_tpl_show_text_langs() function
 */
function langsImages($text, $path = 'images/langs/small/') {
   return vve_tpl_show_text_langs($text, $path);
}

/**
 * Funkce vykreslí obrázky jazyků, které jsou vyplněny v textu (pokud web není vícejazyčný, nevykreslí se nic)
 * @param Model_ORM_LangCell $text -- objekt jazyka
 * @param string $imagesPath -- (option) cesta k obrázkům
 * @return string
 */
function vve_tpl_show_text_langs($text, $path = null)
{
   if(!Locales::isMultilang()) return null;
   $string = null;
   if(($text instanceof Model_ORM_LangCell) OR ($text instanceof Model_LangContainer_LangColumn)){
//      throw new UnexpectedValueException(_('Byl předán špatný typ jazykového kontejneru pro text'));
      foreach (Locales::getAppLangs() as $lang) {
         if($text[$lang] != null){
            $string .= vve_tpl_lang_image($lang, $path);
         }
      }
   }
   return $string;
}

/**
 * Funkce vrací obrázek pro daný jazyk
 * @param string $lang -- jazyková zkratka
 * @param string $path -- cesta k obrázkům
 * @return string tag img
 */
function vve_tpl_lang_image($lang = null, $path = null){
   if($lang == null) $lang = Locales::getDefaultLang();
   if($path == null) $path = Url_Request::getBaseWebDir(true).'images/langs/small/';
   return '<img src="'.$path.$lang.'.png" alt="'.$lang.' flag" class="lang-image" />';
}

/**
 * Funkce odstraní prázdné tagy kromě nepárových
 * @param string $text -- text
 * @return string
 */
function vve_remove_empty_tags($text)
{
   return preg_replace('/<(?!input|br|img|meta|hr|\/)[^>]*>\s*<\/[^>]*>/i', '', $text);
}

/**
 * Metoda pro výpis řetězce se zakódovanými speciálními html znaky
 * @param type $string 
 */
function ps($string)
{
   echo htmlspecialchars($string);
}

/**
 * Funkce vrátí adresu titulního obrázku např. článku 
 * @param string $img -- obrázek
 * @return string -- adresa obrázku 
 */
function vve_tpl_art_title_image($img)
{
   return Url_Request::getBaseWebDir().VVE_DATA_DIR.'/'.VVE_ARTICLE_TITLE_IMG_DIR.'/'.$img;
}

/**
 * Funkce odstraní prázdné tagy z řetězce
 * @param string $string -- řetězec, ze kterého se tagy odstraňují
 * @param string $tags -- regex pro tagy
 */
function vve_strip_empty_tags($string, $tags = 'p|a|span')
{
   $string = preg_replace('/<('.$tags.')\b[^\>]*>(?:\s|&nbsp;)*<\/\1>/', '', $string);
   $string = preg_replace('/<('.$tags.')\s*\/>/', '', $string);
   
   return $string;
}

/* 
 * html_convert_entities($string) -- convert named HTML entities to
 * XML-compatible numeric entities.
 */
function vve_html_convert_entities($string) 
{
  return preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'vve_convert_entity', (string)$string);
}

/* Swap HTML named entity with its numeric equivalent. If the entity
 * isn't in the lookup table, this function returns a blank, which
 * destroys the character in the output - this is probably the
 * desired behaviour when producing XML. 
 */
function vve_convert_entity($string) 
{
   $table = array('quot' => '&#34;',  'amp' => '&#38;', 'lt' => '&#60;', 'gt' => '&#62;', 'OElig' => '&#338;', 'oelig' => '&#339;',
                  'Scaron' => '&#352;', 'scaron' => '&#353;', 'Yuml' => '&#376;', 'circ' => '&#710;', 'tilde' => '&#732;',
                  'ensp' => '&#8194;', 'emsp' => '&#8195;', 'thinsp' => '&#8201;', 'zwnj' => '&#8204;', 'zwj' => '&#8205;',
                  'lrm' => '&#8206;', 'rlm' => '&#8207;', 'ndash' => '&#8211;', 'mdash' => '&#8212;', 'lsquo' => '&#8216;',
                  'rsquo' => '&#8217;', 'sbquo' => '&#8218;', 'ldquo' => '&#8220;', 'rdquo' => '&#8221;', 'bdquo' => '&#8222;',
                  'dagger' => '&#8224;', 'Dagger' => '&#8225;', 'permil' => '&#8240;', 'lsaquo' => '&#8249;', 'rsaquo' => '&#8250;',
                  'euro' => '&#8364;', 'fnof' => '&#402;', 'Alpha' => '&#913;', 'Beta' => '&#914;', 'Gamma' => '&#915;', 'Delta' => '&#916;',
                  'Epsilon' => '&#917;', 'Zeta' => '&#918;', 'Eta' => '&#919;', 'Theta' => '&#920;', 'Iota' => '&#921;', 'Kappa' => '&#922;',
                  'Lambda' => '&#923;', 'Mu' => '&#924;', 'Nu' => '&#925;', 'Xi' => '&#926;', 'Omicron' => '&#927;', 'Pi' => '&#928;',
                  'Rho' => '&#929;', 'Sigma' => '&#931;', 'Tau' => '&#932;', 'Upsilon' => '&#933;', 'Phi' => '&#934;', 'Chi' => '&#935;',
                  'Psi' => '&#936;', 'Omega' => '&#937;', 'alpha' => '&#945;', 'beta' => '&#946;', 'gamma' => '&#947;', 'delta' => '&#948;',
                  'epsilon' => '&#949;', 'zeta' => '&#950;', 'eta' => '&#951;', 'theta' => '&#952;', 'iota' => '&#953;', 'kappa' => '&#954;',
                  'lambda' => '&#955;', 'mu' => '&#956;', 'nu' => '&#957;', 'xi' => '&#958;', 'omicron' => '&#959;', 'pi' => '&#960;',
                  'rho' => '&#961;', 'sigmaf' => '&#962;', 'sigma' => '&#963;', 'tau' => '&#964;', 'upsilon' => '&#965;', 'phi' => '&#966;',
                  'chi' => '&#967;', 'psi' => '&#968;', 'omega' => '&#969;', 'thetasym' => '&#977;', 'upsih' => '&#978;', 'piv' => '&#982;',
                  'bull' => '&#8226;', 'hellip' => '&#8230;', 'prime' => '&#8242;', 'Prime' => '&#8243;', 'oline' => '&#8254;', 
                  'frasl' => '&#8260;', 'weierp' => '&#8472;', 'image' => '&#8465;', 'real' => '&#8476;', 'trade' => '&#8482;',
                  'alefsym' => '&#8501;', 'larr' => '&#8592;', 'uarr' => '&#8593;', 'rarr' => '&#8594;', 'darr' => '&#8595;',
                  'harr' => '&#8596;', 'crarr' => '&#8629;', 'lArr' => '&#8656;', 'uArr' => '&#8657;', 'rArr' => '&#8658;',
                  'dArr' => '&#8659;', 'hArr' => '&#8660;', 'forall' => '&#8704;', 'part' => '&#8706;', 'exist' => '&#8707;','empty' => '&#8709;',
                  'nabla' => '&#8711;', 'isin' => '&#8712;', 'notin' => '&#8713;', 'ni' => '&#8715;', 'prod' => '&#8719;', 'sum' => '&#8721;',
                  'minus' => '&#8722;', 'lowast' => '&#8727;', 'radic' => '&#8730;', 'prop' => '&#8733;', 'infin' => '&#8734;','ang' => '&#8736;',
                  'and' => '&#8743;', 'or' => '&#8744;', 'cap' => '&#8745;', 'cup' => '&#8746;', 'int' => '&#8747;', 'there4' => '&#8756;',
                  'sim' => '&#8764;', 'cong' => '&#8773;', 'asymp' => '&#8776;', 'ne' => '&#8800;', 'equiv' => '&#8801;', 'le' => '&#8804;',
                  'ge' => '&#8805;', 'sub' => '&#8834;', 'sup' => '&#8835;', 'nsub' => '&#8836;', 'sube' => '&#8838;', 'supe' => '&#8839;',
                  'oplus' => '&#8853;','otimes' => '&#8855;','perp' => '&#8869;','sdot' => '&#8901;','lceil' => '&#8968;','rceil' => '&#8969;',
                  'lfloor' => '&#8970;','rfloor' => '&#8971;','lang' => '&#9001;','rang' => '&#9002;','loz' => '&#9674;','spades' => '&#9824;',
                  'clubs' => '&#9827;','hearts' => '&#9829;','diams' => '&#9830;','nbsp' => '&#160;','iexcl' => '&#161;','cent' => '&#162;',
                  'pound' => '&#163;','curren' => '&#164;','yen' => '&#165;','brvbar' => '&#166;','sect' => '&#167;','uml' => '&#168;',
                  'copy' => '&#169;','ordf' => '&#170;','laquo' => '&#171;','not' => '&#172;','shy' => '&#173;','reg' => '&#174;',
                  'macr' => '&#175;','deg' => '&#176;','plusmn' => '&#177;','sup2' => '&#178;','sup3' => '&#179;','acute' => '&#180;',
                  'micro' => '&#181;','para' => '&#182;','middot' => '&#183;','cedil' => '&#184;','sup1' => '&#185;','ordm' => '&#186;',
                  'raquo' => '&#187;','frac14' => '&#188;','frac12' => '&#189;','frac34' => '&#190;','iquest' => '&#191;','Agrave' => '&#192;',
                  'Aacute' => '&#193;','Acirc' => '&#194;','Atilde' => '&#195;','Auml' => '&#196;','Aring' => '&#197;','AElig' => '&#198;',
                  'Ccedil' => '&#199;','Egrave' => '&#200;','Eacute' => '&#201;','Ecirc' => '&#202;','Euml' => '&#203;','Igrave' => '&#204;',
                  'Iacute' => '&#205;','Icirc' => '&#206;','Iuml' => '&#207;','ETH' => '&#208;','Ntilde' => '&#209;','Ograve' => '&#210;',
                  'Oacute' => '&#211;','Ocirc' => '&#212;','Otilde' => '&#213;','Ouml' => '&#214;','times' => '&#215;','Oslash' => '&#216;',
                  'Ugrave' => '&#217;','Uacute' => '&#218;','Ucirc' => '&#219;','Uuml' => '&#220;','Yacute' => '&#221;','THORN' => '&#222;',
                  'szlig' => '&#223;','agrave' => '&#224;','aacute' => '&#225;','acirc' => '&#226;','atilde' => '&#227;','auml' => '&#228;',
                  'aring' => '&#229;','aelig' => '&#230;','ccedil' => '&#231;','egrave' => '&#232;','eacute' => '&#233;','ecirc' => '&#234;',
                  'euml' => '&#235;','igrave' => '&#236;','iacute' => '&#237;','icirc' => '&#238;','iuml' => '&#239;','eth' => '&#240;',
                  'ntilde' => '&#241;','ograve' => '&#242;','oacute' => '&#243;','ocirc' => '&#244;','otilde' => '&#245;','ouml' => '&#246;',
                  'divide' => '&#247;','oslash' => '&#248;','ugrave' => '&#249;','uacute' => '&#250;','ucirc' => '&#251;','uuml' => '&#252;',
                  'yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;'
                        );
   
/*   $table = array('&quot;' => '&#34;',  '&amp;' => '&#38;', '&lt;' => '&#60;', '&gt;' => '&#62;', '&OElig;' => '&#338;', '&oelig;' => '&#339;',
                  '&Scaron;' => '&#352;', '&scaron;' => '&#353;', '&Yuml;' => '&#376;', '&circ;' => '&#710;', '&tilde;' => '&#732;',
                  '&ensp;' => '&#8194;', 'emsp;' => '&#8195;', '&thinsp;' => '&#8201;', '&zwnj;' => '&#8204;', '&zwj;' => '&#8205;',
                  '&lrm;' => '&#8206;', '&rlm;' => '&#8207;', '&ndash;' => '&#8211;', '&mdash;' => '&#8212;', '&lsquo;' => '&#8216;',
                  '&rsquo;' => '&#8217;', '&sbquo;' => '&#8218;', '&ldquo;' => '&#8220;', '&rdquo;' => '&#8221;', '&bdquo;' => '&#8222;',
                  '&dagger;' => '&#8224;', '&Dagger;' => '&#8225;', '&permil;' => '&#8240;', '&lsaquo;' => '&#8249;', '&rsaquo;' => '&#8250;',
                  '&euro;' => '&#8364;', '&fnof;' => '&#402;', '&Alpha;' => '&#913;', '&Beta;' => '&#914;', 'G&amma;' => '&#915;', '&Delta;' => '&#916;',
                  '&Epsilon;' => '&#917;', '&Zeta;' => '&#918;', '&Eta;' => '&#919;', '&Theta;' => '&#920;', '&Iota;' => '&#921;', '&Kappa;' => '&#922;',
                  '&Lambda;' => '&#923;', '&Mu;' => '&#924;', '&Nu;' => '&#925;', '&Xi;' => '&#926;', '&Omicron;' => '&#927;', '&Pi;' => '&#928;',
                  '&Rho;' => '&#929;', '&Sigma;' => '&#931;', '&Tau;' => '&#932;', '&Upsilon;' => '&#933;', '&Phi;' => '&#934;', '&Chi;' => '&#935;',
                  '&Psi;' => '&#936;', '&Omega;' => '&#937;', '&alpha;' => '&#945;', '&beta;' => '&#946;', '&gamma;' => '&#947;', '&delta;' => '&#948;',
                  '&epsilon;' => '&#949;', '&zeta;' => '&#950;', '&eta;' => '&#951;', '&theta;' => '&#952;', '&iota;' => '&#953;', '&kappa;' => '&#954;',
                  '&lambda;' => '&#955;', '&mu;' => '&#956;', '&nu;' => '&#957;', '&xi;' => '&#958;', '&omicron;' => '&#959;', '&pi;' => '&#960;',
                  '&rho;' => '&#961;', '&sigmaf;' => '&#962;', '&sigma;' => '&#963;', '&tau;' => '&#964;', '&upsilon;' => '&#965;', '&phi;' => '&#966;',
                  '&chi;' => '&#967;', '&psi;' => '&#968;', '&omega;' => '&#969;', '&thetasym;' => '&#977;', '&upsih;' => '&#978;', '&piv;' => '&#982;',
                  '&bull;' => '&#8226;', '&hellip;' => '&#8230;', '&prime;' => '&#8242;', '&Prime;' => '&#8243;', '&oline;' => '&#8254;', 
                  '&frasl;' => '&#8260;', '&weierp;' => '&#8472;', '&image;' => '&#8465;', '&real;' => '&#8476;', '&trade;' => '&#8482;',
                  '&alefsym;' => '&#8501;', '&larr;' => '&#8592;', '&uarr;' => '&#8593;', '&rarr;' => '&#8594;', '&darr;' => '&#8595;',
                  '&harr;' => '&#8596;', '&crarr;' => '&#8629;', '&lArr;' => '&#8656;', '&uArr;' => '&#8657;', '&rArr;' => '&#8658;',
                  '&dArr;' => '&#8659;', '&hArr;' => '&#8660;', '&forall;' => '&#8704;', '&part;' => '&#8706;', '&exist;' => '&#8707;','&empty;' => '&#8709;',
                  '&nabla;' => '&#8711;', '&isin;' => '&#8712;', '&notin;' => '&#8713;', '&ni;' => '&#8715;', '&prod;' => '&#8719;', '&sum;' => '&#8721;',
                  '&minus;' => '&#8722;', '&lowast;' => '&#8727;', '&radic;' => '&#8730;', '&prop;' => '&#8733;', '&infin;' => '&#8734;','&ang;' => '&#8736;',
                  '&and;' => '&#8743;', '&or;' => '&#8744;', '&cap;' => '&#8745;', '&cup;' => '&#8746;', '&int;' => '&#8747;', '&there4;' => '&#8756;',
                  '&sim;' => '&#8764;', '&cong;' => '&#8773;', '&asymp;' => '&#8776;', '&ne;' => '&#8800;', '&equiv;' => '&#8801;', '&le;' => '&#8804;',
                  '&ge;' => '&#8805;', '&sub;' => '&#8834;', '&sup;' => '&#8835;', '&nsub;' => '&#8836;', '&sube;' => '&#8838;', '&supe;' => '&#8839;',
                  '&oplus;' => '&#8853;','&otimes;' => '&#8855;','&perp;' => '&#8869;','&sdot;' => '&#8901;','&lceil;' => '&#8968;','&rceil;' => '&#8969;',
                  '&lfloor;' => '&#8970;','&rfloor;' => '&#8971;','&lang;' => '&#9001;','&rang;' => '&#9002;','&loz;' => '&#9674;','&spades;' => '&#9824;',
                  '&clubs;' => '&#9827;','&hearts;' => '&#9829;','&diam;s' => '&#9830;','&nbsp;' => '&#160;','&iexcl;' => '&#161;','&cent;' => '&#162;',
                  '&pound;' => '&#163;','&curren;' => '&#164;','&yen;' => '&#165;','&brvbar;' => '&#166;','&sect;' => '&#167;','&uml;' => '&#168;',
                  '&copy;' => '&#169;','&ordf;' => '&#170;','&laquo;' => '&#171;','&not;' => '&#172;','&shy;' => '&#173;','&reg;' => '&#174;',
                  '&macr;' => '&#175;','&deg;' => '&#176;','&plusmn;' => '&#177;','&sup2;' => '&#178;','&sup3;' => '&#179;','&acute;' => '&#180;',
                  '&micro;' => '&#181;','&para;' => '&#182;','&middot;' => '&#183;','&cedil;' => '&#184;','&sup1;' => '&#185;','&ordm;' => '&#186;',
                  '&raquo;' => '&#187;','&frac14;' => '&#188;','&frac12;' => '&#189;','&frac34;' => '&#190;','&iquest;' => '&#191;','&Agrave;' => '&#192;',
                  '&Aacute;' => '&#193;','&Acirc;' => '&#194;','&Atilde;' => '&#195;','&Auml;' => '&#196;','&Aring;' => '&#197;','&AElig;' => '&#198;',
                  '&Ccedil;' => '&#199;','&Egrave;' => '&#200;','&Eacute;' => '&#201;','&Ecirc;' => '&#202;','&Euml;' => '&#203;','&Igrave;' => '&#204;',
                  '&Iacute;' => '&#205;','&Icirc;' => '&#206;','&Iuml;' => '&#207;','&ETH;' => '&#208;','&Ntilde;' => '&#209;','&Ograve;' => '&#210;',
                  '&Oacute;' => '&#211;','&Ocirc;' => '&#212;','&Otilde;' => '&#213;','&Ouml;' => '&#214;','&times;' => '&#215;','&Oslash;' => '&#216;',
                  '&Ugrave;' => '&#217;','&Uacute;' => '&#218;','&Ucirc;' => '&#219;','&Uuml;' => '&#220;','&Yacute;' => '&#221;','&THORN;' => '&#222;',
                  '&szlig;' => '&#223;','&agrave;' => '&#224;','&aacute;' => '&#225;','&acirc;' => '&#226;','&atilde;' => '&#227;','&auml;' => '&#228;',
                  '&aring;' => '&#229;','&aelig;' => '&#230;','&ccedil;' => '&#231;','&egrave;' => '&#232;','&eacute;' => '&#233;','&ecirc;' => '&#234;',
                  '&euml;' => '&#235;','&igrave;' => '&#236;','&iacute;' => '&#237;','&icirc;' => '&#238;','&iuml;' => '&#239;','&eth;' => '&#240;',
                  '&ntilde;' => '&#241;','&ograve;' => '&#242;','&oacute;' => '&#243;','&ocirc;' => '&#244;','&otilde;' => '&#245;','&ouml;' => '&#246;',
                  '&divide;' => '&#247;','&oslash;' => '&#248;','&ugrave;' => '&#249;','&uacute;' => '&#250;','&ucirc;' => '&#251;','&uuml;' => '&#252;',
                  'yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;');*/
   
   // Entity not found? Destroy it.
   return isset($table[$matches[1]]) ? $table[$matches[1]] : '';
}

/**
 * Funkce rozdělí řetězce na poloviny a jednu vrátí (první polovina je vždy větší)
 * @param string $string -- řetězec
 * @param bool $firstHalf -- jestli se má vrátit první půlka nebo druhá
 */
function vve_split_to_half($text, $firstHalf = true)
{
   $text = strip_tags($text);
   $splitstring1 = substr($text, 0, floor(strlen($text) / 2));
   $splitstring2 = substr($text, floor(strlen($text) / 2));

   if (substr($splitstring1, 0, -1) != ' ' AND substr($splitstring2, 0, 1) != ' ') {
      $middle = strlen($splitstring1) + strpos($splitstring2, ' ') + 1;
   } else {
      $middle = strrpos(substr($text, 0, floor(strlen($text) / 2)), ' ') + 1;
   }

   if($firstHalf){
      return substr($text, 0, $middle);
   }
   return substr($text, $middle);
}
?>
