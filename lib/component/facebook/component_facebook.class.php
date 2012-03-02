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

class Component_Facebook extends Component {
   protected $config = array(
           'appId' => null,
           'secret' => null,
           'targetpage' => null
      );

   /**
    * Objek TCPDF
    * @var TCPDF
    */
   private $facebookObj = null;

   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false) 
   {
      parent::__construct(true); // nemá žádný vystup přes url adresy
      // jazykové nastavení
      require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                      .'nonvve'.DIRECTORY_SEPARATOR."facebook".DIRECTORY_SEPARATOR."facebook.php";
   }

   /**
    * Metoda vrací facebook objekt
    * @return Facebook -- objekt facebook api
    */
   public function fcb() 
   {
      if($this->facebookObj === null) {
         $this->facebookObj = new Facebook($this->config);
      }
      return $this->facebookObj;
   }

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init() {}

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {}

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {}

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {}
}
?>