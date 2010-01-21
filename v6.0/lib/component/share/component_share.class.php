<?php
/** 
 * Třída Komponenty pro práci s odkazy na sdílení odkazu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty pro skrolování po seznamu
 */

class Component_Share extends Component {
   protected $config = array('tpl_file' => 'shares.phtml',
                             'url' => null,
                             'title' => null);

   /**
   private $shareServices = array();

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init(){
   }

   /**
    * Metoda pro spouštění některých akcí přímo v kontroleru
    */
   public function runCtrlPart() {
   }

   /**
    * Spuštění pluginu
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   public function mainController() {
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param integer -- id šablony (jakékoliv)
    */
   public function mainView() {
      $m = new Component_Share_Model();
      $this->template()->shares = $m->getShares();
      
      $this->template()->search = array('{URL}', '{TITLE}');
      $this->template()->replacement = array(rawurlencode($this->getConfig('url')), rawurlencode($this->getConfig('title')));

      $this->template()->addTplFile($this->getConfig('tpl_file'));
   }
}
?>