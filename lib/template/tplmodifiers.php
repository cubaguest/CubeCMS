<?php
/**
 * Soubor s funkcemi modifikátorů
 */

/**
 * Zkrácení textu s XHTML značkami
 * @param string $s -- zkracovaný řetězec bez komentářů a bloků skriptu
 * @param int $limit -- požadovaný počet vrácených znaků
 * @param string $delimiter -- (option) oddělovač na konci (např. "...")
 * @return string -- zkrácený řetězec se správně uzavřenými značkami
 * @author Jakub Vrána
 * @link http://www.root.cz/clanky/php-zkraceni-textu-s-xhtml-znackami/
 */
function xhtml_cut($s, $limit, $delimiter = '...'){
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
      if($strLen > $limit){
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
   function remove_tag($tagName, $string){
      $string = preg_replace('/<'.$tagName.' [^<>]*>/i', '', $string);
      return $string;
   }


   /**
    * Metoda vytvoří tag obrázku s maximální velikostí obrázku
    * @param string $imagePath -- secta k obrázku
    * @param string $alt -- titulek obrázku
    * @param int $maxWidth -- maximální šířka
    * @param int $maxHeight -- maximální výška
    * @return string -- tag obrázku
    */
   function imageTag($imagePath, $alt = null, $mw = false, $mh = false, $clases = null, $other = null) {
      if(file_exists($imagePath)){
         $imageSizes = getimagesize($imagePath);
         if($imageSizes){
            list($w,$h) = $imageSizes;
            foreach(array('w','h') as $v) { $m = "m{$v}";
               if(${$v} > ${$m} && ${$m}) { $o = ($v == 'w') ? 'h' : 'w';
                  $r = ${$m} / ${$v}; ${$v} = ${$m}; ${$o} = ceil(${$o} * $r); } }

            $class = null;
            if($clases != null) {
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
//               substr($others, 0, strlen($other)-1);
//               $others = $others." ";
            }

            return("<img src=\"{$imagePath}\" alt=\"{$alt}\" width=\"{$w}\" height=\"{$h}\" {$class}{$others}/>");
         }
      }
      return null;
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
   function truncate($text,$numb,$etc = "...") {
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

   


   ?>
