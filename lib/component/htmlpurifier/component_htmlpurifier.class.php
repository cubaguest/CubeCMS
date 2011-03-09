<?php
/** 
 * Třída Komponenty pro tvorbu pdf souborů - je postavena na knihovně TCPDF
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty pro tvorbu pdf souborů
 */

class Component_HTMLPurifier extends Component {

   private $purifierConfig = null;


   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false)
   {
      $this->componentName = str_ireplace(__CLASS__.'_', '', get_class($this));
      // jádro
      require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                      .'nonvve'.DIRECTORY_SEPARATOR."htmlpurifier".DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR."HTMLPurifier.auto.php";

      $this->purifierConfig = HTMLPurifier_Config::createDefault();
      $this->purifierConfig->set('HTML.TidyLevel', 'heavy' );
      $this->purifierConfig->set('Cache.SerializerPath', AppCore::getAppCacheDir() );
   }

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init()
   {}

   /**
    * Metoda nastaví proměnou purifieru
    * @return Component_HTMLPurifier
    */
   public function setConfig($name, $value)
   {
      $this->purifierConfig->set($name, $value);
      return $this;
   }

   /**
    * Metoda provede operace nad html (purify z HTMLPurifier)
    * @param string $html -- html řetězec
    * @return string
    */
   public function purify($html)
   {
      $purifier = new HTMLPurifier($this->purifierConfig);
      return $purifier->purify($html);
   }

   /**
    * Metoda provede operace nad html v poli (purify z HTMLPurifier)
    * @param array $htmlArray -- pole s html řetězeci
    * @return array
    */
   public function purifyArray($htmlArray)
   {
      $purifier = new HTMLPurifier($this->purifierConfig);
      return $purifier->purifyArray($htmlArray);
   }

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart()
   {}

   /**
    * Spuštění komponenty (hlavní kontroler)
    */
   public function mainController()
   {}

   /**
    * Metoda provede vykreslení komponenty
    */
   public function mainView()
   {}
}
?>