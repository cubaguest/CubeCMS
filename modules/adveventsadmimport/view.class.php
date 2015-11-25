<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class AdvEventsAdmImport_View extends AdvEventsBase_View {

   public function mainView()
   {
      parent::mainView();
      $this->template()->addFile('tpl://main.phtml');

      $toolboxSource = new Template_Toolbox2();
      $toolboxSource->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);

      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Úprava zdroje'));
      $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolboxSource->addTool($toolEdit);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formRemove);
      $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolRemove->setImportant(true);
      $toolRemove->setConfirmMeassage($this->tr('Opravdu smazat zdroj?'));
      $toolboxSource->addTool($toolRemove);

      foreach ($this->advEventSources as $source) {
         $toolboxSource->edit->setAction($this->link()->clear()->route('editSource', array('id' => $source->getPK())));
         $toolboxSource->advEventSourceDelete->getForm()->setAction($this->link()->clear()->route());
         $toolboxSource->advEventSourceDelete->getForm()->id->setValues($source->getPK());
         $source->toolbox = clone $toolboxSource;
      }
   }

   public function addSourceView()
   {
      Template::setFullWidth(true);
      $this->template()->addFile('tpl://edit.phtml');
   }
   
   public function editSourceView()
   {
      Template::setFullWidth(true);
      Template_Navigation::addItem($this->tr('Úprava zdroje'), $this->link());
      $this->template()->addFile('tpl://edit.phtml');
   }
   
}
