<?php

/**
 * Třída s posrfiltry, aplikovanými na proměnné v šabloně. Měla by být implementována
 * přímo do renderu šablon
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída s postfiltry
 */
class Template_Postfilters {

   private static $emoticonsTranslate = array(
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

   /**
    * Metoda převede předložky a některé znaky s normální mezerou na nezalomitelné mezery
    *
    * @param string -- zadaný text
    * @return string -- text. u kterého jsou převedeny předložky
    */
   public static function czechTypo($text)
   {
      $czechPripositions = "(k|s|v|z|a|i|o|u|ve|ke|ku|za|ze|na|do|od|se|po|pod|před|nad|bez|pro|při|Ing.|Bc.|Arch.|Mgr.)";

      // překlad předložek na konci řádku
      $pattern = "/[[:blank:]]{1}" . $czechPripositions . "[[:blank:]]{1}/";
      $replacement = " \\1&nbsp;";
      $text = preg_replace($pattern, $replacement, $text);

//		$pattern = "/&(?![lt])[a-z]+;".$czechPripositions."[[:blank:]]{1}/";
//		$replacement = "&nbsp;\\1&nbsp;";
//		$text = preg_replace($pattern, $replacement, $text);
      //	zkratky množin
      $pattern = "/([0-9])[[:blank:]]{1}(kč|˚C|V|A|W)/";
      $replacement = "\\1&nbsp;\\2";
      $text = preg_replace($pattern, $replacement, $text);

      //mezera mezi číslovkami
      $pattern = "/([0-9])([[:blank:]]{1})([0-9]{3})/";
      $replacement = "\\1&nbsp;\\3";
      $text = preg_replace($pattern, $replacement, $text);
      return $text;
   }

   /**
    * Metoda převede předložky a některé znaky s normální mezerou na nezalomitelné mezery
    *
    * @param string -- zadaný text
    * @return string -- text. u kterého jsou převedeny předložky
    */
   public static function typo($text, $link = null, $tplobj = null, $lang = null)
   {
      if (!$lang) {
         $lang = Locales::getLang();
      }
      switch ($lang) {
         case 'cs':
            $czechPripositions = "(k|s|v|z|a|i|o|u|ve|ke|ku|za|ze|na|do|od|se|po|pod|před|nad|bez|pro|při|Ing.|Bc.|Arch.|Mgr.)";

            // překlad předložek na konci řádku
//            $pattern = "/[[:blank:]]{1}".$czechPripositions."[[:blank:]]{1}/";
//            $replacement = " \\1&nbsp;";
//            $text = preg_replace($pattern, $replacement, $text);
//            $pattern = "/&(?![lt]|[gt])[a-z]+;".$czechPripositions."[[:blank:]]{1}/";
//            $replacement = "&nbsp;\\1&nbsp;";
//            $text = preg_replace($pattern, $replacement, $text);
            //	zkratky množin
            $pattern = "/([0-9])[[:blank:]]{1}(kč|˚C|V|A|W)/";
            $replacement = "\\1&nbsp;\\2";
            $text = preg_replace($pattern, $replacement, $text);

            //mezera mezi číslovkami
            $pattern = "/([0-9])([[:blank:]]{1})([0-9]{3})/";
            $replacement = "\\1&nbsp;\\3";
            $text = preg_replace($pattern, $replacement, $text);

            break;
      }
      return $text;
   }

   /**
    * Metoda převede emotikony v řetězci na obrázky
    * @param string $string -- řetězec
    * @return string
    */
   public static function emoticons($string)
   {
      $string = strtr($string, self::$emoticonsTranslate);
      return $string;
   }

   public static function modulesContentFilter($string)
   {

      $string = preg_replace_callback('/(?:<(?:span|p)[^>]*>)*\{([A-Z]+):(?:([A-Z]+):)?([^}]+)\}(?:<\/(?:span|p)>)*/', function($matches) {
         $return = '';
         $viewClassName = ucfirst(strtolower($matches[1])) . '_View';
         $method = 'contentFilter';
         if (isset($matches[3])) {
            $method = 'contentFilter' . ucfirst(strtolower($matches[2]));
         }

         $catObj = Model_Category::getNewRecord();
         $catObj->{Model_Category::COLUMN_MODULE} = strtolower($matches[1]);
         $cat = new Category_Core(null, false, $catObj);
         if (method_exists($viewClassName, $method)) {
            $params = explode(':', $matches[3]);
            
            foreach ($params as $key => $p) {
               if(strpos($p, '=') !== false){
                  unset($params[$key]);
                  $parts = explode('=', $p);
                  $params[strtolower($parts[0])] = $parts[1];
               }
            }
            
            /* @var $view CustomBlocks_View */
            $view = new $viewClassName(new Url_Link_Module(), $cat);
            $view->category()->getModule()->loadTemplates();
            $view->$method($params);
            $return = (string) $view->template();
         } else if (method_exists($viewClassName, 'contentFilter')) {
            $params = explode(':', isset($matches[3]) ? $matches[2] . ':' . $matches[3] : $matches[2]);
            
            foreach ($params as $key => $p) {
               if(strpos($p, '=') !== false){
                  unset($params[$key]);
                  $parts = explode('=', $p);
                  $params[strtolower($parts[0])] = $parts[1];
               }
            }
            
            $view = new $viewClassName(new Url_Link_Module(), $cat);
            $view->category()->getModule()->loadTemplates();
            $view->contentFilter($params);
            $return = (string) $view->template();
         }
         return $return;
      }, $string);

      return $string;
   }
   
   /**
    * Filtrace dynamických odkazů
    * @param string $string
    * @return string
    */
   public static function webLinksContentFilter($string)
   {
      return preg_replace_callback('/\{CATLINK-(\d+)\}/i', function($matches){
         return Url_Link::getCategoryLink((int)$matches[1]);
      }, $string);
   }

}
