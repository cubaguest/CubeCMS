<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class AdminEnviroment_View extends View {
	public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
      
     
      
   }
}
