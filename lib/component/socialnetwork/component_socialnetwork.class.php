<?php
/** 
 * Třída Komponenty přístup k sociálním sítím
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ VVE 7.9 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty přístup k sociálním sítím
 */

class Component_SocialNetwork extends Component {
   protected $publishToNetworks = arraY();

   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false) 
   {
      parent::__construct(true); // nemá žádný vystup přes url adresy
      $this->detectAllowedNewtworks();
   }
   
   protected function detectAllowedNewtworks()
   {
      if(VVE_FCB_ACCESS_TOKEN != null && VVE_FCB_APP_ID != null && VVE_FCB_APP_SECRET_KEY != null){
         $this->publishToNetworks = array('facebook');
      }
   }

   public function getNetworks()
   {
      return $this->publishToNetworks;
   }
   
   public function setNetworks($networksArray)
   {
      $this->publishToNetworks = $networksArray;
   }

   protected function isValidNetwork($name)
   {
      return in_array($name, $this->publishToNetworks);
   }
   
   public function isPublishAvailable()
   {
      return !empty($this->publishToNetworks);
   }

   /* UNUSED */
   
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