<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class KzMainPage_View extends View {
   public function mainView() {
      $this->template()->addTplFile("info.phtml");
   }

   public function edititemsView(){
      $this->template()->addTplFile("edititems.phtml");
   }

   public function loadArticlesView() {
      $cnt = null;
      foreach ($this->articles as $key => $art) {
         if(is_array($art)){
            $optGrp = new Html_Element('optgroup');
            $optGrp->setAttrib('label', $key);
            foreach ($art as $key2 => $value) {
               $option = new Html_Element('option', $key2);
               $option->setAttrib('value', $value);
               $optGrp->addContent($option);
            }
            $cnt .= $optGrp;
         } else {
            $option = new Html_Element('option', $key);
            $option->setAttrib('value', $art);
            $cnt .= $option;
         }
      }
      print ($cnt);
   }
}

?>