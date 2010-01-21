<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Kontform_View extends View {
   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_mails', $this->_("Upravit e-maily"),
            $this->link()->action($this->sys()->action()->editMails()),
            $this->_("Upravit e-maily"), "text_edit.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("kontform.phtml");
      $this->template()->addCssFile("style.css");
   }
	/*EOF mainView*/

   /**
    * View pro upravu emailu
    */
   public function editMailsView() {
      $this->template()->setActionName($this->_('Uprava emailů'));
      $this->template()->addTplFile("mails.phtml");
      $this->template()->addCssFile("style.css");
      $model = new Kontform_Model_Mails($this->sys());
      $this->template()->mails = $model->getListMails();
   }
}

?>