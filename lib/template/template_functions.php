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
 * @return string -- zkrácený řetězec se správně uzavřenými značkami
 * @author Jakub Vrána
 * @link http://www.root.cz/clanky/php-zkraceni-textu-s-xhtml-znackami/
 */
function vve_tpl_xhtml_cut($s, $limit, $delimiter = '...') {
   $strLen = strlen($s);
   $length = 0;
   $tags = array(); // dosud neuzavřené značky
   for ($i=0; $i < strlen($s) && $length < $limit; $i++) {
      switch ($s{$i}) {

         case '<':
         // načtení značky
            $start = $i+1;
            while ($i < strlen($s) && $s{$i} != '>' && !ctype_space($s{$i})) {
               $i++;
            }
            $tag = substr($s, $start, $i - $start);
            // přeskočení případných atributů
            while ($i < strlen($s) && $s{$i} != '>') {
               $i++;
            }
            if ($s{$start} == '/') { // uzavírací značka
               array_shift($tags); // v XHTML dokumentu musí být vždy uzavřena poslední neuzavřená značka
            } elseif ($s{$i-1} != '/') { // otevírací značka
               array_unshift($tags, $tag);
            }
            break;

         case '&':
            $length++;
            while ($i < strlen($s) && $s{$i} != ';') {
               $i++;
            }
            break;

         default:
            $length++;

      }
   }
   $s = substr($s, 0, $i);
   if($strLen > $limit) {
      $s .= $delimiter;
   }
   if ($tags) {
      $s .= "</" . implode("></", $tags) . ">";
   }

   return $s;
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
 */
function langsImages($text, $path = 'images/langs/small') {
   if(!Locales::isMultilang()) return null;
   if(($text instanceof Model_ORM_LangCell) == false
      AND ($text instanceof Model_LangContainer_LangColumn) == false) throw new UnexpectedValueException(_('Byl předán špatný typ jazykového kontejneru pro text'));
   $string = null;
   foreach (Locales::getAppLangs() as $lang) {
      if($text[$lang] != null){
         $string .= '<img src="'.$path.URL_SEPARATOR.$lang.'.png" alt="'.$lang.'" class="lang-image" />';
      }
   }
   return $string;
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
?>
