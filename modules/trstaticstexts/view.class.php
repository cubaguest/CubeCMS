<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TrStaticsTexts_View extends View
{
   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
   }

   public function translateModuleView()
   {
      $this->template()->addFile('tpl://translator.phtml');
   }

   public function translateLibsView()
   {
      $this->template()->addFile('tpl://translator.phtml');
   }
}

?>