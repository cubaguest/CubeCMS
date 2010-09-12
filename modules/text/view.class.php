<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Text_View extends View {
   public function mainView() {
      $this->template()->addTplFile("text.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->_('Upravit text'),
                 $this->link()->route('edit'));
         $toolET->setIcon('page_edit.png')->setTitle($this->_("Upravit text"));
         $toolbox->addTool($toolET);

         $modelP = new Model_Panel();
         if($modelP->havePanels($this->category()->getId()) == true){
            $toolEP = new Template_Toolbox2_Tool_PostRedirect('edit_textpanel', $this->_('Upravit text panelu'),
                 $this->link()->route('editpanel'));
            $toolEP->setIcon('page_edit.png')->setTitle($this->_("Upravit text v panel"));
            $toolbox->addTool($toolEP);
         }

         if($this->category()->getParam(Text_Controller::PARAM_ALLOW_PRIVATE, false) == true){
            $toolboxP = new Template_Toolbox2();
            $toolETP = new Template_Toolbox2_Tool_PostRedirect('edit_textprivate', $this->_('Upravit privátní text'),
                 $this->link()->route('editPrivate'));
            $toolETP->setIcon('page_edit.png')->setTitle($this->_("Upravit privátní text"));
            $toolboxP->addTool($toolETP);
            $this->template()->toolboxPrivate = $toolboxP;
         }

         $this->template()->toolbox = $toolbox;
      }
   }
   /*EOF mainView*/

   public function contentView() {
      echo (string)$this->text->{Text_Model_Detail::COLUMN_TEXT};
   }

   public function editView() {
      $this->template()->addTplFile("textedit.phtml");
   }

   public function editPrivateView() {
      $this->template()->addTplFile("textprivateedit.phtml");
   }

   public function editPanelView() {
      $this->template()->addTplFile("textpaneledit.phtml");
   }
   // EOF edittextView
   public function textHtmlView() {
      $model = new Text_Model_Detail();
      $text = $model->getText(Category::getSelectedCategory()->getId());
      if($text != false) {
         $text = $text->{Text_Model_Detail::COLUMN_TEXT};
      } else {
         $text = $this->_("Text nebyl definován, vytvoříte jej v administraci");
      }
      print ($text);
   }
}

?>