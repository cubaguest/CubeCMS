<?php
/**
 * Soubor s výstupními filtry
 */

/**
 * Funkce pro filtraci kotev na stránce
 * @param <type> $cnt
 * @param Url_Link $linkCurPage
 * @return <type>
 */
function vve_filter_anchors($cnt, Url_Link $linkCurPage) {
   return preg_replace('/href=(["\'])#/i', 'href=\1'.(string)$linkCurPage.'#',$cnt);
}

/**
 * Funkce převede řetězce na emotikony
 * @param string $cnt -- text
 * @return string -- text
 */
function vve_filter_emoticons($cnt) {
   $emoticonsTranslate = array(
      ':-D' => '<img src="images/smiles/face-laugh.png" alt=":-D" />',
      ':-(' => '<img src="images/smiles/face-sad.png" alt=":-(" />',
      ':-/' => '<img src="images/smiles/face-uncertain.png" alt=":-/" />',
      ':-|' => '<img src="images/smiles/face-plain.png" alt=":-|" />',
      ';-)' => '<img src="images/smiles/face-wink.png" alt=";-)" />',
      ':-*' => '<img src="images/smiles/face-kiss.png" alt=":-*" />',
      ':*' => '<img src="images/smiles/face-kiss.png" alt=":*" />',
      'O:-)' => '<img src="images/smiles/face-angel.png" alt="O:-)" />',
      'O:)' => '<img src="images/smiles/face-angel.png" alt="O:)" />',
      '>:)' => '<img src="images/smiles/face-evil.png" alt=">:)" />',
      'D:<' => '<img src="images/smiles/face-angry.png" alt="D:<" />',
      'D:-<' => '<img src="images/smiles/face-angry.png" alt="D:-<" />',
      ':-O' => '<img src="images/smiles/face-surprise.png" alt=":-O" />',
      ':O' => '<img src="images/smiles/face-surprise.png" alt=":O" />',
      ':)' => '<img src="images/smiles/face-smile.png" alt=":)" />',
      ':-)' => '<img src="images/smiles/face-smile.png" alt=":-)" />'
   );
   return strtr($cnt, $emoticonsTranslate);
}

/**
 * Metoda provede korekci typografie na textu (pro češtinu předložky nakonci řádku, mezery atd.)
 * @param string $cnt --
 * @return <type>
 */
function vve_filter_typografy($cnt) {
   switch (Locales::getLang()) {
      case 'cs':
//   		$czechPripositions = "(k|s|v|z|a|i|o|u|ve|ke|ku|za|sze|na|do|od|se|po|pod|před|nad|bez|pro|při|Ing.|Bc.|Arch.|Mgr.)";

      	// překlad předložek na konci řádku
//         $pattern = "/[[:blank:]]{1}".$czechPripositions."[[:blank:]]{1}/";
//   		$replacement = " \\1&nbsp;";
//      	$text = preg_replace($pattern, $replacement, $text);
//
//         $pattern = "/&[a-z]+;".$czechPripositions."[[:blank:]]{1}/";
//         $replacement = "&nbsp;\\1&nbsp;";
//         $text = preg_replace($pattern, $replacement, $text);

         
         $patterns = array(
            "/([0-9])[[:blank:]]{1}(kč|˚C|V|A|W)/",      // zkratky množin
            "/([0-9])[[:blank:]]{1}([0-9]{3})/"        // mezera mezi číslovkami
            );
         $replacements = array(
            "\\1&nbsp;\\2",
            "\\1&nbsp;\\1"
            );
         $cnt = preg_replace($pattern, $replacement, $cnt);
      break;
      default:
         break;
   }
   return $cnt;
}

function vve_filter_protectemails($cnt){
   return $cnt;
}

/**
 * Metoda přidá třídy k souborů ke stažení
 * @param string $cnt -- text
 * @return string
 */
function vve_filter_filesicons($cnt){
   return preg_replace('/(href="[^"]+\.)(pdf|txt|ods|ots|doc|dot|docx|xls|ods|ots|zip|rar|avi|mpg|wmv)"/i','\\1\\2" class="file-icon file-\\2"',$cnt);
}
?>
