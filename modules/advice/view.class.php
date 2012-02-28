<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Advice_View extends View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://list.phtml');
      
      if($this->rights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         
         $toolAnswers = new Template_Toolbox2_Tool_PostRedirect('admin_answers', $this->tr("Odpovídat"),
                        $this->link()->route('answers'));
         $toolAnswers->setIcon('comment_edit.png')->setTitle($this->tr('Odpovídat na dotazy'));
         $this->toolbox->addTool($toolAnswers);
         
         $toolStats = new Template_Toolbox2_Tool_PostRedirect('admin_stats', $this->tr("Statistiky"),
                        $this->link()->route('stats'));
         $toolStats->setIcon('table_save.png')->setTitle($this->tr('Statistiky dotazů'));
         $this->toolbox->addTool($toolStats);
         
         if($this->category()->getParam(Advice_Controller::PARAM_ALLOW_DRUGS, false)){
            $name = $this->tr("Kategorie a drogy");
         } else {
            $name = $this->tr("Kategorie");
         }
         
         $toolCats = new Template_Toolbox2_Tool_PostRedirect('admin_quest_cats', $name,
                        $this->link()->route('editCats'));
         $toolCats->setIcon('table_key.png')->setTitle($this->tr('Spravovat kategorie a drogy'));
         $this->toolbox->addTool($toolCats);
      }
   }
   
   public function answersView() 
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://answers.phtml');
   }
   
   public function questionsListView(){
      echo json_encode($this->respond);
   }
   
   public function editQuestionView()
   {
      $this->template()->addFile('tpl://edit_question.phtml');
      $this->setTinyMCE($this->form->question, 'advanced', array('height'=>'300'));
      $this->setTinyMCE($this->form->answer, 'advanced', array('height'=>'300'));
      Template_Module::setEdit(true);
   }
   
   public function addQuestionView()
   {
      $this->template()->addFile('tpl://add_question.phtml');
   }
   
   public function editCatsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://cats_edit.phtml');
   }
   
   public function statsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://stats.phtml');
   }
   
   public function repairTextsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://repair_complete.phtml');
   }
}

?>