<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class AdvEventsAdm_View extends AdvEventsBase_View {
	public function mainView()
   {
      parent::mainView();
      $this->template()->addFile('tpl://main.phtml');
      Template_Core::setFullWidth(true);

      // toolbo itemu
      $toolbox = new Template_Toolbox2();
      $toolbox->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);

      $info = new Template_Toolbox2_Tool_Button('info', $this->tr('Informace'));
      $info->setIcon('info');
      $toolbox->addTool($info);

      $copy = new Template_Toolbox2_Tool_Button('copy', $this->tr('Kopírovat'));
      $copy->setIcon('copy');
      $toolbox->addTool($copy);

      $changeState = new Template_Toolbox2_Tool_Form($this->formChangeState);
      $changeState->setIcon('check-square-o');
      $toolbox->addTool($changeState);

      $changeRecommended = new Template_Toolbox2_Tool_Form($this->formChangeRecommended);
      $changeRecommended->setIcon('star');
      $toolbox->addTool($changeRecommended);

      $edit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Upravit'));
      $edit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolbox->addTool($edit);

      $delete = new Template_Toolbox2_Tool_Form($this->formDelete);
      $delete->setConfirmMeassage($this->tr('Opravdu smazat událost?'));
      $delete->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolbox->addTool($delete);

      $this->template()->toolboxItem = $toolbox;
   }

   public function addEventView()
   {
      $this->template()->addFile('tpl://edit.phtml');
      Template_Core::setFullWidth(true);
      $this->setTinyMCE($this->form->desc, 'advanced');
   }

   public function editEventView()
   {
      $this->template()->addFile('tpl://edit.phtml');
      Template_Core::setFullWidth(true);
      $this->setTinyMCE($this->form->desc, 'advanced');
   }

   public function eventView()
   {
      $this->template()->addFile('tpl://event.phtml');
   }
}
