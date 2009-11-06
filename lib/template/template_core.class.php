<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem). 
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu šablony
 */

class Template_Core extends Template {

   /**
    * Konstruktor
    */
   function  __construct() {
      parent::__construct(new Url_Link());
   }

   /**
    * Metoda vkládá cestu k css souboru i s názvem souboru
    * @param string $name -- název souboru
    * @return string -- cesta k souboru
    */
   public function style($name) {
      return parent::getFileDir($name, self::STYLESHEETS_DIR).$name;
   }

   /**
    * Magická metoda převede šablonu na řetězec
    * @return string -- vygenerovaný řetězec z šablon
    */
   public function  __toString() {
   // zastavení výpisu buferu
      ob_start();
      foreach ($this->templateFiles as $file) {
         include $file;
      }
      $contents = ob_get_contents();
      ob_end_clean();

      //vytvoříme pole css souborů
      $cssfiles = null;
      foreach (Template::getStylesheets() as $css) {
         $elem = new Html_Element('link');
         $elem->setAttrib('rel', "stylesheet");
         $elem->setAttrib('type', "text/css");
         $elem->setAttrib('href', $css);
         $cssfiles .= $elem;
      }
      $contents = str_replace('{STYLESHEETS}', $cssfiles, $contents);

      //vytvoříme pole javascriptů
      $jscripts = null;
      foreach (Template::getJavascripts() as $js) {
         $elem = new Html_Element('script');
         $elem->setAttrib('src', $js);
         $elem->setAttrib('type', "text/javascript");
         $jscripts .= $elem;
      }
      $contents = str_replace('{JAVASCRIPTS}', $jscripts, $contents);

      // doplníme titulek stránky
      $title = null;
      $arr = array_merge(Template::pVar('CURRENT_CATEGORY_PATH'), self::$pageTitle);
      foreach ($arr as $subtitle) {
         $title .= ' - '.(string)$subtitle;
      }
//      $title = substr($title, 0, strlen($title)-3);
      $contents = str_replace('{PAGE_TITLE}', $title, $contents);

      // doplníme hlavní nadpis stránky
      $headline = null;
      foreach (self::$pageHeadline as $line) {
         $headline .= (string)$line.VVE_HEADLINE_SEPARATOR;
      }
      $headline = substr($headline, 0, strlen($headline)-strlen(VVE_HEADLINE_SEPARATOR));
      $contents = str_replace('{PAGE_HEADLINE}', $headline, $contents);
      
      return (string)$contents;
   }
}
?>