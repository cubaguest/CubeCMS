<?php

/**
 * Třída pro usnadnění práce s html řetězci
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Html {

   /**
    * Funkce odstraní html tagy z řetězce a pole (rekurzivní)
    * @param mixed $value -- samotný řetězec nebo pole
    * @return mixed -- řětezce nebo pole bez html tagů
    */
   public static function stripTags($value, $allowedtags = null)
   {
      if (is_array($value)) {
         foreach ($value as $key => $val) {
            $value[$key] = vve_strip_tags($val, $allowedtags);
         }
      } else {
         $value = strip_tags($value, $allowedtags);
      }
      return $value;
   }

   /**
    * Odstraní html komentáře z kódu
    * @param type $str
    * @return type
    */
   public static function stripHtmlComment($str)
   {
      if (is_array($str)) {
         foreach ($str as $key => $s) {
            $str[$key] = self::stripHtmlComment($s);
         }
      } else {
         $str = preg_replace('/<!--(.|\s)*?-->/', '', $str);
      }
      return $str;
   }
   
   
   protected function removeElementsByTagName($tagName, $document) {
      $nodeList = $document->getElementsByTagName($tagName);
      for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0; ) {
        $node = $nodeList->item($nodeIdx);
        $node->parentNode->removeChild($node);
      }
    }  
    
   /**
    * Odstraní tagy bez informací
    * @param type $str
    * @return type
    */
   public static function stripHtml($str)
   {
      if (is_array($str)) {
         foreach ($str as $key => $s) {
            $str[$key] = self::stripHtml($s);
         }
      } else {
         // script, link, style, atd.
         $str = self::removeTag(array('style', 'head','link','script', 'head'), $str);
         // komentáře
         $str = self::stripHtmlComment($str);
         // tagy
         $str = strip_tags($str);
      }
      return $str;
   }

   /**
    * Meotda zkrátí html řetězec
    * @param string $html
    * @param int $length
    * @return string
    */
   public static function truncate($html, $length)
   {
      if (mb_strlen(strip_tags($html)) > $length) {
         $cutter = new HtmlCutString($html, $length);
         return $cutter->cut();
      }
      return $html;
   }
   
   /**
    * Odstraní prázdné tagy z textu
    * @param string $string
    * @return string
    */
   public static function stripEmptyTags($string, $tags = 'p|a|span')
   {
      return preg_replace(array(
          '/<('.$tags.')\b[^\>]*>(?:\s|&nbsp;)*<\/\1>/',
          '/<('.$tags.')\s*\/>/',
          ), 
          array('', ''),
          $string);
   }
   
   /**
    * convert named HTML entities to
    * XML-compatible numeric entities.
    */
   public static function convertNamedEntities($string)
   {
      return preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', array('Utils_Html', '_convertEntity'), (string)$string);
   }
   
   /**
    * Podpůrná funkce pro convertNamedEntities
    * @param type $string
    * @return type
    */
   public static function _convertEntity($string) 
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
      // Entity not found? Destroy it.
      return isset($table[$string]) ? $table[$string] : '';
   }
   
   /**
    * Převede emaily na odkazy
    * @param string $text
    * @return string
    */
   public static function emailsToLinks($text)
   {
      $regex = "/([a-z0-9_\-\.]+)". # name
         "@". # at
         "([a-z0-9-]{1,64})". # domain
         "\.". # period
         "([a-z]{2,10})/i"; # domain extension
      $text = preg_replace($regex, '<a class="mail" href="mailto:\\1@\\2.\\3">\\1@\\2.\\3</a>', $text);
      return $text;
   }

   /**
    * Ochrání emaily před spamboty
    * @param string $email
    * @return string
    */
   public static function protectedEmailLink($email, $classes = '')
   {
      $parts = explode('@', $email);

      $string = '<script type="text/javascript"> '
         .'document.write("<a class=\"'. (is_array($classes) ? implode(' ', $classes) : $classes ).'\" href=\"mailto");'
         .'document.write(":" + "'.$parts[0].'" + "@");'
         .'document.write("'.$parts[1].'" + "\">" + "'.$parts[0].'" + "@" + "'.$parts[1].'" + "<\/a>");'
         .'</script>';
      return $string;
   }
   
   /**
    * Zabezpečí emaily
    * @param string $text
    * @return string
    * @todo Zatím není implemntována
    */
   public static function protectEmails($text)
   {
      return $text;
   }
   
   /**
    * Funkce odstraní požadovaný (nepárový) tag
    * @param string $tagName -- název tagu
    * @param string $string -- řetězec
    * @return string -- upravený řetězec
    */
   public static function removeTag($tagName, $string) {
      if(!is_array($tagName)) {
         $tagName = array($tagName);
      }
      foreach ($tagName as $tag) {
         $string = preg_replace('/(<('.$tag.')\b[^>]*>).*?(<\/\2>)/is', '', $string);
      }
      return $string;
   }
}

/**
 * Author prajwala
 * email  m.prajwala@gmail.com
 * Date   12/04/2009
 * version 1.1
 */
class HtmlCutString {
   protected $tempDiv;
   protected $charCount = 0;
   protected $encoding = 'UTF-8';
   protected $endChars;
   protected $endString;

   public function __construct($string, $limit, $endChars = '...'){
      // create dom element using the html string
      $this->tempDiv = new DomDocument;
      $this->tempDiv->loadXML('<div>'.$string.'</div>');
      // character limit need to check
      $this->limit = $limit;
      $this->endChars = $endChars;
   }
  
   public function cut(){
      // create empty document to store new html
      $this->newDiv = new DomDocument;
      // cut the string by parsing through each element
      $this->searchEnd($this->tempDiv->documentElement,$this->newDiv);
      $newhtml = $this->newDiv->saveHTML();
      return $newhtml;
   }

   protected function deleteChildren($node) {
      while (isset($node->firstChild)) {
         $this->deleteChildren($node->firstChild);
         $node->removeChild($node->firstChild);
      }
   } 
  
   protected function searchEnd($parseDiv, $newParent){
      foreach($parseDiv->childNodes as $ele){
      // not text node
         if($ele->nodeType != 3){
             $newEle = $this->newDiv->importNode($ele,true);
            if(count($ele->childNodes) === 0){
               $newParent->appendChild($newEle);
               continue;
            }
            $this->deleteChildren($newEle);
            $newParent->appendChild($newEle);
            $res = $this->searchEnd($ele,$newEle);
            if($res) {
               return $res;
            } else {
               continue;
            }
         }

         // the limit of the char count reached
         if(mb_strlen($ele->nodeValue,$this->encoding) + $this->charCount >= $this->limit){
            $newEle = $this->newDiv->importNode($ele);
            $newEle->nodeValue = 
                substr($newEle->nodeValue,0, $this->limit - $this->charCount)
                . $this->endChars;
            $newParent->appendChild($newEle);
            return true;
         }
         $newEle = $this->newDiv->importNode($ele);
         $newParent->appendChild($newEle);
         $this->charCount += mb_strlen($newEle->nodeValue,$this->encoding);
      }
      return false;
   }
   
   public function __toString()
   {
      return $this->cut();
   }
}