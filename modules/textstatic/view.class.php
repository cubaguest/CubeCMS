<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TextStatic_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
   }
   
   public function editView() {
      Template_Module::setFullWidth(true);
//      $this->setTinyMCE($this->form->text, $this->category()->getParam(Text_Controller::PARAM_EDITOR_TYPE));
      $this->template()->addFile("tpl://textedit.phtml");
      Template_Navigation::addItem($this->tr('Úprava statických textů'), $this->link(),null,null,null,true);
      
      foreach ($this->form as $key => $item) {
         if($item instanceof Form_Element_TextArea){
            $theme = $this->texts[$key]->tinymce;
            if($theme != 'none'){
               $this->setTinyMCE($item, $this->texts[$key]->tinymce);
            }
         }
      }
   }

}