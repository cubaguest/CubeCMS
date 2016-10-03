<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class FAQ_View extends View {

   public function mainView()
   {
      $this->template()->addFile($this->getTemplate('main'));

      // vytvoř toolboxy
      if ($this->category()->getRights()->isWritable()) {

         if (!empty($this->questions)) {

            $itemToolbox = new Template_Toolbox2();

            $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Upravit'));
            $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
            $itemToolbox->addTool($toolEdit);

            if ($this->formDelete) {
               $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
               $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
               $toolDelete->setImportant(true);
               $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
               $itemToolbox->addTool($toolDelete);
            }

            foreach ($this->questions as $question) {
               $question->toolbox = clone $itemToolbox;
               $question->toolbox->edit->setAction($this->link()->route('editQuestion', array('id' => $question->getPK())));

               if (isset($itemToolbox->removequestion)) {
                  $question->toolbox->removequestion->getForm()->id->setValues($question->getPK());
               }
            }
         }

         // globální toolbox
         $toolbox = new Template_Toolbox2();
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_question', $this->tr("Přidat dotaz"), $this->link()->route('addQuestion'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat nový dotaz'));
         $toolbox->addTool($toolAdd);

         $this->toolbox = $toolbox;
      }
   }

   public function addQuestionView()
   {
      $this->template()->addFile($this->getTemplate('editQuestion'));
      $this->setTinyMCE($this->form->question);
      $this->setTinyMCE($this->form->answer);
      Template_Navigation::addItem($this->tr('Přidání položky'), $this->link());
      Template_Module::setEdit(true);
   }

   public function editQuestionView()
   {
      $this->template()->addFile($this->getTemplate('editQuestion'));
      $this->setTinyMCE($this->form->question);
      $this->setTinyMCE($this->form->answer);
      Template_Navigation::addItem($this->tr('Úprava položky'), $this->link());
      Template_Module::setEdit(true);
   }

}
