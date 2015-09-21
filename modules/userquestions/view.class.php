<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class UserQuestions_View extends View {
	public function mainView()
   {
      $this->template()->addFile($this->getTemplate('main'));
      
      // vytvoř toolboxy
      if($this->category()->getRights()->isWritable() && !empty($this->questions)){
         $itemToolbox = new Template_Toolbox2();

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Upravit'));
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
         $itemToolbox->addTool($toolEdit);

         if($this->formApprove){
            $toolApprove = new Template_Toolbox2_Tool_Form($this->formApprove);
            $toolApprove->setIcon(Template_Toolbox2::ICON_ENABLE);
            $itemToolbox->addTool($toolApprove);
         }
         
         if($this->formDelete){
            $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
            $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
            $toolDelete->setImportant(true);
            $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
            $itemToolbox->addTool($toolDelete);
         }
         
         foreach ($this->questions as $question) {
            $question->toolbox = clone $itemToolbox;
            $question->toolbox->edit->setAction($this->link()->route('editQuestion', array('id' => $question->getPK())));
            
            if($this->formApprove && $question->{UserQuestions_Model::COLUMN_APPROVED} == 0){
               $question->toolbox->approvequestion->getForm()->id->setValues($question->getPK());
            } else {
               unset($question->toolbox->approvequestion);
            }
            
            if(isset($itemToolbox->removequestion)){
               $question->toolbox->removequestion->getForm()->id->setValues($question->getPK());
            }
         }
      }
   }
   
   
	public function addQuestionView()
   {
      $this->template()->addFile($this->getTemplate('addQuestion'));
      Template_Navigation::addItem($this->tr('Přidání položky'), $this->link());
   }
   
	public function editQuestionView()
   {
      $this->template()->addFile($this->getTemplate('editQuestion'));
      $this->setTinyMCE($this->form->answer);
      Template_Navigation::addItem($this->tr('Úprava položky'), $this->link());
   }
}
