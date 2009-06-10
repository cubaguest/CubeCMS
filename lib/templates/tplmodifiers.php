<?php
/**
 * Soubor s funkcemi modifikátorů
 */

/**
 * Zkrácení textu s XHTML značkami
 * @param string $s -- zkracovaný řetězec bez komentářů a bloků skriptu
 * @param int $limit -- požadovaný počet vrácených znaků
 * @return string -- zkrácený řetězec se správně uzavřenými značkami
 * @author Jakub Vrána
 * @link http://www.root.cz/clanky/php-zkraceni-textu-s-xhtml-znackami/
 */
function xhtml_cut($s, $limit){
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


?>
